<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class adminAccountController extends AbstractController {
    
    /**
     * Connexion d'administrateur
     * Lorsque l'authentification fail l'utilisateur obtient un message d'erreur
     * @Route("admin/login", name="admin_login")
     */
    public function adminLogin(AuthenticationUtils $utils){
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render(
            'admin/account/adminLogin.html.twig',
            [
                'error' => $error !== null,
                'username' => $username
            ]
        );
    }

}