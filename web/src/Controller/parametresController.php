<?php

namespace App\Controller;

use App\Entity\Ecole;
use App\Entity\Classe;
use App\Entity\Groups;
use App\Entity\Parametres;
use App\Entity\Ponderation;
use App\Entity\Appreciation;
use App\Form\NewPonderationType;
use App\Form\NewAppreciationEcoleType;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class parametresController extends AbstractController {


    /**
     * @Route("/parametresChoix", name="parametres_choix")
     * @Security("is_granted('ROLE_ACTIF')", statusCode=405)
     */
    public function choixParametres(){
        $manager = $this->getDoctrine()->getManager();
        $getClasses =  $manager
        ->getRepository(Classe::class)
        ->findByprofesseur($this->getUser());
        $ecoles = array();

        foreach($getClasses as $key => $value){
            if(!in_array($value->getEcole(), $ecoles)){
                array_push($ecoles, $value->getEcole());       
            }
        }
        

        return $this->render(
            'parametres/choixParametres.html.twig',
            [
                'ecoles' => $ecoles
            ]
        );
    }

    /**
     * @Route("/parametresEcole/{ecole}", name="parametres_ecole")
     * @Security("is_granted('ROLE_ACTIF')", statusCode=405)
     */
    public function parametres(Ecole $ecole){
        $manager = $this->getDoctrine()->getManager();
        $ponderation = $manager
        ->getRepository(Ponderation::class)
        ->findOneBy(
            [
                'ecole' => $ecole,
                'professeur' => $this->getUser()
            ]
        );

        $appreciation = $manager
        ->getRepository(Appreciation::class)
        ->findBy(
            [
                'ecole' => $ecole,
                'professeur' => $this->getUser()
            ]
        );

        $parametres =  $manager
        ->getRepository(Parametres::class)
        ->findBy(
            [
                'ecole' => $ecole,
                'professeur' => $this->getUser()
            ]
        );

        /*if(empty($parametres)){
            $this->createParametres($ecole);
        }*/

        return $this->render(
            'parametres/parametres.html.twig',
            [
                'ponderation' => $ponderation,
                'appreciation' => $appreciation,
                'ecole' => $ecole,
                'parametres' => $parametres
            ]
        );
    }

    /**
     * @Route("creationPonderation/{ecole}", name="creation_ponderation")
     */
    public function creationPonderation(Ecole $ecole, Request $request){
        $ponderation = new Ponderation();

        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(NewPonderationType::class, $ponderation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $ponderation->setProfesseur($this->getUser());
            $ponderation->setEcole($ecole);

            $somme = $data->getEvaluation()+$data->getCours();
            if($somme != 100){
                $this->addFlash('error', "La somme de l'évaluation et du cours ne corresponds pas au total de 100%");
            }
            else {
                $manager->persist($ponderation);
                $manager->flush();
                
                $this->addFlash('success', "Votre pondération a bien été crée");
                return $this->redirectToRoute('parametres_ecole', [
                    'ecole' => $ecole->getId()
                ]);
            }

        }

        return $this->render(
            'parametres/creationPonderation.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("creationAppreciations/{ecole}", name="creation_appreciations")
     */
    public function creationAppreciations(Ecole $ecole, Request $request){
        $manager = $this->getDoctrine()->getManager();
        $appreciationEcole = new Ecole();

        $form = $this->createForm(NewAppreciationEcoleType::class, $appreciationEcole);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach($appreciationEcole->getAppreciations() as $appreciation){
                $appreciation
                ->setEcole($ecole)
                ->setProfesseur($this->getUser());
                $manager->persist($appreciation);
            }

            $manager->flush();

            $this->addFlash('success', "Vos appréciations ont été crées avec succes");

            return $this->redirectToRoute('parametres_ecole', [
                'ecole' => $ecole->getId()
            ]);
        }

        return $this->render(
            'parametres/creationAppreciation.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/modifPonderation", name="modif_ponderation")
     */
    public function modifPonderation(Request $request){
        $manager = $this->getDoctrine()->getManager();
        if($request->isMethod('post')){
            $data = $request->request->all();
            $getPonderation = $manager
            ->getRepository(Ponderation::class)
            ->findOneById($data['pk']);

            switch($data['name']){
                case 'cours': 
                    $getPonderation->setCours($data['value']);
                    $eval = 100-$data['value'];
                    $getPonderation->setEvaluation($eval);
                break;
                case 'evaluation': 
                    $getPonderation->setEvaluation($data['value']);
                    $cours = 100-$data['value'];
                    $getPonderation->setCours($cours);
                break;
            }

            $manager->persist($getPonderation);
            $manager->flush();
        }

        return new Response("");
    }

        /**
     * @Route("/modifAppreciation", name="modif_appreciation")
     */
    public function modifAppreciation(Request $request){
        $manager = $this->getDoctrine()->getManager();
        if($request->isMethod('post')){
            $data = $request->request->all();
            $getAppreciation = $manager
            ->getRepository(Appreciation::class)
            ->findOneById($data['pk']);

            switch($data['name']){
                case 'intitule': 
                    $getAppreciation->setIntitule($data['value']);
                break;
                case 'cote': 
                    $getAppreciation->setCote($data['value']);
                break;
            }

            $manager->persist($getAppreciation);
            $manager->flush();
        }

        return new Response("");
    }

    private function createParametres($ecole){
        $manager = $this->getDoctrine()->getManager();
        $parametresPeriodes = new Parametres();
            $parametresPeriodes
            ->setEcole($ecole)
            ->setProfesseur($this->getUser())
            ->setType("Periodes")
            ->setAppreciation(false)
            ->setSurCombien(10);

            $parametresSemestres = new Parametres();
            $parametresSemestres
            ->setEcole($ecole)
            ->setProfesseur($this->getUser())
            ->setType("Semestres")
            ->setAppreciation(false)
            ->setSurCombien(10);

            $parametresAnnee = new Parametres();
            $parametresAnnee
            ->setEcole($ecole)
            ->setProfesseur($this->getUser())
            ->setType("Annee")
            ->setAppreciation(false)
            ->setSurCombien(10);

            $manager
            ->persist($parametresAnnee)
            ->persist($parametresPeriodes)
            ->persist($parametresSemestres);

            $manager->flush();

    }
  
}