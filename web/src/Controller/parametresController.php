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
     * Choix auquel des ecoles on souhaite effectuer le paramètrage
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
     * Affichage des parametres par rapport à l'école
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

        if(empty($parametres)){
            $this->createParametres($ecole);
        }

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
     * Création d'une nouvelle pondération entre le cours et l'évaluation
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
     * Création des appréciations pour l'école
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
     * Modificaiton des pondérations cours évaluation
     * @Route("/modifPonderation", name="modif_ponderation")
     */
    public function modifPonderation(Request $request){
        $manager = $this->getDoctrine()->getManager();
        if($request->isMethod('post')){
            $data = $request->request->all();
            $csrf = $data['name'];

            $getPonderation = $manager
            ->getRepository(Ponderation::class)
            ->findOneById($data['pk']);

            if($this->isCsrfTokenValid('ponderation_cours', $csrf)){
                $getPonderation->setCours($data['value']);
                $eval = 100-$data['value'];
                $getPonderation->setEvaluation($eval);
            }

            if($this->isCsrfTokenValid('ponderation_evaluation', $csrf)){
                $getPonderation->setEvaluation($data['value']);
                $cours = 100-$data['value'];
                $getPonderation->setCours($cours);
            }

            $manager->persist($getPonderation);
            $manager->flush();
        }

        return new Response("");
    }

    /**
     * Modifcaition des appréciations crées
     * @Route("/modifAppreciation", name="modif_appreciation")
     */
    public function modifAppreciation(Request $request){
        $manager = $this->getDoctrine()->getManager();
        if($request->isMethod('post')){
            $data = $request->request->all();
            $csrf = $data['name'];

            $getAppreciation = $manager
            ->getRepository(Appreciation::class)
            ->findOneById($data['pk']);

            if($this->isCsrfTokenValid('modif_appreciation', $csrf)){
                $getAppreciation->setIntitule($data['value']);
            }
            if($this->isCsrfTokenValid('modif_cote', $csrf)){
                $getAppreciation->setCote($data['value']);
            }

            $manager->persist($getAppreciation);
            $manager->flush();
        }

        return new Response("");
    }

    /**
     * Modifciation sur combien on veut afficher les points dans le cahier de cotes
     * @Route("/modifSurCombien", name="modif_surCombien")
     */
    public function modifSurCombien(Request $request){
        $manager = $this->getDoctrine()->getManager();
        if($request->isMethod('post')){
            $data = $request->request->all();
            $csrf = $data['name'];

            $parametre = $manager
            ->getRepository(Parametres::class)
            ->findOneById($data['pk']);

            if($this->isCsrfTokenValid('modif_points', $csrf)){
                $parametre->setSurCombien($data['value']);
            }

            $manager->persist($parametre);
            $manager->flush();
        }

        return new Response("");
    }

    /**
     * Modificaiton des appréciation pour les visualiser dans le cahier de cotes
     * @Route("/modifAppreciationCahier", name="modif_appreciation_cahier")
     */
    public function modifAppreciationCahier(Request $request){
        $manager = $this->getDoctrine()->getManager();
        if($request->isMethod('post')){
            $data = $request->request->all();
            $csrf= $data['csrf'];
            $parametre = $manager
            ->getRepository(Parametres::class)
            ->findOneById($data['id']);
            
            if($this->isCsrfTokenValid('modif_app', $csrf)){
                $parametre->setAppreciation($data['appreciation']);
            }

            $manager->persist($parametre);
            $manager->flush();
        }
        
        return new Response("");
    }

    /**
     * Création des paramètres par défaut pour chaque utilisateur voulant accèder pour la première fois au paraètrage
     *
     * @param [type] $ecole
     * @return void
     */
    private function createParametres($ecole){
        $manager = $this->getDoctrine()->getManager();

        $parametresPeriodes = new Parametres();
            $parametresPeriodes
            ->setEcole($ecole)
            ->setProfesseur($this->getUser())
            ->setType("Periodes")
            ->setAppreciation(false)
            ->setSurCombien(10);

            $manager->persist($parametresPeriodes);

            $parametresSemestre1 = new Parametres();
            $parametresSemestre1
            ->setEcole($ecole)
            ->setProfesseur($this->getUser())
            ->setType("Semestre 1")
            ->setAppreciation(false)
            ->setSurCombien(10);

            $manager->persist($parametresSemestre1);

            $parametresSemestre2 = new Parametres();
            $parametresSemestre2
            ->setEcole($ecole)
            ->setProfesseur($this->getUser())
            ->setType("Semestre 2")
            ->setAppreciation(false)
            ->setSurCombien(10);

            $manager->persist($parametresSemestre2);

            $parametresAnnee = new Parametres();
            $parametresAnnee
            ->setEcole($ecole)
            ->setProfesseur($this->getUser())
            ->setType("Annee")
            ->setAppreciation(false)
            ->setSurCombien(10);

            $manager->persist($parametresAnnee);
        

            $manager->flush();
    }
  
}