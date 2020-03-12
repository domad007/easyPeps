<?php 

namespace App\Controller;

use DateTime;
use App\Entity\Cours;
use App\Entity\Ecole;
use App\Entity\Eleve;
use App\Entity\Classe;
use App\Entity\Groups;
use App\Form\GroupType;
use App\Form\AddGroupType;
use App\Form\NewCoursType;
use App\Entity\CoursGroupe;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class groupsController extends AbstractController {
    
    /**
     * @Route("/groups", name="groups")
     */
    public function groups(UserInterface $user){
        $manager = $this->getDoctrine()->getManager();

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('ecole', 'ecole');
        $rsm->addScalarResult('groups_id', 'groups_id');
        $rsm->addScalarResult('groupes', 'groupes');

        $groupsSql = "select ecole.nom_ecole as ecole, groups_id, GROUP_CONCAT(nom_classe SEPARATOR '/') as groupes 
        from classe
        join ecole on classe.ecole_id = ecole.id
        where groups_id is not null and professeur_id = ?
        group by groups_id";

        $getGroups = $manager->createNativeQuery($groupsSql, $rsm);
        $getGroups->setParameter(1, $user->getId());

        $groups = $getGroups->getResult();

        $ecoles = $manager->createQueryBuilder();
        $ecoles
        ->select('ecole')
        ->from('App:Ecole', 'ecole')
        ->join('App:Classe', 'classes', 'WITH', 'ecole.id = classes.ecole')
        ->where('classes.professeur = :idProfesseur')
        ->setParameter('idProfesseur', $user->getId());
        $resultEcoles = $ecoles->getQuery()->getResult();

        return $this->render(
            'groupes/groups.html.twig', [
                'ecoles' => $resultEcoles,
                'groups' => $groups
            ]
        );
    }

    /**
     * @Route("/newGroup/{idEcole}", name="new_group")
     */
    public function newGroup(Request $request, $idEcole){
        $manager = $this->getDoctrine()->getManager();
        $group = new Groups();

        $ecole = $manager->getRepository(Ecole::class)
        ->findOneById($idEcole);

        $classes = $manager->getRepository(Classe::class)
        ->findBy([
           'ecole' =>  $ecole->getId()
        ]);
        
        $form = $this->createForm(AddGroupType::class, $group, ['classes' => $classes]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){           
            foreach($group->getClasses() as $classes){
                $classe = $manager->getRepository(Classe::class)
                ->findOneById($classes->getId());

                $classe->setGroups($group);

                $manager->persist($group);
                $manager->flush();
            }

            $this->addFlash('success', "Le groupe a bien été rajouté");
            return $this->redirectToRoute('groups');
        }

        return $this->render(
            'groupes/newGroup.html.twig', [
                'ecole'=> $ecole,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/group/{idGroup}", name="group")
     */

    public function group($idGroup){
        $manager = $this->getDoctrine()->getManager();
        $group = $manager
        ->getRepository(Classe::class)
        ->findByGroups($idGroup);

        foreach($group as $key => $value){           
            $eleves [] = $manager
            ->getRepository(Eleve::class)
            ->findBy(
                [
                    'classe' => $value->getId()
                ]
            );
        }

        return $this->render(
            'groupes/group.html.twig',[
                'group' => $eleves,
                'ecole' => $group
            ]
        );
    }

    /**
     * @Route("/newCours/{idGroupe}", name="new_cours")
     */
    public function newCours(Request $request, $idGroupe){
        $cours = new Cours();

        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(NewCoursType::class, $cours);

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('ecole', 'ecole');
        $rsm->addScalarResult('groups_id', 'groups_id');
        $rsm->addScalarResult('groupes', 'groupes');

        $groupSql = "select ecole.nom_ecole as ecole, groups_id, GROUP_CONCAT(nom_classe SEPARATOR '/') as groupes 
        from classe
        join ecole on classe.ecole_id = ecole.id
        where groups_id = ?";
        
        $getGroup = $manager->createNativeQuery($groupSql, $rsm);
        $getGroup->setParameter(1, $idGroupe);
        $group = $getGroup->getResult();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $idGroupe = $manager
            ->getRepository(Groups::class)
            ->find($idGroupe);

            $cours
            ->setDateCours(new \DateTime())
            ->setGroupe($idGroupe);

            $manager->persist($cours);
            $manager->flush();

            $group = $manager
            ->getRepository(Classe::class)
            ->findByGroups($idGroupe);

            $cours = $manager
            ->getRepository(Cours::class)
            ->findOneById($cours->getId());
            
            foreach($group as $key => $value){           
                $eleve [] = $manager
                ->getRepository(Eleve::class)
                ->findOneBy(
                    [
                        'classe' => $value->getId()
                    ]
                );
            }

            foreach($eleve as $key => $value){
                $coursGroupe = new CoursGroupe();
                $coursGroupe
                ->setCoursId($cours)
                ->setEleveId($value)
                ->setPoints("0")
                ->setPresence("present");
                $manager->persist($coursGroupe);
            }
            
            $manager->flush();
            
            return $this->redirectToRoute('journal_de_classe');
        }
        return $this->render(
            'groupes/newCours.html.twig', 
            [
                'form' => $form->createView(),
                'group' => $group
            ]
        );
    }

    /**
     * @Route("/newEvaluation/{idGroupe}", name="new_evaluation")
     */
    public function newEvaluation($idGroupe){
        $manager = $this->getDoctrine()->getManager();
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('ecole', 'ecole');
        $rsm->addScalarResult('groups_id', 'groups_id');
        $rsm->addScalarResult('groupes', 'groupes');

        $groupSql = "select ecole.nom_ecole as ecole, groups_id, GROUP_CONCAT(nom_classe SEPARATOR '/') as groupes 
        from classe
        join ecole on classe.ecole_id = ecole.id
        where groups_id = ?";

        $getGroup = $manager->createNativeQuery($groupSql, $rsm);
        $getGroup->setParameter(1, $idGroupe);
        $group = $getGroup->getResult();

        return $this->render(
            'groupes/newEvaluation.html.twig',
            [
                'group' => $group
            ]
        );
    }

}