<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class easyPepsController extends AbstractController {
    
    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion(){
        return $this->render(
            'forms/connexion.html.twig'
        );
    }

    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription(){
        return  $this->render(
            'forms/inscription.html.twig'
        );
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(){
        return $this->render(
            'forms/contact.html.twig'
        );
    }
}
