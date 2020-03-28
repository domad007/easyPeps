<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Periodes;
use App\Entity\Evaluation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class calculController extends AbstractController {

    public function getMoyenneCours($idGroup){
        $manager = $this->getDoctrine()->getManager();

        $coursPeriodes = $this->getDoctrine()
        ->getRepository(Cours::class)
        ->findBygroupe($idGroup);
        
        $periodes = $this->getDoctrine()
        ->getRepository(Periodes::class)
        ->findBygroupe($idGroup); 

        $nombreHeuresPeriode = 0;
        $nombreHeuresTotal = 0;
        $moyenne = 0;
        
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
        $manager = $this->getDoctrine()->getManager();

        $evaluation = $this->getDoctrine()
        ->getRepository(Evaluation::class)
        ->findBygroupe($idGroup);
        
        $periodes = $this->getDoctrine()
        ->getRepository(Periodes::class)
        ->findBygroupe($idGroup); 

        $nombreHeuresPeriode = 0;
        $nombreHeuresTotal = 0;
        $moyenne = 0;
        
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
    }
}