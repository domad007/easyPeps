<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Eleve;
use App\Entity\Classe;
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

        dump($eleves);
        return $this->render(
            'journalDeClasse/journal.html.twig', 
            [
                'cours' => $getCours,
                'eleves' => $eleves
            ]
        );
    }
}