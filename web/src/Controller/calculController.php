<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Periodes;
use App\Entity\Evaluation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class calculController extends AbstractController {

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