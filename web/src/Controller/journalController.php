<?php

namespace App\Controller;

use DateTime;
use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\Classe;
use App\Entity\Groups;
use App\Entity\Periodes;
use App\Entity\Presences;
use App\Entity\Evaluation;
use App\Entity\Competences;
use App\Entity\CoursGroupe;
use App\Entity\EvaluationGroup;
use App\Entity\CustomizedPresences;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class journalController extends AbstractController {

    /**
     * Choix du groupe pour lequel on doit afficher le journal de classe
     * @Route("/journalDeClasse", name="journal")
     *  @Security("is_granted('ROLE_ACTIF')", statusCode=405)
     */
    public function journal(UserInterface $user){

        $manager = $this->getDoctrine()->getManager();
        $rsm = new ResultSetMapping();
        $rsm
        ->addScalarResult('ecole', 'ecole')
        ->addScalarResult('groups_id', 'groups_id')
        ->addScalarResult('groupes', 'groupes');

        $groupsSql = "select ecole.nom_ecole as ecole, groups_id, GROUP_CONCAT(nom_classe SEPARATOR '/') as groupes 
        from classe
        join ecole on classe.ecole_id = ecole.id
        where groups_id is not null and professeur_id = ?
        group by groups_id";

        $getGroups = $manager->createNativeQuery($groupsSql, $rsm);
        $getGroups->setParameter(1, $user->getId());

        $groups = $getGroups->getResult();

        return $this->render(
            'journalDeClasse/journalChoixGroupe.html.twig', 
            [
                'groups' => $groups
            ]
        );
    }
    
    /**
     * Affichage du journal de classe pour le groupe
     * @Route("/journalDeClasse/{group}", name="journal_de_classe")
     * @Security("is_granted('ROLE_ACTIF') and user === group.getProfesseur()", statusCode=405)
     */
    public function journalDeCalsse(Groups $group){
        $manager = $this->getDoctrine()->getManager();
        $getCours = $manager
        ->getRepository(Cours::class)
        ->findBygroupe($group->getId());

        $getPeriodes = $this->getDoctrine()
        ->getRepository(Periodes::class)
        ->findBygroupe($group->getId());

        $classes = $manager
        ->getRepository(Classe::class)
        ->findByGroups($group->getId());

        $getCoursGroupe = $manager
        ->getRepository(CoursGroupe::class)
        ->findBycoursId($getCours);

        $rsm = new ResultSetMapping();
        $rsm
        ->addScalarResult('ecole', 'ecole')
        ->addScalarResult('groups_id', 'groups_id')
        ->addScalarResult('groupes', 'groupes');

        $groupsSql = "select ecole.nom_ecole as ecole, groups_id, GROUP_CONCAT(nom_classe SEPARATOR '/') as groupes 
        from classe
        join ecole on classe.ecole_id = ecole.id
        where groups_id is not null and professeur_id = ?
        group by groups_id";

        $getGroups = $manager->createNativeQuery($groupsSql, $rsm);
        $getGroups->setParameter(1, $group->getProfesseur());

        $groups = $getGroups->getResult();

        $getPresences = $this->getDoctrine()
        ->getRepository(Presences::class)
        ->findAll();


        $getCompetences = $this->getDoctrine()
        ->getRepository(Competences::class)
        ->findBydegre($classes[0]->getGroups()->getDegre()->getId());
        
        $getEvaluations = $this->getDoctrine()
        ->getRepository(Evaluation::class)
        ->findBygroupe($group->getId());
        
        $getEvaluationsGroupe = $this->getDoctrine()
        ->getRepository(EvaluationGroup::class)
        ->findById($getEvaluations);

        $getPresencesCustomized = $manager
        ->getRepository(CustomizedPresences::class)
        ->findBy(
            [
                'user' => $group->getProfesseur()->getId()
            ]
        );

        $eleves = $manager
        ->getRepository(Eleve::class)
        ->findByclasse($classes);



        foreach($eleves as $key => $value){
            foreach($getCoursGroupe as $key => $val){
                if($val->getEleveId()->getId() == $value->getId()){
                    $value->addCoursGroupe($val);                      
                }
            }
            foreach($getEvaluationsGroupe as $key => $val){
                if($val->getEleve()->getId() == $value->getId()){
                    $value->addEvaluationGroup($val);
                }
            }
        }
    
        return $this->render(
            'journalDeClasse/journal.html.twig', 
            [
                'groups' => $groups,
                'ecole' => $classes,
                'eleves' => $eleves,   
                'periodes' => $getPeriodes,
                'presences' => $getPresences,
                'presencesCustomized' => $getPresencesCustomized,
                'competences' => $getCompetences,
            ]
        );
    }

    /**
     * Modification des points pour le cours donné dans le journal de classe
     * @Route("/modifPointsCours", name="modif_points_cours")
     */
    public function modifPoints(Request $request){
        $manager = $this->getDoctrine()->getManager();
        $cours = new CoursGroupe();

        if($request->isMethod('post')){
            $infos =  $request->request->all();

            $getCoursGroupe = $manager
            ->getRepository(CoursGroupe::class)
            ->findOneBy(
                [
                    'coursId' => $infos['pk'],
                    'eleveId' => $infos['name'],
                ]
            );

            $getCoursGroupe->setPoints($infos['value']);
            $manager->persist($getCoursGroupe);
            $manager->flush();

            
        }
        return new Response("");
    }

    /**
     * Gestion des présences dans le journal de classe
     * @Route("/presences", name="presence_eleve")
     */
    public function presenceEleve(Request $request){
        $manager = $this->getDoctrine()->getManager();
        if($request->isMethod('post')){
            $presence = $request->request->all();
            $values = explode(",", $presence['presence']);
            
            $typePresence = $this->getDoctrine()
            ->getRepository(Presences::class)
            ->findOneById($values[2]);

            $presenceEleve = $this->getDoctrine()
            ->getRepository(CoursGroupe::class)
            ->findOneBy(
                [
                    'coursId' => $values[0],
                    'eleveId' => $values[1]
                ]
            );

            $presenceEleve->setPresences($typePresence);
            $manager->persist($presenceEleve);
            $manager->flush();
        }    
        return new Response("");
    }

    /**
     * Gestion des présences des élèves avec les presences customisés
     * @Route("/presencesCustomized", name="presences_customized")
     */
    public function presencesCustomized(Request $request){
        $manager = $this->getDoctrine()->getManager();

        if($request->isMethod('post')){
            $presence = $request->request->all();
            $values = explode(",", $presence['presence']);
            
            $typePresence = $this->getDoctrine()
            ->getRepository(CustomizedPresences::class)
            ->findOneById($values[2]);

            $presenceEleve = $this->getDoctrine()
            ->getRepository(CoursGroupe::class)
            ->findOneBy(
                [
                    'coursId' => $values[0],
                    'eleveId' => $values[1]
                ]
            );

            $presenceEleve
            ->setCustomizedPresences($typePresence)
            ->setPresences($typePresence->getTypePresence());
            $manager->persist($presenceEleve);
            $manager->flush();
        }

        return new Response();
    }

    /**
     * Modification de la date du cours 
     * @Route("/modifDateCours", name="modif_date_cours")
     */
    public function modifDate(Request $request){
        $manager = $this->getDoctrine()->getManager();
        if($request->isMethod('post')){
           
            $date =  $request->request->all();
            $newDate = new \DateTime($date['value']);

            $cours = 
            $manager->getRepository(Cours::class)
            ->findOneById($date['pk']);

            $periodes = $manager
            ->getRepository(Periodes::class)
            ->findBygroupe($cours->getGroupe());

            $cours->setDateCours($newDate);
            $manager->persist($cours);
            $manager->flush();
            
        }    

        return new Response("");
    }

    /**
     * Modification de l'intitulé du cours
     * @Route("/modifIntituleCours", name="modif_intitule_cours")
     */
    public function modifIntitule(Request $request){
        $manager = $this->getDoctrine()->getManager();

        if($request->isMethod('post')){
            $intitule =  $request->request->all();

            $cours = 
            $manager->getRepository(Cours::class)
            ->findOneById($intitule['pk']);

            $cours->setIntitule($intitule['value']);
            $manager->persist($cours);
            $manager->flush();
            
        }    
        return new Response("");
    }

    /**
     * Modification des heures du cours
     * @Route("/modifHeuresCours", name="modif_heures_cours")
     */
    public function modifHeuresCours(Request $request){
        $manager = $this->getDoctrine()->getManager();
        if($request->isMethod('post')){
            $heures =  $request->request->all();

            $cours = 
            $manager->getRepository(Cours::class)
            ->findOneBy(
                [
                    'id' => $heures['pk']
                ]
            );
            $cours->setNombreHeures($heures['value']);
            $manager->persist($cours);
            $manager->flush();


        }

        return new Response("");
    }

     /**
      * Modificaiton de la cote pour le cours
     * @Route("/modifCoteCours", name="modif_cote_cours")
     */
    public function modfiCoteCours(Request $request){
        $manager = $this->getDoctrine()->getManager();

        if($request->isMethod('post')){
            $cote = $request->request->all();

            $cours = $manager
            ->getRepository(Cours::class)
            ->findOneById($cote['pk']);

            $cours->setSurCombien($cote['value']);
            $manager->persist($cours);
            $manager->flush();
        }

        return new Response("");
    }

    /**
     * Modification des heures d'évaluation 
     * @Route("/modifHeuresEval", name="modif_heures_eval")
     */
    public function modifHeuresCompetence(Request $request){
        $manager = $this->getDoctrine()->getManager();

        if($request->isMethod('post')){
            $heures =  $request->request->all();

            $evaluation = $this->getDoctrine()
            ->getRepository(Evaluation::class)
            ->findOneById($heures['pk']);

            $evaluation->setHeuresCompetence($heures['value']);
            $manager->persist($evaluation);
            $manager->flush();

        }

        return new Response("");

    }

    /**
     * Modification de l'intitulé
     * @Route("modifIntituleEval", name="modif_inititule_eval")
     */
    public function modifIntituleEval(Request $request){
        $manager = $this->getDoctrine()->getManager();

        if($request->isMethod('post')){
            $intitule =  $request->request->all();

            $evaluation = $this->getDoctrine()
            ->getRepository(Evaluation::class)
            ->findOneById($intitule['pk']);

            $evaluation->setIntitule($intitule['value']);
            $manager->persist($evaluation);
            $manager->flush();
        }

        return new Response("");
    }

    /**
     * Modification de la date d'évaluation
     * @Route("modifDateEval", name="modif_date_eval")
     */
    public function modifDateEval(Request $request){
        $manager = $this->getDoctrine()->getManager();
        if($request->isMethod('post')){
            $date = $request->request->all();
            $newDate = new \DateTime($date['value']);

            $evaluation = $this->getDoctrine()
            ->getRepository(Evaluation::class)
            ->findOneById($date['pk']);

            $evaluation->setDateEvaluation($newDate);
            $manager->persist($evaluation);
            $manager->flush();
        }
        return new Response("");
    }

    /**
     * Modification des points d'évaluation
     * @Route("modifPointsEval", name="modif_points_evaluation")
     */
    public function modifPointsEval(Request $request){
        $manager = $this->getDoctrine()->getManager();

        if($request->isMethod('post')){
            $points = $request->request->all();

            $evalGroup = $manager
            ->getRepository(EvaluationGroup::class)
            ->findOneBy(
                [
                    'evaluation' => $points['pk'],
                    'eleve' => $points['name']
                ]
            );
            $evalGroup->setPoints($points['value']);
            $manager->persist($evalGroup);
            $manager->flush();
        }

        return new Response("");
    }

    /**
     * Modification de la compétence
     * @Route("/changementCompetence", name="changement_competence")
     */
    public function chagementCompetence(Request $request){
        $manager = $this->getDoctrine()->getManager();

        if($request->isMethod('post')){
            $competence = $request->request->all();
            $values = explode(",", $competence['competences']);

            $typeCompetence =  $this->getDoctrine()
            ->getRepository(Competences::class)
            ->findOneById($values[0]);

            $evaluation = $this->getDoctrine()
            ->getRepository(Evaluation::class)
            ->findOneById($values[1]);

            $evaluation->setCompetence($typeCompetence);
            $manager->persist($evaluation);
            $manager->flush();
        }
        
        return new Response("");
    }

     /**
      * Modification de la cote d'évaluation
     * @Route("/modifCoteEval", name="modif_cote_eval")
     */
    public function modfiCoteEval(Request $request){
        $manager = $this->getDoctrine()->getManager();

        if($request->isMethod('post')){
            $cote = $request->request->all();

            $evaluation = $manager
            ->getRepository(Evaluation::class)
            ->findOneById($cote['pk']);

            $evaluation->setSurCombien($cote['value']);
            $manager->persist($evaluation);
            $manager->flush();
        }

        return new Response("");
        
    }
}