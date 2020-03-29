<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Entity\Classe;
use App\Entity\Periodes;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class cahierCoteController extends AbstractController {


    /**
     * @Route("/choixCahier", name="choix_cahier")
     */
    public function choixCahier(UserInterface $user){
        $manager = $this->getDoctrine()->getManager();
        $rsm = new ResultSetMapping();
        $rsm
        ->addScalarResult('ecole', 'ecole')
        ->addScalarResult('groups_id', 'groups_id')
        ->addScalarResult('groupes', 'groupes');

        $groupsSql = "select ecole.nom_ecole as ecole, groups_id, GROUP_CONCAT(nom_classe SEPARATOR '/') as groupes 
        from classe
        join ecole on classe.ecole_id = ecole.id
        where groups_id is not null and professeur_id = ?
        group by groups_id";

        $getGroups = $manager->createNativeQuery($groupsSql, $rsm);
        $getGroups->setParameter(1, $user->getId());

        $groups = $getGroups->getResult();

        return $this->render(
            '/cahierCotes/choixCahier.html.twig', 
            [
                'groups' => $groups
            ]
        );
    }


    /**
     * @Route("/cahierCotes/{idGroup}", name="cahier_cotes")
     */
    public function cahierCotes(UserInterface $user, $idGroup){

        $manager = $this->getDoctrine()->getManager();
        $rsm = new ResultSetMapping();
        $rsm
        ->addScalarResult('ecole', 'ecole')
        ->addScalarResult('groups_id', 'groups_id')
        ->addScalarResult('groupes', 'groupes');

        $groupsSql = "select ecole.nom_ecole as ecole, groups_id, GROUP_CONCAT(nom_classe SEPARATOR '/') as groupes 
        from classe
        join ecole on classe.ecole_id = ecole.id
        where groups_id is not null and professeur_id = ?
        group by groups_id";

        $getGroups = $manager->createNativeQuery($groupsSql, $rsm);
        $getGroups->setParameter(1, $user->getId());

        $groups = $getGroups->getResult();

        $group = $manager
        ->getRepository(Classe::class)
        ->findByGroups($idGroup);

        $getPeriodes = $this->getDoctrine()
        ->getRepository(Periodes::class)
        ->findBygroupe($idGroup);

        foreach($group as $key => $value){           
            $eleves [] = $manager
            ->getRepository(Eleve::class)
            ->findBy(
                [
                    'classe' => $value->getId()
                ]
            );
        }

        $getCompetencesPeriode = $this->forward('App\Controller\calculController::getMoyenneCompetence', 
        [
            'idGroup' => $idGroup
        ]);
        
        return $this->render(
            '/cahierCotes/cahierCotes.html.twig',
            [
                'ecole' => $group,
                'eleves' => $eleves,   
                'competencesPeriode' => $getCompetencesPeriode,
                'periodes' => $getPeriodes,
                'groups' => $groups,
            ]
        );
    }
}