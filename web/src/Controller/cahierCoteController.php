<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\Classe;
use App\Entity\Groups;
use App\Entity\Periodes;
use App\Entity\Evaluation;
use App\Form\GroupPeriodeType;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class cahierCoteController extends AbstractController {


    /**
     * @Route("/choixCahier", name="choix_cahier")
     * @Security("is_granted('ROLE_ACTIF')", statusCode=405)
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
     * @Route("/cahierCotes/{group}", name="cahier_cotes")
     * @Security("is_granted('ROLE_ACTIF')", statusCode=405)
     */
    public function cahierCotes(UserInterface $user, Groups $group){

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

        $classes = $manager
        ->getRepository(Classe::class)
        ->findByGroups($group);

        $eleves =  $manager
        ->getRepository(Eleve::class)
        ->findByclasse($classes);
        
        $moyenneCoursEval = [$this->getMoyenneCours($group), $this->getMoyenneEvaluation($group)];
        $moyenneSemCoursEval = ['cours' => $this->getCoursSem($group),'eval' => $this->getEvalSem($group)];

        return $this->render(
            '/cahierCotes/cahierCotes.html.twig',
            [
                'ecole' => $classes,
                'eleves' => $eleves, 
                'groups' => $groups,
                'moyennePeriodes' => $moyenneCoursEval,
                'moyenneChampPeriode' => $this->getMoyenneChamps($group),
                'moyenneCompetence' => $this->getMoyenneCompetences($group),
                'moyennesSemestres' => $moyenneSemCoursEval,
                'moyennesChampSem' => $this->getMoyenneChampsSem($group),
                'moyennesCompetencesSem' => $this->getMoyenneCompetencesSem($group)
 
            ]
        );
    }


    public function getMoyenneCours($group){
        $manager = $this->getDoctrine()->getManager();
        $nombreTotalHeures = 0;
        $nombreHeuresPeriode = 0;
        $coursPeriode = $manager
        ->getRepository(Cours::class)
        ->findBygroupe($group);

        if(empty($coursPeriode)) return 0;

        foreach($coursPeriode as $key => $value){
            $nombreTotalHeures += $value->getNombreHeures();
            if($value->getPeriode()){
                $nombreTotalPeriode[$value->getPeriode()->getNomPeriode()][] =  $value->getNombreHeures();
            }
        }
        
        foreach($nombreTotalPeriode as $key => $value){
            $nombreHeuresPeriode = array_sum($value);
            $nombreTotalPeriode[$key] = $nombreHeuresPeriode;           
            $nombreTotalHeures = array_sum($nombreTotalPeriode);
        }

        foreach($nombreTotalPeriode as $key => $value){
            $moyennesCours[$key] = ($value/$nombreTotalHeures)*100;
        }
        ksort($moyennesCours);
        return $moyennesCours;
    }

    public function getMoyenneEvaluation($group){
        $manager = $this->getDoctrine()->getManager();
        $nombreTotalHeures = 0;
        $nombreHeuresPeriode = 0;
        $coursEvaluation = $manager
        ->getRepository(Evaluation::class)
        ->findBygroupe($group);

        if(empty($coursEvaluation)) return 0;

        foreach($coursEvaluation as $key => $value){
            $nombreTotalHeures += $value->getHeuresCompetence();
            if($value->getPeriode()){
                $nombreTotalPeriode[$value->getPeriode()->getNomPeriode()][] =  $value->getHeuresCompetence();
            }
        }
        
        foreach($nombreTotalPeriode as $key => $value){
            $nombreHeuresPeriode = array_sum($value);
            $nombreTotalPeriode[$key] = $nombreHeuresPeriode;           
            $nombreTotalHeures = array_sum($nombreTotalPeriode);
        }

        foreach($nombreTotalPeriode as $key => $value){
            $moyennesEvaluation[$key] = ($value/$nombreTotalHeures)*100;
        }
        ksort($moyennesEvaluation);
        return $moyennesEvaluation;
    }

    public function getMoyenneChamps($group){
        $manager = $this->getDoctrine()->getManager();
        $nombreTotalHeures = 0;
        $nombreHeuresPeriode = 0;
        $coursEvaluationCompetences = $manager
        ->getRepository(Evaluation::class)
        ->findBygroupe($group);

        if(empty($coursEvaluationCompetences)) return 0;

        foreach($coursEvaluationCompetences as $key => $value){
            if($value->getCompetence()->getTypeCompetence()->getId()){
                $nombreTotalPeriode[$value->getPeriode()->getNomPeriode()][$value->getCompetence()->getTypeCompetence()->getIntitule()][] =  $value->getHeuresCompetence();
            }
        }       

        foreach($nombreTotalPeriode as $key => $value){
            foreach($value as $cle => $val){
                $totalChamp = array_sum($val);
                $totalChampPeriode[$key][$cle] = $totalChamp;

                $totalPeriode[$key] = array_sum($totalChampPeriode[$key]);
            }
        }
        
        foreach($totalChampPeriode as $key => $value){
            if($key){
                foreach($value as $cle => $val){
                   $moyenneChamp[$key][$cle] = ($totalChampPeriode[$key][$cle]/$totalPeriode[$key])*100;
                }
            }
        }
        $periodes = array_keys($moyenneChamp);
        foreach($moyenneChamp as $key => $value){
            foreach($value as $cle => $val){
                if($cle){
                    $moyenne[$cle][$key] = $val;
                }
            }
        }

        foreach($moyenne as $key => $value){
            foreach($periodes as $cle => $valeur){
                if(empty($value[$valeur])){
                   $moyenne[$key][$valeur] = 0;
                }    
            }
        }

        foreach($moyenne as $key => $value){
            ksort($value);
            $moyenne[$key] = $value;
        }
        return $moyenne;    
    }

    public function getMoyenneCompetences($group){
        $manager = $this->getDoctrine()->getManager();
        $nombreTotalHeures = 0;
        $nombreHeuresPeriode = 0;
        $coursEvaluationCompetences = $manager
        ->getRepository(Evaluation::class)
        ->findBygroupe($group);

        if(empty($coursEvaluationCompetences)) return 0;

        foreach($coursEvaluationCompetences as $key => $value){
            if($value->getCompetence()->getTypeCompetence()->getId()){
                $nombreTotalPeriode[$value->getPeriode()->getNomPeriode()][$value->getCompetence()->getNom()][] =  $value->getHeuresCompetence();
            }
        }       

        foreach($nombreTotalPeriode as $key => $value){
            foreach($value as $cle => $val){
                $totalChamp = array_sum($val);
                $totalChampPeriode[$key][$cle] = $totalChamp;

                $totalPeriode[$key] = array_sum($totalChampPeriode[$key]);
            }
        }
        
        foreach($totalChampPeriode as $key => $value){
            if($key){
                foreach($value as $cle => $val){
                   $moyenneChamp[$key][$cle] = ($totalChampPeriode[$key][$cle]/$totalPeriode[$key])*100;
                }
            }
        }
        $periodes = array_keys($moyenneChamp);
        foreach($moyenneChamp as $key => $value){
            foreach($value as $cle => $val){
                if($cle){
                    $moyenne[$cle][$key] = $val;
                }
            }
        }

        foreach($moyenne as $key => $value){
            foreach($periodes as $cle => $valeur){
                if(empty($value[$valeur])){
                   $moyenne[$key][$valeur] = 0;
                }    
            }
        }

        foreach($moyenne as $key => $value){
            ksort($value);
            $moyenne[$key] = $value;
        }
        ksort($moyenne);
        return $moyenne;
    }

    public function getCoursSem($group){
        $manager = $this->getDoctrine()->getManager();
        $coursSem = $manager
        ->getRepository(Cours::class)
        ->findBygroupe($group);

        $nombreHeuresTotal =0;

        foreach($coursSem as $key => $value){
            $nombreHeuresTotal += $value->getNombreHeures();
            if($value->getPeriode()->getSemestres()){
                $heuresSemestre[$value->getPeriode()->getSemestres()->getIntitule()][] = $value->getNombreHeures();
            }
        }

        foreach($heuresSemestre as $key => $value){
            $coursSem[$key] = array_sum($value);
            $moyenne[$key] = ($coursSem[$key]/$nombreHeuresTotal)*100;
        }

        ksort($moyenne);
        return $moyenne;

    }

    public function getEvalSem($group){
        $manager = $this->getDoctrine()->getManager();
        $evalSem = $manager
        ->getRepository(Evaluation::class)
        ->findBygroupe($group);

        $nombreHeuresTotal =0;

        foreach($evalSem as $key => $value){
            $nombreHeuresTotal += $value->getHeuresCompetence();
            if($value->getPeriode()->getSemestres()){
                $heuresSemestre[$value->getPeriode()->getSemestres()->getIntitule()][] = $value->getHeuresCompetence();
            }
        }

        foreach($heuresSemestre as $key => $value){
            $coursSem[$key] = array_sum($value);
            $moyenne[$key] = ($coursSem[$key]/$nombreHeuresTotal)*100;
        }
        ksort($moyenne);
        return $moyenne;
    }

    public function getMoyenneChampsSem($group){
        $manager = $this->getDoctrine()->getManager();
        $nombreTotalHeures = 0;
        $nombreHeuresPeriode = 0;
        $coursEvaluationCompetences = $manager
        ->getRepository(Evaluation::class)
        ->findBygroupe($group);

        if(empty($coursEvaluationCompetences)) return 0;

        foreach($coursEvaluationCompetences as $key => $value){
            if($value->getCompetence()->getTypeCompetence()->getId()){
                $nombreTotalPeriode[$value->getPeriode()->getSemestres()->getIntitule()][$value->getCompetence()->getTypeCompetence()->getIntitule()][] =  $value->getHeuresCompetence();
            }
        }       

        foreach($nombreTotalPeriode as $key => $value){
            foreach($value as $cle => $val){
                $totalChamp = array_sum($val);
                $totalChampPeriode[$key][$cle] = $totalChamp;

                $totalPeriode[$key] = array_sum($totalChampPeriode[$key]);
            }
        }
        
        foreach($totalChampPeriode as $key => $value){
            if($key){
                foreach($value as $cle => $val){
                   $moyenneChamp[$key][$cle] = ($totalChampPeriode[$key][$cle]/$totalPeriode[$key])*100;
                }
            }
        }
        $periodes = array_keys($moyenneChamp);
        foreach($moyenneChamp as $key => $value){
            foreach($value as $cle => $val){
                if($cle){
                    $moyenne[$cle][$key] = $val;
                }
            }
        }

        foreach($moyenne as $key => $value){
            foreach($periodes as $cle => $valeur){
                if(empty($value[$valeur])){
                   $moyenne[$key][$valeur] = 0;
                }    
            }
        }

        foreach($moyenne as $key => $value){
            ksort($value);
            $moyenne[$key] = $value;
        }
        
        return $moyenne;  
    }

    public function getMoyenneCompetencesSem($group){
        $manager = $this->getDoctrine()->getManager();
        $nombreTotalHeures = 0;
        $nombreHeuresPeriode = 0;
        $coursEvaluationCompetences = $manager
        ->getRepository(Evaluation::class)
        ->findBygroupe($group);

        if(empty($coursEvaluationCompetences)) return 0;

        foreach($coursEvaluationCompetences as $key => $value){
            if($value->getCompetence()->getTypeCompetence()->getId()){
                $nombreTotalPeriode[$value->getPeriode()->getSemestres()->getIntitule()][$value->getCompetence()->getNom()][] =  $value->getHeuresCompetence();
            }
        }       

        foreach($nombreTotalPeriode as $key => $value){
            foreach($value as $cle => $val){
                $totalChamp = array_sum($val);
                $totalChampPeriode[$key][$cle] = $totalChamp;

                $totalPeriode[$key] = array_sum($totalChampPeriode[$key]);
            }
        }
        
        foreach($totalChampPeriode as $key => $value){
            if($key){
                foreach($value as $cle => $val){
                   $moyenneChamp[$key][$cle] = ($totalChampPeriode[$key][$cle]/$totalPeriode[$key])*100;
                }
            }
        }
        $periodes = array_keys($moyenneChamp);
        foreach($moyenneChamp as $key => $value){
            foreach($value as $cle => $val){
                if($cle){
                    $moyenne[$cle][$key] = $val;
                }
            }
        }

        foreach($moyenne as $key => $value){
            foreach($periodes as $cle => $valeur){
                if(empty($value[$valeur])){
                   $moyenne[$key][$valeur] = 0;
                }    
            }
        }

        foreach($moyenne as $key => $value){
            ksort($value);
            $moyenne[$key] = $value;
        }
        ksort($moyenne);
        return $moyenne;
    }
}