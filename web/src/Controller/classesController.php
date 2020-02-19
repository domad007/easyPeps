<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Ecole;
use App\Entity\Classe;
use App\Form\NewClassType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class classesController extends AbstractController {

    /**
     * Affichage des classes selon l'utilisateur
     * @Route("/classes", name="classes")
     */
    public function classes(UserInterface $user){
         $classes = $this->getDoctrine()
        ->getRepository(Classe::class)
        ->findBy(
            [
                'professeur' => $user->getId()
            ]
        );

        return $this->render(
            'classes/mesClasses.html.twig',
            [
                'classes' => $classes
            ]
        );
    }
    /**
     * Formulaire permettant d'ajouter une nouvelle classe
     * @Route("/classes/newClass", name="newClass")
     */
    public function newClass(Request $request){
        $class = new Classe();
        $user = $this->getUser();
        
        $formNewClass = $this->createForm(NewClassType::class, $class);
        $formNewClass->handleRequest($request);
        
        if($formNewClass->isSubmitted() && $formNewClass->isValid()){
            $manager = $this->getDoctrine()->getManager();

            $class->setProfesseur($user);
            $manager->persist($class);
            $manager->flush();
            $class->setProfesseur($user);

            $manager->persist($class);
            $manager->flush();
                       
            $this->addFlash('success', "La classe a été rajouté avec succes");
            return $this->redirectToRoute('classes');
        }

        return $this->render(
            'classes/newClass.html.twig', [
                'form' => $formNewClass->createView()
            ]
        );
    }

    /**
     * @Route("/classes/{nomEcole}/{nomClasse}", name="class")
     */
    public function class($nomEcole, $nomClasse){
        return $this->render(
            'classes/class.html.twig', [
                'nomEcole' => $nomEcole,
                'nomClasse' => $nomClasse
            ]

        );
    }

}