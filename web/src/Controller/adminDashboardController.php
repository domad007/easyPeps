<?php

namespace App\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class adminDashboardController extends AbstractController {
    
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function adminDashboard(){
        $manager = $this->getDoctrine()->getManager();
        $countUsers = $manager->createQueryBuilder();
        $countUsers
        ->select('count(u.id)')
        ->from('App:User', 'u');
        $countUsersResult = $countUsers->getQuery()->getSingleScalarResult();
        return $this->render(
            'admin/dashboard/dashboard.html.twig',
            [
                'totalUsers' => $countUsersResult
            ]
        );
    }
}