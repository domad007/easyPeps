<?php

namespace App\Controller;

use App\Entity\Ecole;
use App\Entity\Classe;
use App\Entity\Groups;
use App\Entity\Ponderation;
use App\Form\NewPonderationType;
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

        return $this->render(
            'parametres/parametres.html.twig',
            [
                'ponderation' => $ponderation,
                'ecole' => $ecole
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
            dump($somme);
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
}