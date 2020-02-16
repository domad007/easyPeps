<?php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class classesController extends AbstractController {

    /**
     * @Route("/classes", name="classes")
     */
    public function classes(){
        return $this->render(
            ''
        );
    }

}