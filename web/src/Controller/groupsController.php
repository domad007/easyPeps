<?php 

namespace App\Controller;

use App\Entity\Ecole;
use App\Entity\Classe;
use App\Entity\Groups;
use App\Form\GroupType;
use App\Form\AddGroupType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class groupsController extends AbstractController {
    
    /**
     * @Route("/groups", name="groups")
     */
    public function groups(UserInterface $user){
        $manager = $this->getDoctrine()->getManager();

       /* $ecoles = $manager->getRepository(Classe::class)
        ->findBy(
            array(
                'professeur' => $user->getId(), 
                'ecole.id' => 'groupby'
            ),
        );*/
        $ecoles = $manager->createQueryBuilder();
        $ecoles->select('ecole')
        ->from('App:Ecole', 'ecole')
        ->join('App:Classe', 'classes', 'WITH', 'ecole.id = classes.ecole');

        $resultEcoles = $ecoles->getQuery()->getResult();
        

        return $this->render(
            'groupes/groups.html.twig', [
                'ecoles' => $resultEcoles
            ]
        );
    }

    /**
     * @Route("/newGroup/{idEcole}", name="new_group")
     */
    public function newGroup(Request $request, $idEcole){
        $manager = $this->getDoctrine()->getManager();
        $group = new Groups();

        $ecole = $manager->getRepository(Ecole::class)
        ->findOneById($idEcole);

        $classes = $manager->getRepository(Classe::class)
        ->findBy([
           'ecole' =>  $ecole->getId()
        ]);
        
        $form = $this->createForm(AddGroupType::class, $group, ['classes' => $classes]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){           
            foreach($group->getClasses() as $classes){
                $classe = $manager->getRepository(Classe::class)
                ->findOneById($classes->getId());

                $classe->setGroups($group);

                $manager->persist($group);
                $manager->flush();
            }

            $this->addFlash('success', "Le groupe a bien été rajouté");
            return $this->redirectToRoute('groups');
        }

        return $this->render(
            'groupes/newGroup.html.twig', [
                'ecole'=> $ecole,
                'form' => $form->createView()
            ]
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