<?php

namespace App\Controller;
use App\Form\ContactType;
use App\Form\ConnexionType;
use App\Form\InscriptionType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class easyPepsController extends AbstractController {
    
    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion(){
        $formConnexion = $this->createForm(ConnexionType::class);
        return $this->render(
            'forms/connexion.html.twig',
            [
                'form' => $formConnexion->createView()
            ]
        );
    }

    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription(){
        $formInscription = $this->createForm(InscriptionType::class);
        return  $this->render(
            'forms/inscription.html.twig', 
            [
                'form' => $formInscription->createView()
            ]
        );
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(){
        $formContact = $this->createForm(ContactType::class);

        return $this->render(
            'forms/contact.html.twig',
            [
                'form' => $formContact->createView()
            ]
        );
    }
}
