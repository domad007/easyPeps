<?php

namespace App\Controller;

use Exception;
use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\Types;
use App\Entity\Classe;
use App\Entity\Groups;
use App\Entity\Periodes;
use App\Entity\Evaluation;
use App\Entity\Parametres;
use App\Entity\Competences;
use App\Entity\CoursGroupe;
use App\Entity\Ponderation;
use App\Entity\Appreciation;
use App\Form\GroupPeriodeType;
use App\Entity\EvaluationGroup;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class cahierCoteController extends AbstractController {

    /**
     * Choix du groupe pour lequel on veut accèder au cahier de cotes
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
     * Affichage de toutes les moyennes des élèves
     * Les moyennes sont calcules dans des fonction distinctes
     * Cette fonction joue une sorte de main
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
                'eleves' => $this->moyennesEleve($group, $user->getId()),
                'moyennePeriodes' => $this->getMoyenneCours($group),
                'moyenneChampPeriode' => $this->getMoyenneChamps($group),
                'moyenneCompetence' => $this->getMoyenneCompetences($group),
                'moyennesSemestres' => $this->getCoursSem($group),
                'moyennesChampSem' => $this->getMoyenneChampsSem($group),
                'moyennesCompetencesSem' => $this->getMoyenneCompetencesSem($group),
                'moyenneEvaluationPeriodes' => $this->getMoyenneEvaluation($group),
                'moyenneEvaluationSem' => $this->getEvalSem($group)
 
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

        if(empty($coursSem)) return 0;
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

        if(empty($evalSem)) return 0;

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

        if(empty($coursEvaluationCompetence)) return 0;

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

    private function getMoyenneCoursPeriodeEleve($group){
        $manager = $this->getDoctrine()->getManager();
        $getCours = $manager
        ->getRepository(Cours::class)
        ->findBygroupe($group);

        $coursGroupe = $manager
        ->getRepository(CoursGroupe::class)
        ->findBycoursId($getCours);

        if(empty($coursGroupe)){
            return null;
        }
        $i=0;
        foreach($getCours as $key => $value){
            $heures[$value->getPeriode()->getSemestres()->getIntitule()][$value->getPeriode()->getId()][] = $value->getNombreHeures();
            $heuresTotal[$value->getPeriode()->getSemestres()->getIntitule()][$value->getPeriode()->getId()] = array_sum($heures[$value->getPeriode()->getSemestres()->getIntitule()][$value->getPeriode()->getId()]);

            $points[$value->getPeriode()->getSemestres()->getIntitule()][$value->getPeriode()->getId()][] = $value->getSurCombien();
            $pointsTotal[$value->getPeriode()->getSemestres()->getIntitule()][$value->getPeriode()->getId()] =  array_sum($points[$value->getPeriode()->getSemestres()->getIntitule()][$value->getPeriode()->getId()]);
        }

        foreach($coursGroupe as $key => $value){
            $pointsEleve[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()][] = $value->getPoints();
            $pointsEleveTotal[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["points"] = array_sum($pointsEleve[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]);
            $pointsEleveTotal[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["total"] =  $pointsTotal[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()];

            if($value->getCustomizedPresences() == null){
                if($value->getPresences()->getId() != 1 && $value->getPresences()->getId() != 4 &&$value->getPresences()->getId() != 12 ){
                    $pointsEleveAbs[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["points"][] = $value->getPoints();
                    $pointsEleveAbs[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["total"][] = $value->getCoursId()->getSurCombien();

                    $pointsEleveAbsTotal[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["points"] = array_sum($pointsEleveAbs[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["points"]);
                    $pointsEleveAbsTotal[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["total"] = array_sum($pointsEleveAbs[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["total"]);
                }
            }
            else {
                if($value->getCustomizedPresences()->getTypePresence()->getId() != 1 &&$value->getCustomizedPresences()->getTypePresence()->getId() != 4 &&$value->getCustomizedPresences()->getTypePresence()->getId() != 12 ){
                    $pointsEleveAbs[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["points"][] = $value->getPoints();
                    $pointsEleveAbs[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["total"][] = $value->getCoursId()->getSurCombien();

                    $pointsEleveAbsTotal[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["points"] = array_sum( $pointsEleveAbs[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["points"]);
                    $pointsEleveAbsTotal[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["total"] = array_sum( $pointsEleveAbs[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]["total"]);
                }
            }

            if($value->getCustomizedPresences() == null){
                if($value->getPresences()->getId() == 4){
                    $oubliTshirt[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()][] = $value->getCoursId()->getId();
                    $countOubli[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()] = count($oubliTshirt[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()]);
                }
            }
            else {
                if($value->getCustomizedPresences()->getTypePresence()->getId() == 4 ){
                    $oubliTshirt[$value->getCoursId()->getPeriode()->getSemestres()->getIntitule()][$value->getCoursId()->getPeriode()->getId()][$value->getEleveId()->getId()][] = $value->getCoursId()->getId();
                }
            }
        }
        if(!empty($pointsEleveAbsTotal)){
            foreach($pointsEleveAbsTotal as $key => $value){
                foreach($value as $k => $v){
                    foreach($v as $a => $b){
                        $pointsEleveTotal[$key][$k][$a]["points"] = $pointsEleveTotal[$key][$k][$a]["points"] - $pointsEleveAbsTotal[$key][$k][$a]["points"];
                        $pointsEleveTotal[$key][$k][$a]["total"] = $pointsEleveTotal[$key][$k][$a]["total"] - $pointsEleveAbsTotal[$key][$k][$a]["total"];
                    }
                }
            }
        }

        foreach($pointsEleveTotal as $key => $value){
            foreach($value as $k => $v){
                foreach($v as $a => $b){
                    $moyenne[$key][$k][$a] = ($pointsEleveTotal[$key][$k][$a]["points"]/$pointsEleveTotal[$key][$k][$a]["total"])*10;
                }
            }
            ksort($moyenne[$key]);
        }
        if(!empty($countOubli)){
            foreach($countOubli as $key => $value){
                foreach($value as $cle => $valeur){
                    foreach($valeur as $k => $v){             
                        if($v %  2 == 0){
                            $moyenne[$key][$cle][$k] = $moyenne[$key][$cle][$k] - $v/2;
                        }
                        if($v % 2 == 1){
                            $enleverPoints = ($v-1)/2;
                            $moyenne[$key][$cle][$k] = $moyenne[$key][$cle][$k] - $enleverPoints;
                        }
                    }
                }
            }
        }
        return array(
            'heuresPeriode' => $heuresTotal, 
            'moyennesEleves' => $moyenne
        );
    }

    private function getMoyenneCoursSemestreEleve($group){
        $coursPeriode = $this->getMoyenneCoursPeriodeEleve($group);
        $heuresPeriode = $coursPeriode['heuresPeriode'];
        $moyennePeriode = $coursPeriode['moyennesEleves'];

        if(empty($coursPeriode)){
            return 0;
        }

        foreach($heuresPeriode as $key => $value){
            foreach($value as $k => $v){
                $heuresSemestre[$key][] = $heuresPeriode[$key][$k];
                $heuresSemestreTotal[$key] = array_sum($heuresSemestre[$key]);
            }
        }

        foreach($moyennePeriode as $key => $value){
            foreach($value as $k => $v){
                foreach($v as $a => $b){
                    $moyenneSemestre[$key][$a][] = $moyennePeriode[$key][$k][$a]*$heuresPeriode[$key][$k];
                    $moyenne[$key][$a] = array_sum($moyenneSemestre[$key][$a])/$heuresSemestreTotal[$key];
                }
            }
        }
        return $moyenne;
    }

    private function getMoyenneChampsPeriodeEleve($group){
        $manager = $this->getDoctrine()->getManager();
        $getEvaluation = $manager
        ->getRepository(Evaluation::class)
        ->findBygroupe($group);

        $evaluationGroup = $manager
        ->getRepository(EvaluationGroup::class)
        ->findByevaluation($getEvaluation);

        $typeCompetences =  $manager
        ->getRepository(Types::class)
        ->findAll();

        if(empty($evaluationGroup)){
            return 0;
        }

        foreach($getEvaluation as $key => $value){
            $heures[$value->getPeriode()->getSemestres()->getIntitule()][$value->getPeriode()->getId()][$value->getCompetence()->getTypeCompetence()->getIntitule()][] = $value->getHeuresCompetence();
            $points[$value->getPeriode()->getSemestres()->getIntitule()][$value->getPeriode()->getId()][$value->getCompetence()->getTypeCompetence()->getIntitule()][] = $value->getSurCombien();
        
            $heuresTotal[$value->getPeriode()->getSemestres()->getIntitule()][$value->getPeriode()->getId()][$value->getCompetence()->getTypeCompetence()->getIntitule()] = array_sum( $heures[$value->getPeriode()->getSemestres()->getIntitule()][$value->getPeriode()->getId()][$value->getCompetence()->getTypeCompetence()->getIntitule()]);
            $pointsTotal[$value->getPeriode()->getSemestres()->getIntitule()][$value->getPeriode()->getId()][$value->getCompetence()->getTypeCompetence()->getIntitule()] = array_sum($points[$value->getPeriode()->getSemestres()->getIntitule()][$value->getPeriode()->getId()][$value->getCompetence()->getTypeCompetence()->getIntitule()]);
        }

        foreach($evaluationGroup as $key => $value){
            $pointsEleve[$value->getEvaluation()->getPeriode()->getSemestres()->getIntitule()][$value->getEvaluation()->getPeriode()->getId()][$value->getEleve()->getId()][$value->getEvaluation()->getCompetence()->getTypeCompetence()->getIntitule()][] = $value->getPoints();
            $pointsEleveTotal[$value->getEvaluation()->getPeriode()->getSemestres()->getIntitule()][$value->getEvaluation()->getPeriode()->getId()][$value->getEleve()->getId()]["points"][$value->getEvaluation()->getCompetence()->getTypeCompetence()->getIntitule()]= array_sum($pointsEleve[$value->getEvaluation()->getPeriode()->getSemestres()->getIntitule()][$value->getEvaluation()->getPeriode()->getId()][$value->getEleve()->getId()][$value->getEvaluation()->getCompetence()->getTypeCompetence()->getIntitule()]);
            $pointsEleveTotal[$value->getEvaluation()->getPeriode()->getSemestres()->getIntitule()][$value->getEvaluation()->getPeriode()->getId()][$value->getEleve()->getId()]["total"][$value->getEvaluation()->getCompetence()->getTypeCompetence()->getIntitule()]=$pointsTotal[$value->getEvaluation()->getPeriode()->getSemestres()->getIntitule()][$value->getEvaluation()->getPeriode()->getId()][$value->getEvaluation()->getCompetence()->getTypeCompetence()->getIntitule()];
            
            if(!(string)(int)$value->getPoints() && $value->getPoints() != 0){
                $pointsEleveAbs[$value->getEvaluation()->getPeriode()->getSemestres()->getIntitule()][$value->getEvaluation()->getPeriode()->getId()][$value->getEleve()->getId()]["points"][$value->getEvaluation()->getCompetence()->getTypeCompetence()->getIntitule()][] = 0;
                $pointsEleveAbs[$value->getEvaluation()->getPeriode()->getSemestres()->getIntitule()][$value->getEvaluation()->getPeriode()->getId()][$value->getEleve()->getId()]["total"][$value->getEvaluation()->getCompetence()->getTypeCompetence()->getIntitule()][] = $value->getEvaluation()->getSurCombien();

                $pointsEleveAbsTotal[$value->getEvaluation()->getPeriode()->getSemestres()->getIntitule()][$value->getEvaluation()->getPeriode()->getId()][$value->getEleve()->getId()]["total"][$value->getEvaluation()->getCompetence()->getTypeCompetence()->getIntitule()] = array_sum($pointsEleveAbs[$value->getEvaluation()->getPeriode()->getSemestres()->getIntitule()][$value->getEvaluation()->getPeriode()->getId()][$value->getEleve()->getId()]["total"][$value->getEvaluation()->getCompetence()->getTypeCompetence()->getIntitule()]);
            }
        }

        if(!empty($pointsEleveAbsTotal)){
            foreach($pointsEleveAbsTotal as $key => $value){
                foreach($value as $cle => $valeur){
                    foreach($valeur as $k => $v){
                        foreach($v as $a => $b){
                            foreach($b as $c => $d){
                                $pointsEleveTotal[$key][$cle][$k]["total"][$c] = $pointsEleveTotal[$key][$cle][$k]["total"][$c] - $pointsEleveAbsTotal[$key][$cle][$k]["total"][$c];
                            }
                        }
                    }
                }
            }
        }

        foreach($heuresTotal as $key => $value){
            foreach($value as $cle => $valeur){
                foreach($typeCompetences as $a => $b){
                    if(empty($heuresTotal[$key][$cle][$b->getIntitule()])){
                        $heuresTotal[$key][$cle][$b->getIntitule()] = 0;
                    }
                }
            }
        }
        foreach($pointsEleveTotal as $key => $value){
            foreach($value as $cle => $valeur){
                foreach($valeur as $k => $v){
                    foreach($v as $a => $b){
                        foreach($b as $c => $d){
                            $moyenne[$key][$cle][$k][$c] = ($pointsEleveTotal[$key][$cle][$k]["points"][$c]/$pointsEleveTotal[$key][$cle][$k]["total"][$c])*10;                 
                            foreach($typeCompetences as $a => $b){
                                if(empty($moyenne[$key][$cle][$k][$b->getIntitule()])){
                                    $moyenne[$key][$cle][$k][$b->getIntitule()] = 0;
                                }
                            }
                            ksort($moyenne[$key][$cle][$k]);
                            ksort($moyenne[$key]);
                        }
                    }
                }
            }
        }
        
        return array(
            'moyenneEleves' => $moyenne,
            'heuresTotal' => $heuresTotal
        );
    }

    private function getMoyenneEvaluationPeriodeEleve($group){
        $evaluationChamp = $this->getMoyenneChampsPeriodeEleve($group);
        $heuresChamp = $evaluationChamp['heuresTotal'];
        $moyennesChamp = $evaluationChamp['moyenneEleves'];

        if(empty($evaluationChamp)){
            return 0;
        }

        foreach($heuresChamp as $key => $value){
            foreach($value as $cle => $valeur){
                $heuresChampTotal[$key][$cle] = array_sum($heuresChamp[$key][$cle]);
            }
        }

        foreach($moyennesChamp as $key => $value){
            foreach($value as $cle => $valeur){
                foreach($valeur as $k => $v){
                    foreach($v as $a => $b){
                        $totalChamps[$key][$cle][$k][] = $moyennesChamp[$key][$cle][$k][$a]*$heuresChamp[$key][$cle][$a];
                        $moyenne[$key][$cle][$k] = array_sum($totalChamps[$key][$cle][$k])/$heuresChampTotal[$key][$cle];
                        
                        ksort($moyenne[$key]);
                    }
                }
            }
        }

        return $moyenne;
    }

    private function getMoyenneChampsSemestreEleve($group){
        $evaluationPeriode =  $this->getMoyenneChampsPeriodeEleve($group);
        $moyennePeriode = $evaluationPeriode['moyenneEleves'];
        $heuresPeriode = $evaluationPeriode['heuresTotal'];

        if(empty($evaluationPeriode)){
            return 0;
        }
        foreach($heuresPeriode as $key => $value){
            foreach($value as $cle => $valeur){
                foreach($valeur as $k => $v){
                    $heuresSemestre[$key][$k][] = $heuresPeriode[$key][$cle][$k];
                    $heuresSemestreTotal[$key][$k] = array_sum($heuresSemestre[$key][$k]);
                }
            }
        }
        foreach($moyennePeriode as $key => $value){
            foreach($value as $cle => $valeur){
                foreach($valeur as $k => $v){
                    foreach($v as $a => $b){
                        $moyenneSemestre[$key][$k][$a][] = $moyennePeriode[$key][$cle][$k][$a]*$heuresPeriode[$key][$cle][$a];
                        try {
                            $moyenne[$key][$k][$a] = array_sum($moyenneSemestre[$key][$k][$a])/$heuresSemestreTotal[$key][$a];
                        }
                        catch(\Exception $e){
                            $moyenne[$key][$k][$a] = 0;

                        }
                        ksort($moyenne[$key][$k]);
                    }
                }
            }
        }
        return array(
            'moyenne' => $moyenne,
            'heures' => $heuresSemestreTotal
        );
    }

    private function getMoyenneEvaluationSemestreEleve($group){
        $champsSemestre = $this->getMoyenneChampsSemestreEleve($group);

        $moyennesChamp = $champsSemestre['moyenne'];
        $heuresChamp= $champsSemestre['heures'];

        if(empty($champsSemestre)){
            return 0;
        }

        foreach($heuresChamp as $key => $value){
            $heuresSemestreTotal[$key] = array_sum($heuresChamp[$key]);
        }

        foreach($moyennesChamp as $key => $value){
            foreach($value as $cle => $valeur){
                foreach($valeur as $k => $v){
                    $totalChamps[$key][$cle][] = $moyennesChamp[$key][$cle][$k]*$heuresChamp[$key][$k];
                    $moyenne[$key][$cle] = array_sum($totalChamps[$key][$cle])/$heuresSemestreTotal[$key];
                }
            }
        }
        
        return $moyenne;
    }

    private function getMoyenneCoursEvalPeriodeEleve($group, $user){
        $manager= $this->getDoctrine()->getManager();
        $coursPeriode = $this->getMoyenneCoursPeriodeEleve($group);
        $moyenneEvalPeriode =  $this->getMoyenneEvaluationPeriodeEleve($group);
        $moyenneCoursPeriode = $coursPeriode['moyennesEleves'];

        if(empty($coursPeriode)){
            return 0;
        }
        $classes = $manager
        ->getRepository(Classe::class)
        ->findByGroups($group);
        $ponderation = $manager
        ->getRepository(Ponderation::class)
        ->findOneBy(
            [
                'ecole' => $classes[0]->getEcole()->getId(),
                'professeur' => $user
            ]
        );
        if(!empty($ponderation)){
            foreach($moyenneCoursPeriode as $key => $value){
                foreach($value as $cle => $valeur){
                    foreach($valeur as $k => $v){
                        $moyenne[$key][$cle][$k] = ($v*$ponderation->getCours() + $moyenneEvalPeriode[$key][$cle][$k]*$ponderation->getEvaluation())/100;
                        
                        ksort($moyenne[$key]);
                    }
                }
            }
        }
        else {
            foreach($moyenneCoursPeriode as $key => $value){
                foreach($value as $cle => $valeur){
                    foreach($valeur as $k => $v){
                        $moyenne[$key][$cle][$k] = ($v + $moyenneEvalPeriode[$key][$cle][$k])/2;
                        ksort($moyenne[$key]);
                    }
                }
            }
        }
        return $moyenne;
    }
    private function getMoyenneCoursEvalSemEleve($group, $user){
        $manager= $this->getDoctrine()->getManager();
        $moyenneEvalSem = $this->getMoyenneEvaluationSemestreEleve($group);
        $moyenneCoursSem = $this->getMoyenneCoursSemestreEleve($group);

        if(empty($moyenneEvalSem)) return 0;
        if(empty($moyenneCoursSem)) return 0;
        $classes = $manager
        ->getRepository(Classe::class)
        ->findByGroups($group);
        $ponderation = $manager
        ->getRepository(Ponderation::class)
        ->findOneBy(
            [
                'ecole' => $classes[0]->getEcole()->getId(),
                'professeur' => $user
            ]
        );

        if(!empty($ponderation)){
            foreach($moyenneEvalSem as $key => $value){
                foreach($value as $k => $v){
                    $moyenne[$key][$k] = ($v*$ponderation->getEvaluation() + $moyenneCoursSem[$key][$k]*$ponderation->getCours())/100;
                }
            }
        }
        else {
            foreach($moyenneEvalSem as $key => $value){
                foreach($value as $k => $v){
                    $moyenne[$key][$k] = ($v + $moyenneCoursSem[$key][$k])/2;
                }
            }
        }

        return $moyenne;
    }

    private function getMoyenneCoursEvalAnneeEleve($group, $user){
        $manager = $this->getDoctrine()->getManager();
        $moyenneSemestre = $this->getMoyenneCoursEvalSemEleve($group, $user);
        $periodeCours= $this->getMoyenneCoursPeriodeEleve($group);
        $heuresPeriodeCours = $periodeCours['heuresPeriode'];

        if(empty($moyenneSemestre)) return 0;
        if(empty($periodeCours)) return 0;
        foreach($heuresPeriodeCours as $key => $value){
           $heuresTotalCoursSem[$key] = array_sum($value);      
        }

        $heuresTotalAnnee = array_sum($heuresTotalCoursSem);

        foreach($moyenneSemestre as $key => $value){
            foreach($value as $k => $v){
                $moyenneSem[$k][] = $v*$heuresTotalCoursSem[$key];
                $moyenne[$k] = array_sum($moyenneSem[$k])/$heuresTotalAnnee;
            }
        }

        return $moyenne;
    }

    private function applicationParametres($group, $user){
        $moyenneCoursPeriode = $this->getMoyenneCoursPeriodeEleve($group)['moyennesEleves'];
        $moyenneCoursSemestre = $this->getMoyenneCoursSemestreEleve($group);
        $moyenneChampsPeriode = $this->getMoyenneChampsPeriodeEleve($group)['moyenneEleves'];
        $moyenneChampsSemestre = $this->getMoyenneChampsSemestreEleve($group)['moyenne'];
        $moyenneEvalPeriode = $this->getMoyenneEvaluationPeriodeEleve($group);
        $moyenneEvalSemestre = $this->getMoyenneEvaluationSemestreEleve($group);
        $moyenneCoursEvalPeriode = $this->getMoyenneCoursEvalPeriodeEleve($group, $user);
        $moyenneCoursEvalSemestre = $this->getMoyenneCoursEvalSemEleve($group, $user);
        $moyenneAnnee = $this->getMoyenneCoursEvalAnneeEleve($group, $user);

        $manager = $this->getDoctrine()->getManager();
        $classes = $manager
        ->getRepository(Classe::class)
        ->findByGroups($group);
        $parametres = $manager
        ->getRepository(Parametres::class)
        ->findBy(
            [
                'ecole' => $classes[0]->getEcole()->getId(),
                'professeur' => $user
            ]
        );
        $appreciation = $manager
        ->getRepository(Appreciation::class)
        ->findBy(
            [
                'ecole' => $classes[0]->getEcole()->getId(),
                'professeur' => $user
            ]
        );
        if(!empty($parametres)){
            foreach($parametres as $key => $value){
                if(!$value->getAppreciation()){
                    switch($value->getType()){
                        case 'Periodes':
                            try {
                                foreach($moyenneCoursPeriode as $cle => $valeur){
                                    foreach($valeur as $k => $v){
                                        foreach($v as $a => $b){
                                            $moyenneCoursPeriode[$cle][$k][$a] = ($b/10)*$value->getSurCombien();
                                        }
                                    }
                                }
                                foreach($moyenneChampsPeriode as $cle => $valeur){
                                    foreach($valeur as $k => $v){
                                        foreach($v as $a => $b){
                                            foreach($b as $c => $d){
                                                $moyenneChampsPeriode[$cle][$k][$a][$c] = ($d/10)*$value->getSurCombien();
                                            }
                                        }
                                    }
                                }
                                foreach($moyenneEvalPeriode as $cle => $valeur){
                                    foreach($valeur as $k => $v){
                                        foreach($v as $a => $b){
                                            $moyenneEvalPeriode[$cle][$k][$a] = ($b/10)*$value->getSurCombien();
                                        }
                                    }
                                }
                                foreach($moyenneCoursEvalPeriode as $cle => $valeur){
                                    foreach($valeur as $k => $v){
                                        foreach($v as $a => $b){
                                            $moyenneCoursEvalPeriode[$cle][$k][$a] = ($b/10)*$value->getSurCombien();
                                        }
                                    }
                                }
                            } 
                            catch(\Exception $e){

                            }
                            
                        break;
                        
                        case 'Semestre 1': 
                            try {
                                foreach($moyenneCoursSemestre as $cle => $valeur){
                                    if($cle == "Semestre 1"){
                                        foreach($valeur as $k => $v){
                                            $moyenneCoursSemestre[$cle][$k] = ($v/10)*$value->getSurCombien();
                                        }
                                    }
                                }
                                foreach($moyenneChampsSemestre as $cle => $valeur){
                                    if($cle == "Semestre 1"){
                                        foreach($valeur as $k => $v){
                                            foreach($v as $a => $b){
                                                $moyenneChampsSemestre[$cle][$k][$a] = ($b/10)*$value->getSurCombien();
                                            }
                                        }
                                    }
                                }
                                foreach($moyenneEvalSemestre as $cle => $valeur){
                                    if($cle == "Semestre 1"){
                                        foreach($valeur as $k => $v){
                                            $moyenneEvalSemestre[$cle][$k] = ($v/10)*$value->getSurCombien();
                                        }
                                    }
                                }
                                foreach($moyenneCoursEvalSemestre as $cle => $valeur){
                                    if($cle == "Semestre 1"){
                                        foreach($valeur as $k => $v){
                                            $moyenneCoursEvalSemestre[$cle][$k] = ($v/10)*$value->getSurCombien();
                                        }
                                    }
                                }
                            }
                            catch(\Exception $e){

                            }
                        break;
                        case 'Semestre 2': 
                            try {
                                foreach($moyenneCoursSemestre as $cle => $valeur){
                                    if($cle == "Semestre 2"){
                                        foreach($valeur as $k => $v){
                                            $moyenneCoursSemestre[$cle][$k] = ($v/10)*$value->getSurCombien();
                                        }
                                    }
                                }
                                foreach($moyenneChampsSemestre as $cle => $valeur){
                                    if($cle == "Semestre 2"){
                                        foreach($valeur as $k => $v){
                                            foreach($v as $a => $b){
                                                $moyenneChampsSemestre[$cle][$k][$a] = ($b/10)*$value->getSurCombien();
                                            }
                                        }
                                    }
                                }
                                foreach($moyenneEvalSemestre as $cle => $valeur){
                                    if($cle == "Semestre 2"){
                                        foreach($valeur as $k => $v){
                                            $moyenneEvalSemestre[$cle][$k] = ($v/10)*$value->getSurCombien();
                                        }
                                    }
                                }
                                foreach($moyenneCoursEvalSemestre as $cle => $valeur){
                                    if($cle == "Semestre 2"){
                                        foreach($valeur as $k => $v){
                                            $moyenneCoursEvalSemestre[$cle][$k] = ($v/10)*$value->getSurCombien();
                                        }
                                    }
                                }
                            }
                            catch(\Exception $e){

                            }
                        break;

                        case 'Annee': 
                            try {
                                foreach($moyenneAnnee as $cle => $valeur){
                                    $moyenneAnnee[$cle] = ($valeur/10)*$value->getSurCombien();
                                }
                            }
                            catch(\Exception $e){

                            }
                        break;
                    }
                }
                else {
                    switch($value->getType()){
                        case 'Periodes': 
                            try {
                                foreach($moyenneCoursPeriode as $cle => $valeur){
                                    foreach($valeur as $k => $v){
                                        foreach($v as $a => $b){
                                            $getAppreciation = array();
                                            foreach($appreciation as $c => $d){
                                                if($d->getCote() <= $b && $d->getCote() <= $b){
                                                    $getAppreciation[$d->getCote()] = $d->getIntitule();
                                                    ksort($getAppreciation);
                                                }
                                            }
                                            $moyenneCoursPeriode[$cle][$k][$a] = end($getAppreciation);
                                        }
                                    }
                                }
                                foreach($moyenneChampsPeriode as $cle => $valeur){
                                    foreach($valeur as $k => $v){
                                        foreach($v as $a => $b){
                                            foreach($b as $c => $d){
                                                $getAppreciation = array();
                                                foreach($appreciation as $e => $f){
                                                    if($f->getCote() <= $d && $f->getCote() <= $d){
                                                        $getAppreciation[$f->getCote()] = $f->getIntitule();
                                                        ksort($getAppreciation);
                                                    }
                                                }
                                                $moyenneChampsPeriode[$cle][$k][$a][$c] = end($getAppreciation);
                                            }
                                        }
                                    }
                                }
                                foreach($moyenneEvalPeriode as $cle => $valeur){
                                    foreach($valeur as $k => $v){
                                        foreach($v as $a => $b){
                                            $getAppreciation = array();
                                            foreach($appreciation as $c => $d){
                                                if($d->getCote() <= $b && $d->getCote() <= $b){
                                                    $getAppreciation[$d->getCote()] = $d->getIntitule();
                                                    ksort($getAppreciation);
                                                }
                                            }
                                            $moyenneEvalPeriode[$cle][$k][$a] = end($getAppreciation);
                                        }
                                    }
                                }
                                foreach($moyenneCoursEvalPeriode as $cle => $valeur){
                                    foreach($valeur as $k => $v){
                                        foreach($v as $a => $b){
                                            $getAppreciation = array();
                                            foreach($appreciation as $c => $d){
                                                if($d->getCote() <= $b && $d->getCote() <= $b){
                                                    $getAppreciation[$d->getCote()] = $d->getIntitule();
                                                    ksort($getAppreciation);
                                                }
                                            }
                                            $moyenneCoursEvalPeriode[$cle][$k][$a] = end($getAppreciation);
                                        }
                                    }
                                }
                            } 
                            catch(\Exception $e){

                            }
                        break;
                        case 'Semestre 1': 
                            try {
                                foreach($moyenneCoursSemestre as $cle => $valeur){
                                    if($cle == "Semestre 1"){
                                        foreach($valeur as $k => $v){
                                            $getAppreciation = array();
                                            foreach($appreciation as $c => $d){
                                                if($d->getCote() <= $v && $d->getCote() <= $v){
                                                    $getAppreciation[$d->getCote()] = $d->getIntitule();
                                                    ksort($getAppreciation);
                                                }
                                            }
                                            $moyenneCoursSemestre[$cle][$k] = end($getAppreciation);
                                        }
                                    }
                                }
                                foreach($moyenneChampsSemestre as $cle => $valeur){
                                    if($cle == "Semestre 1"){
                                        foreach($valeur as $k => $v){
                                            foreach($v as $a => $b){
                                                $getAppreciation = array();
                                                foreach($appreciation as $c => $d){
                                                    if($d->getCote() <= $b && $d->getCote() <= $b){
                                                        $getAppreciation[$d->getCote()] = $d->getIntitule();
                                                        ksort($getAppreciation);
                                                    }
                                                }
                                                $moyenneChampsSemestre[$cle][$k][$a] = end($getAppreciation);
                                            }
                                        }
                                    }
                                }
                                foreach($moyenneEvalSemestre as $cle => $valeur){
                                    if($cle == "Semestre 1"){
                                        foreach($valeur as $k => $v){
                                            $getAppreciation = array();
                                            foreach($appreciation as $c => $d){
                                                if($d->getCote() <= $v && $d->getCote() <= $v){
                                                    $getAppreciation[$d->getCote()] = $d->getIntitule();
                                                    ksort($getAppreciation);
                                                }
                                            }
                                            $moyenneEvalSemestre[$cle][$k] = end($getAppreciation);
                                        }
                                    }
                                }
                                foreach($moyenneCoursEvalSemestre as $cle => $valeur){
                                    if($cle == "Semestre 1"){
                                        foreach($valeur as $k => $v){
                                            $getAppreciation = array();
                                            foreach($appreciation as $c => $d){
                                                if($d->getCote() <= $v && $d->getCote() <= $v){
                                                    $getAppreciation[$d->getCote()] = $d->getIntitule();
                                                    ksort($getAppreciation);
                                                }
                                            }
                                            $moyenneCoursEvalSemestre[$cle][$k] = end($getAppreciation);
                                        }
                                    }
                                }
                            }
                            catch(\Exception $e){

                            }
                        break;
                        case 'Semestre 2': 
                            try {
                                foreach($moyenneCoursSemestre as $cle => $valeur){
                                    if($cle == "Semestre 2"){
                                        foreach($valeur as $k => $v){
                                            $getAppreciation = array();
                                            foreach($appreciation as $c => $d){
                                                if($d->getCote() <= $v && $d->getCote() <= $v){
                                                    $getAppreciation[$d->getCote()] = $d->getIntitule();
                                                    ksort($getAppreciation);
                                                }
                                            }
                                            $moyenneCoursSemestre[$cle][$k] = end($getAppreciation);
                                        }
                                    }
                                }
                                foreach($moyenneChampsSemestre as $cle => $valeur){
                                    if($cle == "Semestre 2"){
                                        foreach($valeur as $k => $v){
                                            foreach($v as $a => $b){
                                                $getAppreciation = array();
                                                foreach($appreciation as $c => $d){
                                                    if($d->getCote() <= $b && $d->getCote() <= $b){
                                                        $getAppreciation[$d->getCote()] = $d->getIntitule();
                                                        ksort($getAppreciation);
                                                    }
                                                }
                                                $moyenneChampsSemestre[$cle][$k][$a] = end($getAppreciation);
                                            }
                                        }
                                    }
                                }
                                foreach($moyenneEvalSemestre as $cle => $valeur){
                                    if($cle == "Semestre 2"){
                                        foreach($valeur as $k => $v){
                                            $getAppreciation = array();
                                            foreach($appreciation as $c => $d){
                                                if($d->getCote() <= $v && $d->getCote() <= $v){
                                                    $getAppreciation[$d->getCote()] = $d->getIntitule();
                                                    ksort($getAppreciation);
                                                }
                                            }
                                            $moyenneEvalSemestre[$cle][$k] = end($getAppreciation);
                                        }
                                    }
                                }
                                foreach($moyenneCoursEvalSemestre as $cle => $valeur){
                                    if($cle == "Semestre 2"){
                                        foreach($valeur as $k => $v){
                                            $getAppreciation = array();
                                            foreach($appreciation as $c => $d){
                                                if($d->getCote() <= $v && $d->getCote() <= $v){
                                                    $getAppreciation[$d->getCote()] = $d->getIntitule();
                                                    ksort($getAppreciation);
                                                }
                                            }
                                            $moyenneCoursEvalSemestre[$cle][$k] = end($getAppreciation);
                                        }
                                    }
                                }
                            }
                            catch(\Exception $e){

                            }
                        break;

                        case 'Annee': 
                            try {
                                foreach($moyenneAnnee as $cle => $valeur){
                                    $getAppreciation = array();
                                    foreach($appreciation as $c => $d){
                                        if($d->getCote() <= $valeur && $d->getCote() <= $valeur){
                                            $getAppreciation[$d->getCote()] = $d->getIntitule();
                                            ksort($getAppreciation);
                                        }
                                    }
                                    $moyenneAnnee[$cle] = end($getAppreciation);
                                }
                            }
                            catch(\Exception $e){

                            }
                        break;
                    }
                }
            }
        }

        return array(
            'moyenneCoursPeriode' => $moyenneCoursPeriode,
            'moyenneCoursSemestre' => $moyenneCoursSemestre,
            'moyenneChampsPeriode' => $moyenneChampsPeriode,
            'moyenneChampsSemestre' => $moyenneChampsSemestre,
            'moyenneEvalPeriode' => $moyenneEvalPeriode,
            'moyenneEvalSemestre' => $moyenneEvalSemestre,
            'moyenneCoursEvalPeriode' => $moyenneCoursEvalPeriode,
            'moyenneCoursEvalSemestre' => $moyenneCoursEvalSemestre,
            'moyenneAnnee' => $moyenneAnnee
        );
    }

    private function moyennesEleve($group, $user){
        $manager = $this->getDoctrine()->getManager();
        $moyenne = $this->applicationParametres($group, $user); 
        
        $classes = $manager
        ->getRepository(Classe::class)
        ->findByGroups($group);
        
        $eleves =  $manager
        ->getRepository(Eleve::class)
        ->findByclasse($classes);

        $periodes = $manager
        ->getRepository(Periodes::class)
        ->findBygroupe($group);

        foreach($eleves as $key => $value){
            if(empty($moyenne['moyenneCoursPeriode'])) $value->addMoyennePeriodeCours([1 => "NE"]);
            else {
                foreach($moyenne['moyenneCoursPeriode'] as $cle => $valeur){
                    foreach($valeur as $k => $v){
                        foreach($periodes as $a => $b){
                            if($b->getId() == $k){
                                foreach($v as $c => $d){
                                    if($value->getId() == $c){
                                        $value->addMoyennePeriodeCours([$b->getNomPeriode() => $d]);
                                    }
                                }
                            }
                        }
                    }  
                }
            }
            if(empty($moyenne['moyenneCoursSemestre'])) $value->addMoyenneSemCours([0 => "NE"]);
            else {
                foreach($moyenne['moyenneCoursSemestre'] as $cle => $valeur){
                    foreach($valeur as $k => $v){
                        if($value->getId() == $k){
                            $value->addMoyenneSemCours([$cle => $v]);
                        }
                    }
                }
            }
            if(empty($moyenne['moyenneEvalPeriode'])) $value->addMoyennePeriodeEval([0 => "NE"]);
            else {
                foreach($moyenne['moyenneEvalPeriode'] as $cle => $valeur){
                    foreach($valeur as $k => $v){
                        foreach($periodes as $a => $b){
                            if($b->getId() == $k){
                                foreach($v as $c => $d){
                                    if($value->getId() == $c){
                                        $value->addMoyennePeriodeEval([$b->getNomPeriode() => $d]);
                                    }
                                }
                            }
                        }
                    }  
                }
            }
            if(empty($moyenne['moyenneEvalSemestre'])) $value->addMoyenneSemEval(["Semestres" => "NE"]);
            else {
                foreach($moyenne['moyenneEvalSemestre'] as $cle => $valeur){
                    foreach($valeur as $k => $v){
                        if($value->getId() == $k){
                            $value->addMoyenneSemEval([$cle => $v]);
                        }
                    }
                }
            }
            if(empty($moyenne['moyenneChampsPeriode'])) $value->addMoyenneChampPer(["Périodes" => "NE"]);
            else {
                foreach($moyenne['moyenneChampsPeriode'] as $cle => $valeur){
                    foreach($valeur as $k => $v){
                        foreach($periodes as $a => $b){
                            if($b->getId() == $k){
                                foreach($v as $c => $d){
                                    if($value->getId() == $c){
                                        $value->addMoyenneChampPer([$b->getNomPeriode() => $d]);
                                    }
                                }
                            }
                        }
                    }  
                }
            }
            if(empty($moyenne['moyenneChampsSemestre'])) $value->addMoyenneChampSem(["Semestres" => "NE"]);
            else {
                foreach($moyenne['moyenneChampsSemestre'] as $cle => $valeur){
                    foreach($valeur as $k => $v){
                        if($value->getId() == $k){
                            $value->addMoyenneChampSem([$cle => $v]);
                        }
                    }
                }
            }
            if(empty($moyenne['moyenneCoursEvalPeriode'])) $value->addMoyennePeriode(["Périodes" => "NE"]);
            else {
                foreach($moyenne['moyenneCoursEvalPeriode'] as $cle => $valeur){
                    foreach($valeur as $k => $v){
                        foreach($periodes as $a => $b){
                            if($b->getId() == $k){
                                foreach($v as $c => $d){
                                    if($value->getId() == $c){
                                        $value->addMoyennePeriode([$b->getNomPeriode() => $d]);
                                    }
                                }
                            }
                        }
                    }  
                }
            }
            if(empty($moyenne['moyenneCoursEvalSemestre'])) $value->addMoyenneSem(["Semestres" => "NE"]);
            else {
                foreach($moyenne['moyenneCoursEvalSemestre'] as $cle => $valeur){
                    foreach($valeur as $k => $v){
                        if($value->getId() == $k){
                            $value->addMoyenneSem([$cle => $v]);
                        }
                    }
                }
            }
            if(empty($moyenne['moyenneAnnee'])) $value->addMoyenneAnnee([0 => "NE"]);
            else {
                foreach($moyenne['moyenneAnnee'] as $cle => $valeur){
                    if($value->getId() == $cle){
                        $value->addMoyenneAnnee([$cle => $valeur]);
                    }
                }
            }   
        }

        return $eleves;
    }

    /**
     * @Route("test/{group}/{user}", name="test")
     */
    public function appliMoyennes($group, $user){
        $moyennes = $this->moyennesEleve($group, $user);
        foreach($moyennes as $key => $value){
            $eleves[$key]['Nom'] = $value->getNom();
            $eleves[$key]['Prenom'] = $value->getPrenom();
            $eleves[$key]['Classe'] = $value->getClasse()->getNomClasse();
            foreach($value->getMoyennePeriode() as $k => $v){
                foreach($v as $a => $b){
                    $eleves[$key][$a] = $b;
                }
            }
            foreach($value->getMoyenneSem() as $k => $v){
                foreach($v as $a => $b){
                    $eleves[$key][$a] = $b;
                }
            }
            foreach($value->getMoyenneAnnee() as $k => $v){
                foreach($v as $a => $b){
                    $eleves[$key]['Annee'] = $b;
                }
            }
        }
        return new JsonResponse($eleves);
    }
}