<?php 

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class groupsController extends AbstractController {
    
    /**
     * @Route("/groups", name="groups")
     */
    public function groups(){
        return $this->render(
            'groupes/groups.html.twig'
        );
    }

    /**
     * @Route("/newGroup", name="new_group")
     */
    public function newGroup(){
        return $this->render(
            'groupes/newGroup.html.twig'
        );
    }

    /**
     * @Route("/group", name="group")
     */

    public function group(){
        return $this->render(
            'groupes/group.html.twig'
        );
    }
}