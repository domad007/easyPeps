<?php

namespace App\Controller;
use DateTime;
use App\Entity\User;
use App\Entity\Cours;
use App\Entity\Ecole;
use App\Entity\Eleve;
use App\Entity\Classe;
use App\Form\EleveType;
use App\Entity\Presences;
use App\Entity\Evaluation;
use App\Form\AddEleveType;
use App\Form\NewClassType;
use App\Entity\CoursGroupe;
use App\Entity\EleveSupprime;
use App\Form\ChangeClassType;
use App\Entity\EvaluationGroup;
use App\Entity\CustomizedPresences;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class classesController extends AbstractController {

    /**
     * Affichage des classes selon l'utilisateur
     * @Route("/classes", name="classes")
     * @Security("is_granted('ROLE_USER')")
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
     * @IsGranted("ROLE_USER")
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
     * @Route("/classes/{classe}", name="class")
     * @Security("is_granted('ROLE_USER') and user === classe.getProfesseur()")
     */
    public function class(Classe $classe, Request $request, UserInterface $user){
        $manager = $this->getDoctrine()->getManager();
        $class = new Classe();
        $eleves = $manager->getRepository(Eleve::class)
        ->findByClasse($classe->getId());

        $cours = $manager
        ->getRepository(Cours::class)
        ->findBygroupe($classe->getGroups());

        $presencesCustom = $manager
        ->getRepository(CustomizedPresences::class)
        ->findOneBy(
            [
                'typePresence' => 1,
                'user' => $user
            ]
        );

        $presences = $manager
        ->getRepository(Presences::class)
        ->findOneById(1);

        $evaluations = $manager
        ->getRepository(Evaluation::class)
        ->findBygroupe($classe->getGroups());

        $eleve = new Eleve();

        $formAddStudent= $this->createForm(AddEleveType::class, $class);
        $formAddStudent->handleRequest($request);

        if($formAddStudent->isSubmitted() && $formAddStudent->isValid()){
            
            foreach($class->getEleves() as $eleves){   
                $eleves->setClasse($classe);
                $manager->persist($eleves);
                if(!empty($evaluations)){
                    foreach($evaluations as $key => $value){
                        $evaluationGroupe = new EvaluationGroup();
                        $evaluationGroupe
                        ->setEvaluation($value)
                        ->setEleve($eleves)
                        ->setPoints("0");

                        $manager->persist($evaluationGroupe);
                    }
                }
                if(!empty($cours)){
                    foreach($cours as $key => $value){
                        if(!empty($presencesCustom)){
                            $coursGroupe = new CoursGroupe();
                            $coursGroupe
                            ->setCoursId($value)
                            ->setEleveId($eleves)
                            ->setPoints("0")
                            ->setPresences($presences)
                            ->setCustomizedPresences($presencesCustom);
    
                            $manager->persist($coursGroupe);
                        }
                        else {
                            $coursGroupe = new CoursGroupe();
                            $coursGroupe
                            ->setCoursId($value)
                            ->setEleveId($eleves)
                            ->setPoints("0")
                            ->setPresences($presences)
                            ->setCustomizedPresences(null);
    
                            $manager->persist($coursGroupe);
                        }
                        
                    }
                }
                $manager->flush();
            }

            $this->addFlash('success', "L'élève ou les élèves ont été rajouté avec succès");
            return $this->redirectToRoute('class', ['classe' => $classe->getId()]);
        }

        return $this->render(
            'classes/class.html.twig', [
                'classe' => $classe,
                'eleves' => $eleves,
                'form' => $formAddStudent->createView()
            ]

        );
    }

    /**
     * @Route("/changeClasse/{eleve}", name="change_class")
     * @Security("is_granted('ROLE_USER') and user === eleve.getClasse().getProfesseur()")
     */
    public function changeClasse(Eleve $eleve, Request $request, UserInterface $user){

        $manager = $this->getDoctrine()->getManager();

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

            $cours = $manager
            ->getRepository(Cours::class)
            ->findBygroupe($classe->getGroups());

            $presencesCustom = $manager
            ->getRepository(CustomizedPresences::class)
            ->findOneBy(
                [
                    'typePresence' => 1,
                    'user' => $user
                ]
            );
            $presences = $manager
            ->getRepository(Presences::class)
            ->findOneById(1);

            $evaluations = $manager
            ->getRepository(Evaluation::class)
            ->findBygroupe($classe->getGroups());

            if(!empty($evaluations)){
                foreach($evaluations as $key => $value){
                    $evaluationGroup = new EvaluationGroup();
                    $evaluationGroup
                    ->setEvaluation($value)
                    ->setEleve($eleve)
                    ->setPoints("0");

                    $manager->persist($evaluationGroup);
                }
            }

            if(!empty($cours)){
                foreach($cours as $key => $value){
                    if(!empty($presencesCustom)){
                        $coursGroupe = new CoursGroupe();
                        $coursGroupe
                        ->setCoursId($value)
                        ->setEleveId($eleve)
                        ->setPoints("0")
                        ->setPresences($presencesCustom->getTypePresence())
                        ->setCustomizedPresences($presencesCustom);

                        $manager->persist($coursGroupe);
                    }
                    else {
                        $coursGroupe = new CoursGroupe();
                        $coursGroupe
                        ->setCoursId($value)
                        ->setEleveId($eleve)
                        ->setPoints("0")
                        ->setPresences($presences->getId());

                        $manager->persist($coursGroupe);
                    }

                }
            }

            $eleve->setClasse($classe);

            $manager->persist($eleve);
            $manager->flush();

            $this->addFlash('success', "L'élève a bien été changé");
            return $this->redirectToRoute('class', [
                'classe' => $eleve->getClasse()->getId()
            ]);
        }

        return $this->render(
            '/classes/changeClasse.html.twig', [
                'eleve' => $eleve,
                'classes' => $classes
            ]
        );
    }


}