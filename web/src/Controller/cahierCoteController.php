<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\Classe;
use App\Entity\Groups;
use App\Entity\Periodes;
use App\Entity\Evaluation;
use App\Entity\CoursGroupe;
use App\Form\GroupPeriodeType;
use App\Entity\EvaluationGroup;
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
        return $this->render(
            '/cahierCotes/cahierCotes.html.twig',
            [
                'groups' => $groups,
                'eleves' => $this->getMoyenneCoursEleve($group),
                'moyennePeriodes' => $this->getMoyenneCours($group),
                'moyenneChampPeriode' => $this->getMoyenneChamps($group),
                'moyenneCompetence' => $this->getMoyenneCompetences($group),
                'moyennesSemestres' => $this->getCoursSem($group),
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

    public function getMoyenneCoursEleve($group){
        $manager = $this->getDoctrine()->getManager();
        $getCours = $manager
        ->getRepository(Cours::class)
        ->findBygroupe($group);

        $getEvaluation = $manager
        ->getRepository(Evaluation::class)
        ->findBygroupe($group);
        
        $coursGroupe = $manager
        ->getRepository(CoursGroupe::class)
        ->findBycoursId($getCours);

        $evaluationGroup = $manager
        ->getRepository(EvaluationGroup::class)
        ->findByevaluation($getEvaluation);

        $classes = $manager
        ->getRepository(Classe::class)
        ->findByGroups($group);

        $eleves =  $manager
        ->getRepository(Eleve::class)
        ->findByclasse($classes);

        $periodes = $manager
        ->getRepository(Periodes::class)
        ->findBygroupe($group);


        foreach($getCours as $key => $value){
            if($value->getPeriode()){
                $heuresPeriode[$value->getPeriode()->getId()][] = $value->getNombreHeures();
                $pointsTotal[$value->getPeriode()->getId()][] =  $value->getSurCombien();

                $heuresPeriodeTotal[$value->getPeriode()->getId()] = array_sum($heuresPeriode[$value->getPeriode()->getId()]);
                $pointsTotalPeriode[$value->getPeriode()->getId()] = array_sum($pointsTotal[$value->getPeriode()->getId()]);
            }
        }

        foreach($coursGroupe as $key => $value){
            if($value->getCoursId()->getPeriode()){
                $pointsElevePeriode[$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()][] = $value->getPoints(); 
                $totalPointsPeriode[$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()] = array_sum($pointsElevePeriode[$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]);
            }
        }

        foreach($totalPointsPeriode as $key => $value){
            foreach($pointsTotalPeriode as $cle => $valeur){
                if($key == $cle){
                    foreach($value as $ke => $val){
                        $pointsElevePeriode[$cle][$ke] = ($val/$valeur)*10;
                    }
                }
            }
        }

        foreach($getEvaluation as $key => $value){
            if($value->getPeriode()){
                $heuresPeriodeEval[$value->getPeriode()->getId()][] = $value->getHeuresCompetence();
                $pointsTotalEval[$value->getPeriode()->getId()][] =  $value->getSurCombien();

                $heuresPeriodeTotalEval[$value->getPeriode()->getSemestres()->getIntitule()][$value->getPeriode()->getId()] = array_sum($heuresPeriodeEval[$value->getPeriode()->getId()]);
                ksort($heuresPeriodeTotalEval[$value->getPeriode()->getSemestres()->getIntitule()]);
                $pointsTotalPeriodeEval[$value->getPeriode()->getId()] = array_sum($pointsTotalEval[$value->getPeriode()->getId()]);
            }
        }

        foreach($evaluationGroup as $key => $value){
            if($value->getEvaluation()->getPeriode()){
                $pointsElevePeriodeEval[$value->getEvaluation()->getPeriode()->getId()][$value->getEleve()->getId()][] = $value->getPoints(); 
                $totalPointsPeriodeEval[$value->getEvaluation()->getPeriode()->getId()][$value->getEleve()->getId()] = array_sum($pointsElevePeriodeEval[$value->getEvaluation()->getPeriode()->getId()][$value->getEleve()->getId()]);
            }
        }

        foreach($totalPointsPeriodeEval as $key => $value){
            foreach($pointsTotalPeriodeEval as $cle => $valeur){
                if($key == $cle){
                    foreach($value as $ke => $val){
                        $pointsEleveEval[$cle][$ke] = ($val/$valeur)*10;
                    }
                }
            }
        } 

        ksort($pointsElevePeriode);
        ksort($pointsEleveEval);

        foreach($eleves as $key => $value){
            foreach($pointsElevePeriode as $cle => $valeur){
                foreach($valeur as $ke => $val){
                    if($value->getId() == $ke){                      
                        $value->addMoyennePeriodeCours([$cle => $val]);                        
                    }                   
                }
            }
            foreach($pointsEleveEval as $cle => $valeur){
                foreach($valeur as $ke => $val){
                    if($value->getId() == $ke){
                        $value->addMoyennePeriodeEval([$cle => $val]);
                    }
                }
            }
        }

        foreach($periodes as $key => $value){
            foreach($pointsElevePeriode as $cle => $valeur){
                if($value->getId() == $cle){
                    $pointsEleveSemCours[$value->getSemestres()->getIntitule()][$cle] = $valeur;
                }
            }

            foreach($pointsEleveEval as $cle => $valeur){
                if($value->getId() == $cle){
                    $pointsEleveSemEval[$value->getSemestres()->getIntitule()][$cle] = $valeur;
                }
            }
        }

        foreach($periodes as $key => $value){
            foreach($heuresPeriodeTotal as $k => $v){
                if($value->getId() == $k){
                    $heuresSemCours[$value->getSemestres()->getIntitule()][] = $v;
                    $heuresSemCoursTotal[$value->getSemestres()->getIntitule()] = array_sum($heuresSemCours[$value->getSemestres()->getIntitule()]);
                }
            }

            foreach($heuresPeriodeTotalEval as $k => $v){
                foreach($v as $a => $b){
                    if($value->getId() == $a){                   
                        $heuresSemEval[$value->getSemestres()->getIntitule()][] = $b;
                        $heuresSemEvalTotal[$value->getSemestres()->getIntitule()] = array_sum($heuresSemEval[$value->getSemestres()->getIntitule()]);
    
                    }
                }
            }
        }

        dump($heuresPeriodeTotal);
        dump($heuresSemCoursTotal);
        dump($pointsEleveSemCours);
        foreach($pointsEleveSemEval as $key => $value){
            foreach($value as $cle => $valeur){
                foreach($valeur as $k => $v){
                    $moy[$key][$k][] = ($pointsEleveSemEval[$key][$cle][$k]*$heuresPeriodeTotalEval[$key][$cle]);
                    $moyenne[$key][$k] = array_sum($moy[$key][$k])/$heuresSemEvalTotal[$key];
                }
            }
        }
        //$moyenne["moyenne"] = array_sum($moy["Semestre 1"][1])/31;
        return $eleves;
    }
}