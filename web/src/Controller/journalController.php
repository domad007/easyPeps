<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\Classe;
use App\Entity\CoursGroupe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class journalController extends AbstractController {

    /**
     * @Route("/journalDeCalsse/{idGroup}", name="journal_de_classe")
     */
    public function journalDeCalsse($idGroup){
        $manager = $this->getDoctrine()->getManager();
        $getCours = $manager
        ->getRepository(Cours::class)
        ->findBygroupe($idGroup);

        $group = $manager
        ->getRepository(Classe::class)
        ->findByGroups($idGroup);

        foreach($group as $key => $value){           
            $eleves [] = $manager
            ->getRepository(Eleve::class)
            ->findBy(
                [
                    'classe' => $value->getId()
                ]
            );
        }

        //dump($getCours);
        return $this->render(
            'journalDeClasse/journal.html.twig', 
            [
                'cours' => $getCours,
                'eleves' => $eleves
            ]
        );
    }

    /**
     * @Route("/modifPoints", name="modif_points")
     */
    public function modifPoints(Request $request){
        $manager = $this->getDoctrine()->getManager();
        $cours = new CoursGroupe();

        if($request->isMethod('post')){
            $infos =  $request->request->all();

            $getCoursGroupe = $manager
            ->getRepository(CoursGroupe::class)
            ->findOneBy(
                [
                    'coursId' => $infos['pk'],
                    'eleveId' => $infos['name'],
                ]
            );

            $getCoursGroupe->setPoints($infos['value']);
            $manager->persist($getCoursGroupe);
            $manager->flush();

            
        }
        return new Response("");
    }

    /**
     * @Route("/presences", name="presence_eleve")
     */
    public function presenceEleve(){

        return new Response("");
    }
}