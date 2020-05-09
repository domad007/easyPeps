<?php

namespace App\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class adminDashboardController extends AbstractController {
    
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function adminDashboard(){
        return $this->render(
            'admin/dashboard/dashboard.html.twig'
        );
    }
}