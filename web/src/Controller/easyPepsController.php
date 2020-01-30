<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\MdpType;
use App\Form\ContactType;
use App\Form\InscriptionType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @Route("/mdpOublie", name="mdpOublie")
     */
    public function mdpOublie(){
        $user = new User();
        $formMdp = $this->createForm(MdpType::class, $user);
        return $this->render(
            'forms/mdpOublie.html.twig',
            [
                'form' => $formMdp->createView()
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
    public function inscription(Request $request, UserPasswordEncoderInterface $encoder){
        $user = new User();
        $formInscription = $this->createForm(InscriptionType::class, $user);
        $formInscription->handleRequest($request);

        if($formInscription->isSubmitted() && $formInscription->isValid()){
            $manager = $this->getDoctrine()->getManager();

            $mdp = $encoder->encodePassword($user, $user->getMdp());
            $user->setMdp($mdp);

            $manager->persist($user);
            $manager->flush();
            
            return $this->redirectToRoute("connexion_user");
        }

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
