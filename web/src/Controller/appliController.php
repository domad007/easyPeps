<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Classe;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class appliController extends AbstractController {
    private $serializer;
    public function __construct(SerializerInterface $serializer){
        $this->serializer = $serializer;
    }
    /**
     * @Route("/connexionAppli/{pseudo}/{mdp}", name="connexion_appli")
     */
    public function connexionAppli($pseudo, $mdp, UserPasswordEncoderInterface $encoder){
        $manager = $this->getDoctrine()->getManager();
        $getUser = $manager
        ->getRepository(User::class)
        ->findBy(
            [
                'nomUser' => $pseudo,
            ]
        );
        foreach($getUser as $key => $value){
            $user['id'] = $value->getId();
        }
        if(!empty($getUser)){
            echo json_encode($user);
        }
        else {
            echo json_encode("problem");
        }
        return new Response("");
    }

    /**
     * @Route("/groupsUser/{user}", name="ecoles_user")
     */
    public function ecolesUser($user){
        $manager = $this->getDoctrine()->getManager();
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('ecole', 'ecole')
        ->addScalarResult('groups_id', 'groups_id')
        ->addScalarResult('groupes', 'groupes')
        ->addScalarResult('nombreEleves', 'nombreEleves');
        $groupsSql = "select ecole.nom_ecole as ecole, groups_id, GROUP_CONCAT(nom_classe SEPARATOR '/') as groupes 
        from classe
        join ecole on classe.ecole_id = ecole.id
        where groups_id is not null and professeur_id = ?
        group by groups_id";
        $getGroups = $manager->createNativeQuery($groupsSql, $rsm);
        $getGroups->setParameter(1, $user);
        $groups = $getGroups->getResult();

        if(empty($groups)){
            echo json_encode("probleme");
        }
        else {
            echo json_encode($groups);
        }

        return new Response();   
    }

}