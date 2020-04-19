<?php

namespace App\Controller;
use App\Entity\Role;
use App\Entity\User;
use App\Form\MdpType;
use App\Entity\ModifMdp;
use App\Form\CompteType;
use App\Form\ContactType;
use App\Form\ModifMdpType;
use App\Form\MdpOublieType;
use App\Form\InscriptionType;
use App\Entity\ModifMdpOublie;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class accountController extends AbstractController {
    

    /**
     * Inscription de l'utilisateur
     * @Route("/inscription", name="inscription_user")
     */
    public function inscription(Request $request, UserPasswordEncoderInterface $encoder){
        $user = new User();
        $formInscription = $this->createForm(InscriptionType::class, $user);
        $formInscription->handleRequest($request);

        if($formInscription->isSubmitted() && $formInscription->isValid()){
            $manager = $this->getDoctrine()->getManager();
            $role = $manager
            ->getRepository(Role::class)
            ->findOneBytitle("ROLE_ACTIF");
            $mdp = $encoder->encodePassword($user, $user->getMdp());
            $user->setMdp($mdp);
            $user->setUserActif(0);
            $user->addUserRole($role);
            

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', "Vous avez été inscrit veuillez passer à la connexion");
            return $this->redirectToRoute("connexion_user");
        }

        return  $this->render(
            'account/inscription.html.twig', 
            [
                'form' => $formInscription->createView()
            ]
        );
    }

    /**
     * Formulaire de connexion
     * @Route("/connexion", name="connexion_user")
     */
    public function connexion(AuthenticationUtils $utils){
        
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render(
            'account/connexion.html.twig',
            [
                'error' => $error !== null,
                'username' => $username
            ]
        );
    }

    /**
     * Modificaiton du profil
     * Modifie juste les données personnels 
     * Ne modifie pas le mot de passe
     *
     * @Route("/compte/profil", name="mon_profil")
     * @Security("is_granted('ROLE_USER')")
     */
    public function modifProfil(Request $request){
        $user = $this->getUser();
        $profilForm = $this->createForm(CompteType::class, $user);
        $profilForm->handleRequest($request);

        if($profilForm->isSubmitted() && $profilForm->isValid()){
            $manager = $this->getDoctrine()->getManager();
            if($user->getUserActif() === true){
                $roleInactif = $manager
                ->getRepository(Role::class)
                ->findOneBytitle("ROLE_INACTIF");

                $roleActif = $manager
                ->getRepository(Role::class)
                ->findOneBytitle("ROLE_ACTIF");


                $user->addUserRole($roleInactif);
                $user->removeUserRole($roleActif);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', "Votre compte a bien été désactivé");
                return $this->redirectToRoute('homepage');
            }
            else {
                $roleInactif = $manager
                ->getRepository(Role::class)
                ->findOneBytitle("ROLE_INACTIF");
                $roleActif =  $manager
                ->getRepository(Role::class)
                ->findOneBytitle("ROLE_ACTIF");

                $user->addUserRole($roleActif);
                $user->removeUserRole($roleInactif);
                
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', "Votre profil a bien été modifié");
                return $this->redirectToRoute('homepage');

            }

            $this->addFlash(
                'success',
                "Les données ont bien été enregistrées"
            );
        }    
        return $this->render(
           'account/profile.html.twig', [
               'form' => $profilForm->createView()
           ]
       );
    }

    /**
     * Modification du mot de passe
     * @Route("/compte/mdpOublie", name="mon_mdp")
     * @Security("is_granted('ROLE_USER')")
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
            'account/modifMdp.html.twig', [
               'form' => $formMdp->createView()
            ]
        );
    }

     /**
     * Deconnexion de l'utilisateur
     * @Route("/deconnexion", name="deconnexion_user")
     *
     * @return void
     */
    public function deconnexion(){

    }

    /**
     * Récuperation du mot de passe en tant que membre déconnecté
     * Envoi d'un mail avec un lien qui contient le token qui se trouve temporairement dans la BDD
     * @Route("/mdpOublie", name="mdpOublie")
     */
    public function mdpOublie(Request $request, \Swift_Mailer $mailer, UserPasswordEncoderInterface $encoder, TokenGeneratorInterface $tokenGenerator){
        if ($request->isMethod('POST')) {

            $email = $request->request->get('email');

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)
            ->findOneByMail(
                $email
            );

            if ($user === null) {
                $this->addFlash('danger', 'Votre email est inconnu');
                return $this->redirectToRoute('mdpOublie');
            }
            
            else {
                $token = $tokenGenerator->generateToken();              
                $user->setResetToken($token);
                $entityManager->flush();
                $url = $this->generateUrl('modif_mdp_oublie', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

                $message = (new \Swift_Message('Mot de passe oublié'))
                    ->setFrom('dominikfiedorczuk69@gmail.com')
                    ->setTo($user->getMail())
                    ->setBody(
                        "Voici le lien pour réinitialiser votre mot de passe : " . $url,
                        'text/html'
                    );

                $mailer->send($message);

                $this->addFlash('success', 'Un lien de récupération de mot de passe a été envoyé');

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render(
            'account/mdpOublie.html.twig'
        );
    }
    /**
     * Changement du mot de passe après la réception du token par email
     * Le changement se fait via le user non connecté
     * @Route("/modifMdpOublie/{token}", name="modif_mdp_oublie")
     * @return void
     */
    public function modifMdpOublie(Request $request, $token, UserPasswordEncoderInterface $passwordEncoder){
        $mdp = new ModifMdp();
        $formMdpOublie = $this->createForm(MdpOublieType::class, $mdp);
        $formMdpOublie->handleRequest($request);
        
        if ($formMdpOublie->isSubmitted() && $formMdpOublie->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $user = $entityManager->getRepository(User::class)
            ->findOneByResetToken(
                $token
            );

            if ($user === null) {
                $this->addFlash('danger', 'Le token semble inconnu');
                return $this->redirectToRoute('homepage');
            }

            $user->setResetToken(null);
            $newMdp = $passwordEncoder->encodePassword($user, $mdp->getNewPassword());
            $user->setMdp($newMdp);

            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été mis à jour');

            return $this->redirectToRoute('homepage');
        }
        
        else {
            return $this->render(
                'account/modifMdpOublie.html.twig', [
                    'form' => $formMdpOublie->createView(),
                    'token' => $token
                ]
            );
        }
    }

    /**
     * Formulaire de contact
     * Envoi un mail chez l'administrateur
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request,\Swift_Mailer $mailer){
        $formContact = $this->createForm(ContactType::class);

        $formContact->handleRequest($request);
        if($formContact->isSubmitted() && $formContact->isValid()){
            $data = $formContact->getData();
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
            'account/contact.html.twig',
            [
                'form' => $formContact->createView()
            ]
        );
    }
}
