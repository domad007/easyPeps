<?php

namespace App\Controller;

use App\Entity\Presences;
use App\Entity\Competences;
use App\Entity\CustomizedPresences;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @Route("application", name="application")
     */
    public function application(){
        return $this->render(
            'contenu/application.html.twig'
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

    /**
     * @Route("CGU", name="cgu")
     */
    public function CGU(){
        return $this->render(
            'contenu/CGU.html.twig'
        );
    }

    /**
     * @Route("/descriptionCompSocles", name="description_comp_socles")
     */
    public function descriptionCompSocles(){

        $getCompetences = $this->getDoctrine()
        ->getRepository(Competences::class)
        ->findBydegre(1);

        return $this->render(
            'contenu/descriptionCompSocles.html.twig',
            [
                'competences' => $getCompetences
            ]
        );
    }

    /**
     * @Route("/descriptionCompTerminales", name="description_comp_terminales")
     */
    public function descriptionCompTerminales(){

        $getCompetences = $this->getDoctrine()
        ->getRepository(Competences::class)
        ->findBydegre(2);

        return $this->render(
            'contenu/descriptionCompTerminales.html.twig',
            [
                'competences' => $getCompetences
            ]
        );
    }

    /**
     * @Route("/descriptionPresences", name="description_presences")
     * @Security("is_granted('ROLE_ACTIF')", statusCode=405)
     */
    public function descriptionPresecnes(UserInterface $user){
        $getPresences = $this->getDoctrine()
        ->getRepository(Presences::class)
        ->findAll();

       $getPresencesCustomized =  $this->getDoctrine()
       ->getRepository(CustomizedPresences::class)
       ->findBy(
           [
               'user' => $user->getId()
           ]
       );

        return $this->render(
            'contenu/descriptionPresences.html.twig',
            [
                'presences' => $getPresences,
                'presencesCustomized' =>  $getPresencesCustomized
            ]
        );
    }

    /**
     * @Route("/presencesCustomizedd", name="presences_customizedd")
     */
    public function presencesCustomized(Request $request, UserInterface $user){
        $manager = $this->getDoctrine()->getManager();
        if($request->isMethod('post')){
            $presence = $request->request->all();
            $csrf = $presence['name'];

            if($this->isCsrfTokenValid('modif_presence', $csrf) == true){

                $getPresenceCustomized = $manager
                ->getRepository(CustomizedPresences::class)
                ->findOneById($presence['pk']);

                if(!empty($getPresenceCustomized)){
                    $getPresenceCustomized->setAbreviationCustomized($presence['value']);
                    $manager->persist($getPresenceCustomized);
                    $manager->flush();
                }
                else {
                    $getTypePresence = $manager
                    ->getRepository(Presences::class)
                    ->findAll();                

                    foreach($getTypePresence as $key => $value){
                        $newPresenceCustomized = new CustomizedPresences();
                        $newPresenceCustomized
                        ->setTypePresence($value)
                        ->setUser($user)
                        ->setAbreviationCustomized($value->getAbreviation());
                        $manager->persist($newPresenceCustomized);
                    }
                    $manager->flush();

                    $setCustomPresence = $manager
                    ->getRepository(CustomizedPresences::class)
                    ->findOneBy(
                        [
                            'typePresence' => $presence['pk'],
                            'user' => $user->getId()
                        ]
                    );
                    $setCustomPresence
                    ->setAbreviationCustomized($presence['value']);
                    $manager->persist($setCustomPresence);
                    $manager->flush();

                }
            }

        }
        return new Response("");
    }

}