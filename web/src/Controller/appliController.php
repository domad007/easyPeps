<?php

namespace App\Controller;
use DateTime;
use App\Entity\User;
use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\Classe;
use App\Entity\Groups;
use App\Entity\Evaluation;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class appliController extends AbstractController {
    private $serializer;
    public function __construct(SerializerInterface $serializer){
        $this->serializer = $serializer;
    }
    /**
     * @Route("/connexionAppli/{pseudo}", name="connexion_appli")
     */
    public function connexionAppli($pseudo){
        $manager = $this->getDoctrine()->getManager();
        $getUser = $manager
        ->getRepository(User::class)
        ->findBy(
            [
                'nomUser' => $pseudo,
            ]
        );
        foreach($getUser as $key => $value){
            $user['id'] = $value->getId();
        }
        if(!empty($getUser)){
            echo json_encode($user);
        }
        else {
            echo json_encode("problem");
        }
        return new Response("");
    }

    /**
     * @Route("/groupsUser/{user}", name="ecoles_user")
     */
    public function ecolesUser($user){
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
        $getGroups->setParameter(1, $user);
        $groups = $getGroups->getResult();

        if(empty($groups)){
            echo json_encode("probleme");
        }
        else {
            echo json_encode($groups);
        }

        return new Response();   
    }

    /**
     * @Route("coursUser/{group}", name="cours_user")
     */
    public function coursGroup(Groups $group){
        $manager = $this->getDoctrine()->getManager();
        $cours = $manager
        ->getRepository(Cours::class)
        ->findBygroupe($group);

        if(!empty($cours)){
            foreach($cours as $key => $value){
                $coursProf[$key]['cours'] = $value->getIntitule();
                $coursProf[$key]['date_cours'] = $value->getDateCours()->format('d-m-y');
                $coursProf[$key]['heures'] = $value->getNombreHeures();
                $coursProf[$key]['periode'] = $value->getPeriode()->getNomPeriode();
            }
            echo json_encode($coursProf);
        }
        else {
            echo json_encode("probleme");
        }

       return new Response();
    }

    /**
     * @Route("evaluationUser/{group}", name="evaluation_user")
     */
    public function evaluationUser(Groups $group){
        $manager = $this->getDoctrine()->getManager();
        $evaluation = $manager
        ->getRepository(Evaluation::class)
        ->findBygroupe($group);

        if(!empty($evaluation)){
            foreach($evaluation as $key => $value){
                $evalProf[$key]['evaluation'] = $value->getIntitule();
                $evalProf[$key]['date_evaluation'] = $value->getDateEvaluation()->format('d-m-y');
                $evalProf[$key]['heures'] = $value->getHeuresCompetence();
                $evalProf[$key]['competence'] = $value->getCompetence()->getNom();
                $evalProf[$key]['sur_combien'] = $value->getSurCombien();
                $evalProf[$key]['periode'] = $value->getPeriode()->getNomPeriode();
            }
            echo json_encode($evalProf);
        }
        else {
            echo json_encode("probleme");
        }

        return new Response();
    }

    /**
     * @Route("userEleves/{group}", name="user_eleves")
     */
    public function userEleves(Groups $group){
        $manager = $this->getDoctrine()->getManager();
        $dateAjd = new DateTime();
        $classes = $manager
        ->getRepository(Classe::class)
        ->findBygroups($group);

        $eleves = $manager
        ->getRepository(Eleve::class)
        ->findByclasse($classes);
        
        if(!empty($eleves)){
            foreach($eleves as $key => $value){
                $eleveProf[$key]['nom'] = $value->getNom();
                $eleveProf[$key]['prenom'] = $value->getPrenom();
                $eleveProf[$key]['classe'] = $value->getClasse()->getNomClasse();
                $eleveProf[$key]['age'] = $dateAjd->diff($value->getDateNaissance(), true)->y;
            }

            echo json_encode($eleveProf);
        }
        else {
            echo json_encode("probleme");
        }
        return new Response();
    }

}