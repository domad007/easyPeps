<?php

namespace App\Controller;
use DateTime;
use App\Entity\User;
use App\Entity\Ecole;
use App\Entity\Eleve;
use App\Entity\Classe;
use App\Form\EleveType;
use App\Form\AddEleveType;
use App\Form\NewClassType;
use App\Entity\EleveSupprime;
use App\Form\ChangeClassType;
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
        $ecole = new Ecole();
        $user = $this->getUser();
        
        $formNewClass = $this->createForm(NewClassType::class, $class);
        $formNewClass->handleRequest($request);
        
        if($formNewClass->isSubmitted() && $formNewClass->isValid()){
            $manager = $this->getDoctrine()->getManager();
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
     * @Route("/modifEleve", name="modif_eleve")
     */
    public function modifEleve(Request $request){
        if($request->isMethod('post')){
            $manager = $this->getDoctrine()->getManager();
            $studentData = $request->request->all();

            $student = $this->getDoctrine()
            ->getRepository(Eleve::class)
            ->findOneById($studentData['pk']);

            if($studentData['name'] == "nom"){
                $student->setNom($studentData['value']);
            }
            else {
                $student->setPrenom($studentData['value']);
            }
            
            $manager->persist($student);
            $manager->flush();
        }
        return new Response("");
    }

    /**
     * @Route("/deleteEleve", name="delete_eleve")
     */
    public function deleteEleve(Request $request){
        $eleveSuppr = new EleveSupprime();
        if($request->isMethod('post')){
            $manager = $this->getDoctrine()->getManager();
            $idEleve = $request->request->all();

            $eleve = $this->getDoctrine()
            ->getRepository(Eleve::class)
            ->findOneById($idEleve['eleve']);
            
            $eleveSuppr->setNom($eleve->getNom())
            ->setPrenom($eleve->getPrenom())
            ->setClasse($eleve->getClasse())
            ->setDateNaissance($eleve->getDateNaissance())
            ->setEcole($eleve->getClasse()->getEcole());
            
            $manager->remove($eleve);
            $manager->persist($eleveSuppr);
            $manager->flush();
        }
        return new Response("");
    }

    /**
     * @Route("/classes/{idEcole}/{idClasse}", name="class")
     */
    public function class(Request $request, $idEcole, $idClasse){
        $manager = $this->getDoctrine()->getManager();

        $nomEcole = $manager->getRepository(Ecole::class)
        ->findOneById($idEcole);

        $nomClasse = $manager->getRepository(Classe::class)
        ->findOneById($idClasse);

        $eleves = $manager->getRepository(Eleve::class)
        ->findByClasse($idClasse);

        $classe = new Classe();
        $eleve = new Eleve();

        $formAddStudent= $this->createForm(AddEleveType::class, $classe);
        $formAddStudent->handleRequest($request);

        if($formAddStudent->isSubmitted() && $formAddStudent->isValid()){
            
            foreach($classe->getEleves() as $eleves){
                $eleves->setClasse($nomClasse);             
                $manager->persist($eleves);
                $manager->flush();
            }

            $this->addFlash('success', "L'élève a été rajouté avec succès");
            return $this->redirectToRoute('class', ['idEcole' => $idEcole, 'idClasse' => $idClasse]);
        }

        return $this->render(
            'classes/class.html.twig', [
                'nomEcole' => $nomEcole,
                'nomClasse' => $nomClasse,
                'eleves' => $eleves,
                'form' => $formAddStudent->createView()
            ]

        );
    }

    /**
     * @Route("/changeClasse/{idEleve}", name="change_class")
     */
    public function changeClasse(Request $request, $idEleve){

        $manager = $this->getDoctrine()->getManager();

        $eleve = $manager
        ->getRepository(Eleve::class)
        ->findOneById($idEleve);

        $classes = $manager
        ->getRepository(Classe::class)
        ->findBy([
           'ecole' => $eleve->getClasse()->getEcole()->getId()
        ]);

        if($request->isMethod('post')){
            $data = $request->request->all();

            $classe = $manager
            ->getRepository(Classe::class)
            ->findOneById($data['classe']);

            $eleve->setClasse($classe);

            $manager->persist($eleve);
            $manager->flush();

            $this->addFlash('success', "L'élève a bien été changé");
            return $this->redirectToRoute('class', [
                'idEcole' => $eleve->getClasse()->getEcole()->getId(),
                'idClasse' => $eleve->getClasse()->getId()
            ]);
        }
        //dump($eleve);

        return $this->render(
            '/classes/changeClasse.html.twig', [
                'eleve' => $eleve,
                'classes' => $classes
            ]
        );
    }


}