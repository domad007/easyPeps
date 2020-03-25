<?php

namespace App\Controller;

use DateTime;
use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\Classe;
use App\Entity\Periodes;
use App\Entity\Presences;
use App\Entity\Evaluation;
use App\Entity\Competences;
use App\Entity\CoursGroupe;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class journalController extends AbstractController {

    /**
     * @Route("/journalDeClasse", name="journal")
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
     * @Route("/journalDeClasse/{idGroup}", name="journal_de_classe")
     */
    public function journalDeCalsse(UserInterface $user, $idGroup){
        $manager = $this->getDoctrine()->getManager();
        $getCours = $manager
        ->getRepository(Cours::class)
        ->findBygroupe($idGroup, 
        [
            'dateCours' => 'ASC'
        ]);

        $getPeriodes = $this->getDoctrine()
        ->getRepository(Periodes::class)
        ->findBygroupe($idGroup);

        $group = $manager
        ->getRepository(Classe::class)
        ->findByGroups($idGroup);

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
        $getGroups->setParameter(1, $user->getId());

        $groups = $getGroups->getResult();

        $getPresences = $this->getDoctrine()
        ->getRepository(Presences::class)
        ->findAll();

        $getEvaluations = $this->getDoctrine()
        ->getRepository(Evaluation::class)
        ->findBycours($getCours);


        $getCompetences = $this->getDoctrine()
        ->getRepository(Competences::class)
        ->findBydegre($group[0]->getGroups()->getDegre()->getId());


        foreach($group as $key => $value){           
            $eleves [] = $manager
            ->getRepository(Eleve::class)
            ->findBy(
                [
                    'classe' => $value->getId()
                ]
            );
        }

        foreach($eleves as $key => $value){
            foreach($value as $key => $val){
                foreach($getCoursGroupe as $key => $value){
                    if($value->getEleveId()->getId() == $val->getId()){
                        $val->addCoursGroupe($value);
                        
                    }
                    foreach($getEvaluations as $cle => $valeur){
                        if($value->getCoursId()->getId() == $valeur->getCours()->getId()){
                            $value->getCoursId()->addEvaluation($valeur);
                        }
                    }
                }
            }
        }

    
        return $this->render(
            'journalDeClasse/journal.html.twig', 
            [
                'groups' => $groups,
                'ecole' => $group,
                'eleves' => $eleves,   
                'periodes' => $getPeriodes,
                'presences' => $getPresences,
                'competences' => $getCompetences     
            ]
        );
    }

    /**
     * @Route("/modifPoints", name="modif_points")
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
     * @Route("/modifDate", name="modif_date")
     */
    public function modifDate(Request $request){
        $manager = $this->getDoctrine()->getManager();
        if($request->isMethod('post')){
            $date =  $request->request->all();
            $newDate = new \DateTime($date['value']);

            $cours = 
            $manager->getRepository(Cours::class)
            ->findOneById($date['pk']);

            $cours->setDateCours($newDate);
            $manager->persist($cours);
            $manager->flush();
            
        }    

        return new Response("");
    }

    /**
     * @Route("/modifIntitule", name="modif_intitule")
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
     * @Route("/modifHeuresCompetence", name="modif_heures_competence")
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
     * @Route("/modifPeriode", name="modif_periode")
     */
    public function modifPeriode(Request $request){
        $manager = $this->getDoctrine()->getManager();

        if($request->isMethod('post')){
            $periodeData = $request->request->all(); 
            $getPeriode = $this->getDoctrine()
            ->getRepository(Periodes::class)
            ->findOneById($periodeData['pk']);

            $getPeriode->setPourcentage($periodeData['value']);
            $manager->persist($getPeriode);
            $manager->flush();
        }

        return new Response("");
    }

    /**
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

            $evaluation->setCompetences($typeCompetence);
            $manager->persist($evaluation);
            $manager->flush();
        }
        return new Response("");
    }
}