<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\ContactType;
use App\Form\InscriptionType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class easyPepsController extends AbstractController {
    
    /**
     * @Route("/connexion", name="connexion_user")
     */
    public function connexion(AuthenticationUtils $utils){
        
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render(
            'forms/connexion.html.twig',
            [
                'error' => $error !== null,
                'username' => $username
            ]
        );
    }

    /**
     * @Route("/deconnexion", name="deconnexion_user")
     *
     * @return void
     */
    public function deconnexion(){

    }

    /**
     * @Route("/inscription", name="inscription_user")
     */
    public function inscription(Request $request){
        $user = new User();

        $formInscription = $this->createForm(InscriptionType::class, $user);

        $form->handleRequest($request);
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
