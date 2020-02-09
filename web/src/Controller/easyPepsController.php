<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\MdpType;
use App\Entity\ModifMdp;
use App\Form\CompteType;
use App\Form\ContactType;
use App\Form\ModifMdpType;
use App\Form\InscriptionType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

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
    public function mdpOublie(Request $request,\Swift_Mailer $mailer){
        return $this->render(
            'forms/mdpOublie.html.twig'
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
            
            $this->addFlash('success', "Vous avez été inscrit veuillez passer à la connexion");
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
    public function contact(Request $request,\Swift_Mailer $mailer){
        $formContact = $this->createForm(ContactType::class);

        $formContact->handleRequest($request);
        if($formContact->isSubmitted() && $formContact->isValid()){
            $data = $formContact->getData();
            dump($data);
            $message = (new \Swift_Message('Contact avec administrateur'))
                ->setFrom($data['mail'])
                ->setTo('dominikfiedorczuk69@gmail.com')
                ->setBody(
                    'Nom: ' .$data['nom'].'<br>'. 
                    'Prénom: '. $data['prenom'] .'<br>'. 
                    'Adresse de contact: ' . $data['mail'].'<br>'.
                    'Message: '. $data['commentaire'],
                    'text/html'
                );

            $mailer->send($message);
            $this->addFlash('success', 'Votre message a été envoyé, nous essayerons de vous répondre au plus vite');
        }

        return $this->render(
            'forms/contact.html.twig',
            [
                'form' => $formContact->createView()
            ]
        );
    }

    /**
     * Undocumented function
     *
     * @Route("/compte/profil", name="mon_profil")
     */
    public function modifProfil(Request $request){
        $user = $this->getUser();
        $profilForm = $this->createForm(CompteType::class, $user);
        $profilForm->handleRequest($request);

        if($profilForm->isSubmitted() && $profilForm->isValid()){
            $manager = $this->getDoctrine()->getManager();

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données ont bien été enregistrées"
            );
        }    
        return $this->render(
           'forms/profile.html.twig', [
               'form' => $profilForm->createView()
           ]
       );
    }

    /**
     * @Route("/compte/mdpOublie", name="mon_mdp")
     */
    public function modifMdp(Request $request, UserPasswordEncoderInterface $encoder){
        $mdp = new ModifMdp();
        $user = $this->getUser();
        $formMdp = $this->createForm(ModifMdpType::class, $mdp);
        $formMdp->handleRequest($request);


        if($formMdp->isSubmitted() && $formMdp->isValid()){
            $manager = $this->getDoctrine()->getManager();

            if(!password_verify($mdp->getOldPassword(), $user->getMdp())){
                $this->addFlash(
                    'danger',
                    "Votre ancien mot de passe ne correspond pas au mot de passe actuel"
                );
            }
            else {
                $newMdp= $mdp->getNewPassword();
                $modifMdp= $encoder->encodePassword($user, $newMdp);
                $user->setMdp($modifMdp);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a été modifié"
                );
            }
        }
        return $this->render(
            'forms/modifMdp.html.twig', [
               'form' => $formMdp->createView()
            ]
        );
    }
}
