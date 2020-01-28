<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class homeController extends AbstractController {
    
    /**
     * @Route("/", name="homepage")
     */

    public function home(){
        return $this->render(
            'base.html.twig'
        );
        
    }

    /**
     * @Route("/introduction", name="introduction")
     */

    public function introduction(){
         return $this->render(
             '/contenu/introduction.html.twig'
         );
    }

    /**
     * @Route("/genese", name="genese")
     */

    public function genese(){
        return $this->render(
            '/contenu/genese.html.twig'
        );
    }

    /**
     * @Route("/qui-suis-je", name="qui")
     */
    public function quiSuisJe(){
        return $this->render(
            '/contenu/quiSuisJe.html.twig'
        );
    }

    /**
     * @Route("/objectifs", name="objectifs")
     */
    public function objectifs(){
        return $this->render(
            '/contenu/objectif.html.twig'
        );
    }

    /**
     * @Route("/journalClasse", name="journalClasse")
     */
    public function journalClasse(){
        return $this->render(
            '/contenu/journal.html.twig'
        );    
    }

    /**
     * @Route("/agenda", name="agenda")
     */
    public function agenda(){
        return $this->render(
            '/contenu/agenda.html.twig'
        );
    }

    /**
     * @Route("/cahierCotes", name="cahier")
     */
    public function cahier(){
        return $this->render(
            '/contenu/cahierCotes.html.twig'
        );
    }

    /**
     * @Route("/planning", name="planning")
     */
    public function planning(){
        return $this->render(
            '/contenu/planning.html.twig'
        );
    }

        /**
     * @Route("/parametres", name="parametres")
     */
    public function parametres(){
        return $this->render(
            '/contenu/parametres.html.twig'
        );
    }

    /**
     * @Route("/vma", name="vma")
     */
    public function vma(){
        return $this->render(
            '/contenu/vma.html.twig'
        );
    }

        /**
     * @Route("/course", name="course")
     */
    public function course(){
        return $this->render(
            '/contenu/course.html.twig'
        );
    }

    /**
     * @Route("/evaluation", name="evaluation")
     */
    public function evaluation(){
        return $this->render(
            '/contenu/evaluation.html.twig'
        );
    }

    /**
     * @Route("/application", name="application")
     */
    public function application(){
        return $this->render(
            '/contenu/appli.html.twig'
        );
    }

    /**
     * @Route("/formations", name="formations")
     */
    public function formation(){
        return $this->render(
            '/contenu/formation.html.twig'
        );
    }

    /**
     * @Route("/descriptionOutilsGestion", name="descriptionOutilsGestion")
     */
    public function descriptionOutilsGestion(){
        return $this->render(
            '/contenu/outilsGestionClasse.html.twig'
        );
    }

    /**
     * @Route("/descriptionVMA", name="descriptionVMA")
     */
    public function descriptionVMA(){
        return $this->render(
            '/contenu/outilsVMA.html.twig'
        );
    }

}