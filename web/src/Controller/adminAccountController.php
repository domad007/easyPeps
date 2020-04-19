<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class adminAccountController extends AbstractController {
    
    /**
     * @Route("admin/login", name="admin_login")
     */
    public function adminLogin(){
        return $this->render(
            'admin/account/adminAccount.html.twig'
        );
    }
}