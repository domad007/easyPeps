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
     * Choix du groupe auquel on veut accèder
     * @Route("/groups", name="groups")
     * @Security("is_granted('ROLE_ACTIF')", statusCode=405)
     */
    public function groups(UserInterface $user){
        $manager = $this->getDoctrine()->getManager();

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('ecole', 'ecole')
        ->addScalarResult('groups_id', 'groups_id')
        ->addScalarResult('groupes', 'groupes')
        ->addScalarResult('nombreEleves', 'nombreEleves');

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
     * Création d'un nouveau groupe
     * @Route("/newGroup/{ecole}", name="new_group")
     * @Security("is_granted('ROLE_ACTIF')", statusCode=405)
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
     * Affichage des élèves appartenant au groupe
     * @Route("/group/{group}", name="group")
     * @Security("is_granted('ROLE_ACTIF') and user === group.getProfesseur()", statusCode=405)
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
     * Création d'une nouvelle periode pour ce groupe
     * @Route("/newPeriode/{group}", name="new_periode")
     * @Security("is_granted('ROLE_ACTIF') and user === group.getProfesseur()", statusCode=405)
     */
    public function newPeriode(Groups $group, Request $request){
        $manager = $this->getDoctrine()->getManager();
        $groups = new Groups();
        
        $form = $this->createForm(GroupPeriodeType::class, $groups);
        $form->handleRequest($request);        

        if($form->isSubmitted() && $form->isValid()){
            dump($groups);
            foreach($groups->getPeriodes() as $periodes){         
                $periodes
                ->setGroupe($group);
                $manager->persist($periodes);

            }
            $manager->flush();
            return $this->redirectToRoute('journal_de_classe', ['group' =>  $group->getId()]);
        }

        return $this->render(
            'groupes/newPeriode.html.twig', 
            [
                'form' => $form->createView(),
                'calculAuto' => $group
            ]
        );
    }

    
    /**
     * Création d'un nouveau cours pour le groupe
     * @Route("/newCours/{group}", name="new_cours")
     * @Security("is_granted('ROLE_ACTIF') and user === group.getProfesseur()", statusCode=405)
     */
    public function newCours(Groups $group, Request $request){
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
                'user' => $group->getProfesseur()->getId(),
                'typePresence' => 1
            ]
        );
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $getPresence = $this->getDoctrine()
            ->getRepository(Presences::class)
            ->findOneById(1);
            if($cours->getPeriode() == null){
                foreach($periodes as $key => $value){
                    if(new \DateTime() >= $value->getDateDebut() && new \DateTime() <= $value->getDateFin()){
                        $cours->setPeriode($value);
                    }
                }    
            }
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
     * Création d'une nouvelle évaluation pour le groupe
     * @Route("/newEvaluation/{group}", name="new_evaluation")
     * @Security("is_granted('ROLE_ACTIF') and user === group.getProfesseur()", statusCode=405)
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
                if($data->getPeriode() == null){
                    foreach($getPeriodes as $key => $value){
                        if($data->getDateEvaluation() >= $value->getDateDebut() && $data->getDateEvaluation() <= $value->getDateFin()){
                            $evaluations
                            ->setPeriode($value);
                        }
                    }
                }
                else {
                    $evaluations
                    ->setPeriode($data->getPeriode());
                }

                $evaluations
                    ->setDateEvaluation($data->getDateEvaluation())
                    ->setGroupe($group);

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