<?php 

namespace App\Controller;

use DateTime;
use App\Entity\Cours;
use App\Entity\Degre;
use App\Entity\Ecole;
use App\Entity\Eleve;
use App\Entity\Classe;
use App\Entity\Groups;
use App\Form\GroupType;
use App\Entity\Periodes;
use App\Entity\Presences;
use App\Entity\Evaluation;
use App\Form\AddGroupType;
use App\Form\NewCoursType;
use App\Entity\Competences;
use App\Entity\CoursGroupe;
use App\Form\GroupPeriodeType;
use App\Entity\EvaluationGroup;
use App\Form\NewEvaluationType;
use App\Entity\CustomizedPresences;
use App\Form\AddNewEvaluationsType;
use App\Form\NewEvaluationCoursType;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class groupsController extends AbstractController {
    
    /**
     * @Route("/groups", name="groups")
     * @Security("is_granted('ROLE_USER')")
     */
    public function groups(UserInterface $user){
        $manager = $this->getDoctrine()->getManager();

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('ecole', 'ecole')
        ->addScalarResult('groups_id', 'groups_id')
        ->addScalarResult('groupes', 'groupes')
        ->addScalarResult('nombreEleves', 'nombreEleves');

        $groupsSql = "select ecole.nom_ecole as ecole, groups_id, GROUP_CONCAT(nom_classe SEPARATOR '/') as groupes, count(eleve.id) as nombreEleves 
        from classe
        join ecole on classe.ecole_id = ecole.id
        join eleve on classe.id = eleve.classe_id
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
     * @Route("/newGroup/{ecole}", name="new_group")
     * @Security("is_granted('ROLE_USER')")
     */
    public function newGroup(Ecole $ecole, Request $request, UserInterface $user){
        $manager = $this->getDoctrine()->getManager();
        $group = new Groups();

        $classes = $manager->getRepository(Classe::class)
        ->findByecole($ecole->getId());

        $degre = $manager
        ->getRepository(Degre::class)
        ->findAll();

        $form = $this->createForm(AddGroupType::class, $group, 
            [
                'classes' => $classes, 
                'degre' => $degre
            ]
        );

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){          
            foreach($group->getClasses() as $classes){
                
                $classe = $manager->getRepository(Classe::class)
                ->findOneById($classes->getId());

                $classe->setGroups($group);
                $group->setProfesseur($user);

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
     * @Route("/group/{group}", name="group")
     * @Security("is_granted('ROLE_USER') and user === group.getProfesseur()")
     */
    public function group(Groups $group){
        $manager = $this->getDoctrine()->getManager();
        $classes = $manager
        ->getRepository(Classe::class)
        ->findByGroups($group);
        
        $eleves = $manager
        ->getRepository(Eleve::class)
        ->findByclasse($classes);

        return $this->render(
            'groupes/group.html.twig',[
                'group' => $eleves,
                'ecole' => $classes
            ]
        );
    }

    
    /**
     * @Route("/newCours/{group}", name="new_cours")
     * @Security("is_granted('ROLE_USER') and user === group.getProfesseur()")
     */
    public function newCours(Groups $group, Request $request, UserInterface $user){
        $cours = new Cours();

        $periodes = $this->getDoctrine()
        ->getRepository(Periodes::class)
        ->findBygroupe($group->getId());

        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(NewCoursType::class, $cours, ['periodes' => $periodes]);

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('ecole', 'ecole');
        $rsm->addScalarResult('groups_id', 'groups_id');
        $rsm->addScalarResult('groupes', 'groupes');

        $groupSql = "select ecole.nom_ecole as ecole, groups_id, GROUP_CONCAT(nom_classe SEPARATOR '/') as groupes 
        from classe
        join ecole on classe.ecole_id = ecole.id
        where groups_id = ?";
        
        $getGroup = $manager->createNativeQuery($groupSql, $rsm);
        $getGroup->setParameter(1, $group->getId());
        $groupe = $getGroup->getResult();

        $presencesCustomized = $manager
        ->getRepository(CustomizedPresences::class)
        ->findOneBy(
            [
                'user' => $user->getId(),
                'typePresence' => 1
            ]
        );
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $getPresence = $this->getDoctrine()
            ->getRepository(Presences::class)
            ->findOneById(1);

            $cours
            ->setDateCours(new \DateTime())
            ->setGroupe($group);

            $manager->persist($cours);
            $manager->flush();

            $classes = $manager
            ->getRepository(Classe::class)
            ->findByGroups($group->getId());

            $cours = $manager
            ->getRepository(Cours::class)
            ->findOneById($cours->getId());
            
            $eleve = $manager
            ->getRepository(Eleve::class)
            ->findByclasse($classes);

            if(!empty($presencesCustomized)){
                foreach($eleve as $key => $value){
                    $coursGroupe = new CoursGroupe();
                    $coursGroupe
                    ->setCoursId($cours)
                    ->setEleveId($value)
                    ->setPresences($getPresence)
                    ->setCustomizedPresences($presencesCustomized);

                    $manager->persist($coursGroupe);
                }
            }
            else {
                foreach($eleve as $key => $value){
                    $coursGroupe = new CoursGroupe();
                    $coursGroupe
                    ->setCoursId($cours)
                    ->setEleveId($value)
                    ->setPresences($getPresence);

                    $manager->persist($coursGroupe);
                }
                
            }
            $manager->flush();

            $this->forward('App\Controller\cahierCoteController::getMoyenneCours', [
                'idGroup' =>  $group->getId()
            ]);
            
            return $this->redirectToRoute('journal_de_classe', 
                [
                    'group' => $group->getId()
                ]
            );
        }

        return $this->render(
            'groupes/newCours.html.twig', 
            [
                'form' => $form->createView(),
                'group' => $groupe
            ]
        );
    }

    /**
     * @Route("/newEvaluation/{group}", name="new_evaluation")
     * @Security("is_granted('ROLE_USER') and user === group.getProfesseur()")
     */
    public function newEvaluation(Groups $group, Request $request){
        $manager = $this->getDoctrine()->getManager();
        $evaluation = new Evaluation();

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('ecole', 'ecole');
        $rsm->addScalarResult('groups_id', 'groups_id');
        $rsm->addScalarResult('groupes', 'groupes');

        $groupSql = "select ecole.nom_ecole as ecole, groups_id, GROUP_CONCAT(nom_classe SEPARATOR '/') as groupes 
        from classe
        join ecole on classe.ecole_id = ecole.id
        where groups_id = ?";

        $getGroup = $manager->createNativeQuery($groupSql, $rsm);
        $getGroup->setParameter(1, $group->getId());
        $groupe = $getGroup->getResult();


        $getPeriodes = $manager
        ->getRepository(Periodes::class)
        ->findBygroupe($group->getId());

        $getCompetences = $manager
        ->getRepository(Competences::class)
        ->findBydegre($group->getDegre()->getId());

        $form = $this->createForm(AddNewEvaluationsType::class, $evaluation,
            [
                'periodes' => $getPeriodes,
                'competences' => $getCompetences
            ]
        );


        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $classes = $manager
            ->getRepository(Classe::class)
            ->findByGroups($group->getId());

            $eleve = $manager
            ->getRepository(Eleve::class)
            ->findByclasse($classes);

            $data = $form->getData();
            
            foreach($evaluation->getEvaluations() as $evaluations){
                $evaluations
                ->setDateEvaluation($data->getDateEvaluation())
                ->setGroupe($group)
                ->setPeriode($data->getPeriode());

                foreach($eleve as $key => $value){
                    $evaluationGroup = new EvaluationGroup();
                    $evaluationGroup->setEleve($value)
                    ->setEvaluation($evaluations)
                    ->setPoints("0");
                    $evaluations->addEvaluationGroup($evaluationGroup);
                }
                $manager->persist($evaluations);
            }

            $manager->flush();

            $this->forward('App\Controller\cahierCoteController::getMoyenneEvaluation', 
            [
                'idGroup' => $group->getId()
            ]);

            $this->forward('App\Controller\cahierCoteController::getMoyenneCompetence', 
            [
                'idGroup' => $group->getId()
            ]);
            
            return $this->redirectToRoute('journal_de_classe', ['group' => $group->getId() ]);
        }

        return $this->render(
            'groupes/newEvaluation.html.twig',
            [
                'group' => $groupe,
                'form' => $form->createView()
            ]
        );
    }

}