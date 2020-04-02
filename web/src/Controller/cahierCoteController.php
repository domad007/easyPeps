<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Classe;
use App\Entity\Periodes;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class cahierCoteController extends AbstractController {


    /**
     * @Route("/choixCahier", name="choix_cahier")
     */
    public function choixCahier(UserInterface $user){
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
            '/cahierCotes/choixCahier.html.twig', 
            [
                'groups' => $groups
            ]
        );
    }


    /**
     * @Route("/cahierCotes/{idGroup}", name="cahier_cotes")
     */
    public function cahierCotes(UserInterface $user, $idGroup){

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

        $group = $manager
        ->getRepository(Classe::class)
        ->findByGroups($idGroup);

        $getPeriodes = $this->getDoctrine()
        ->getRepository(Periodes::class)
        ->findBygroupe($idGroup);

        $eleves =  $manager
        ->getRepository(Eleve::class)
        ->findByclasse($group);

        $getCompetencesPeriode = $this->forward('App\Controller\calculController::getMoyenneCompetence', 
        [
            'idGroup' => $idGroup
        ]);
        
        return $this->render(
            '/cahierCotes/cahierCotes.html.twig',
            [
                'ecole' => $group,
                'eleves' => $eleves,   
                'competencesPeriode' => $getCompetencesPeriode,
                'periodes' => $getPeriodes,
                'groups' => $groups,
            ]
        );
    }

    public function getMoyenneCours($idGroup){
        $nombreHeuresPeriode = 0;
        $nombreHeuresTotal = 0;
        $moyenne = 0;

        $manager = $this->getDoctrine()->getManager();

        $coursPeriodes = $this->getDoctrine()
        ->getRepository(Cours::class)
        ->findBygroupe($idGroup);
        
        $periodes = $this->getDoctrine()
        ->getRepository(Periodes::class)
        ->findBygroupe($idGroup); 
        
        foreach($coursPeriodes as $key => $val){
            $nombreHeuresTotal += $val->getNombreHeures();
            if($val->getPeriode()->getId()){                  
                $heures[$val->getPeriode()->getId()][] = $val->getNombreHeures();
            }
        }

        foreach($heures as $key => $value){
            $nombreHeuresPeriode = array_sum($value);
            foreach($periodes as $cle => $val){
                if($val->getId() == $key){
                    $moyenne = ($nombreHeuresPeriode/$nombreHeuresTotal)*100;
                    $val->setPourcentageCours($moyenne);
                    $manager->persist($val);
                }
            }
        }
        $manager->flush();
    }
    
    public function getMoyenneEvaluation($idGroup){
        $nombreHeuresPeriode = 0;
        $nombreHeuresTotal = 0;
        $heuresCompetence = 0;
        $moyenne = 0;

        $manager = $this->getDoctrine()->getManager();

        $evaluation = $this->getDoctrine()
        ->getRepository(Evaluation::class)
        ->findBygroupe($idGroup);
        
        $periodes = $this->getDoctrine()
        ->getRepository(Periodes::class)
        ->findBygroupe($idGroup); 
        
        foreach($evaluation as $key => $val){
            $nombreHeuresTotal += $val->getHeuresCompetence();
            if($val->getPeriode()->getId()){                  
                $heures[$val->getPeriode()->getId()][] = $val->getHeuresCompetence();
            }
        }

        foreach($heures as $key => $value){
            $nombreHeuresPeriode = array_sum($value);
            foreach($periodes as $cle => $val){
                if($val->getId() == $key){
                    $moyenne = ($nombreHeuresPeriode/$nombreHeuresTotal)*100;
                    $val->setPourcentageEval($moyenne);
                    $manager->persist($val);
                }
            }
        }
        $manager->flush();
        return new Response("");
    }

    public function getMoyenneCompetence($idGroup){
        $nombreHeuresCompetence = 0;
        $nombreHeuresTotal = 0;
        $moyenne = 0;
        $heuresParPeriode = 0;

        $evaluation = $this->getDoctrine()
        ->getRepository(Evaluation::class)
        ->findBygroupe($idGroup);
        
        $periodes = $this->getDoctrine()
        ->getRepository(Periodes::class)
        ->findBygroupe($idGroup); 

        foreach($evaluation as $key => $val){
            if($val->getPeriode()->getId()){                  
                $heuresPeriodes[$val->getPeriode()->getId()][] = $val->getHeuresCompetence();
            }
            if($val->getPeriode()->getId() && $val->getCompetence()->getId()){
                $heuresCompetence[$val->getPeriode()->getNomPeriode()][$val->getCompetence()->getNom()][] = $val->getHeuresCompetence();
            }
        }

        foreach($heuresCompetence as $key => $value){
            foreach($value as $cle => $val){
                $nombreHeuresCompetence = array_sum($val);
                $heures[$key][$cle] = $nombreHeuresCompetence;
            
            }
        }
        return $heures;
    }
}