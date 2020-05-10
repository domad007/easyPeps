<?php 

namespace App\Controller;
use DateTime;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Cours;
use App\Entity\Groups;
use App\Form\CompteType;
use App\Entity\Appreciation;
use App\Repository\UserRepository;
use App\Entity\CustomizedPresences;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Histogram;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class usersController extends AbstractController {

    /**
     * @Route("/admin/accounts", name="admin_accounts")
     */
    public function accounts(UserRepository $users){
        return $this->render(
            'admin/gestionUser/userAccount.html.twig',
            [
                'users' => $users->findAll()
            ]
        );
    }

    /**
     * @Route("/admin/editUser/{user}", name="edit_user")
     */
    public function editUser(User $user, Request $request){
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(CompteType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', "Le profil a été modifié avec succes !");
            return $this->redirectToRoute('admin_accounts');
        }

        return $this->render(
            'admin/gestionUser/editUser.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user
            ]
        );
    }

    /**
     * @Route("/addRoleAdmin", name="add_role_admin")
     */
    public function addRoleAdmin(Request $request){
        $manager = $this->getDoctrine()->getManager();
        if($request->isMethod('post')){
            $data = $request->request->all();
            $user = $manager
            ->getRepository(User::class)
            ->findOneById($data['userId']);
            $roleAdmin = $manager
            ->getRepository(Role::class)
            ->findOneBytitle("ROLE_ADMIN");


            switch($data['admin']){
                case 'true': 
                    $user->addUserRole($roleAdmin);
                break;
                case 'false': 
                    $user->removeUserRole($roleAdmin);
                break;
            }

            $manager->persist($user);
            $manager->flush();
            
        }

        return new Response("");
    }

    /**
     * @Route("/admin/userStatistiques", name="users_statistiques")
     */
    public function userStatistiques(){
        return $this->render(
            'admin/gestionUser/statistiquesUsers.html.twig',
            [
                'statistiquesSexe' => $this->statistiqueSexe(),
                'statistiquesEcole' => $this->statistiqueEcole(),
                'statistiquesAge' => $this->statistiquesAge()
            ]
        );
    }

    private function statistiqueSexe(){
        $manager = $this->getDoctrine()->getManager();
        $hommes = $manager->getRepository(User::class)->count(['sexe' => "H"]);
        $femmes = $manager->getRepository(User::class)->count(['sexe' => "F"]);
        $graphSexe = new PieChart();
        $graphSexe->getData()->setArrayToDataTable(
            [
                ['Sexe', 'Hommes et femmes inscrits sur le site'],
                ['Hommes',  $hommes],
                ['Femmes',  $femmes],
            ]
        );
        $graphSexe->getOptions()->setPieSliceText('label');
        $graphSexe->getOptions()->setTitle('Hommes et femmes inscrits sur le site');
        $graphSexe->getOptions()->setBackgroundColor('transparent');
        $graphSexe->getOptions()->setPieStartAngle(100);
        $graphSexe->getOptions()->setHeight(300);
        $graphSexe->getOptions()->setWidth(500);
        $graphSexe->getOptions()->getLegend()->setPosition('center');

        return $graphSexe;
    }

    private function statistiqueEcole(){
        $manager = $this->getDoctrine()->getManager();
        $totalUser = 0;
        $data = [['Ecole','Nombre prof']];

        $rsm = new ResultSetMapping();
        $rsm
        ->addScalarResult('nombreProf', 'nombreProf')
        ->addScalarResult('ecole', 'ecole');
        $userEcole = "
            SELECT count(DISTINCT professeur_id) as nombreProf, ecole.nom_ecole as ecole  FROM `classe`
            join ecole on classe.ecole_id = ecole.id
            group by ecole_id
        ";

        $getUserEcole = $manager->createNativeQuery($userEcole, $rsm);
        $resultUserEcole = $getUserEcole->getResult();

        foreach($resultUserEcole as $value){
            $data[]= array(
                $value['ecole'], 
                (int)$value['nombreProf'],
            );
        }
        
        $graphEcole = new PieChart();
        $graphEcole->getData()->setArrayToDataTable($data);
        $graphEcole->getOptions()->setPieSliceText('label');
        $graphEcole->getOptions()->setTitle("Professeurs par rapport à l'école");
        $graphEcole->getOptions()->setBackgroundColor('transparent');
        $graphEcole->getOptions()->setPieStartAngle(100);
        $graphEcole->getOptions()->setHeight(300);
        $graphEcole->getOptions()->setWidth(500);
        $graphEcole->getOptions()->getLegend()->setPosition('center');

        return $graphEcole;
    }

    private function statistiquesAge(){
        $manager = $this->getDoctrine()->getManager();
        $dateAjd = new \DateTime();
        $totalUsers = 0;
        $users = $manager
        ->getRepository(User::class)
        ->findAll();
        foreach($users as $key => $value){
            $ageUsers[$value->getId()] = $dateAjd->diff($value->getDateNaiss(), true)->y; 
            $totalUsers++;
        }
        foreach($ageUsers as $key => $value){
            if($value < 20){
                $age['max20'][] = $key;
            }
            if($value >= 20 && $value <= 30){
                $age['max30'][] = $key;
            }
            if($value >= 31 && $value <= 40){
                $age['max40'][] = $key;
            }
            if($value >= 41 && $value <= 50){
                $age['max50'][] = $key;
            }
            if($value >= 51 && $value <= 65){
                $age['max65'][] = $key;
            }
        }
        foreach($age as $key => $value){
            $age[$key] = count($value); 
        }
        
        $graphAge = new PieChart();
        $graphAge->getData()->setArrayToDataTable(
            [
                ['Sexe', "Tranches d'âges inscrits sur le site"],
                ['En dessous de 20 ans', $age['max20']],
                ['Entre 20 et 30 ans', $age['max30']],
                ['Entre 31 et 40 ans', $age['max40']],
                ['Entre 41 et 50 ans', $age['max50']],
                ['Entre 51 et 65 ans', $age['max65']]
               
            ]
        );
        $graphAge->getOptions()->setPieSliceText('label');
        $graphAge->getOptions()->setTitle("Tranches d'âges inscris sur le site");
        $graphAge->getOptions()->setPieStartAngle(100);
        $graphAge->getOptions()->setBackgroundColor('transparent');
        $graphAge->getOptions()->setHeight(300);
        $graphAge->getOptions()->setWidth(500);
        $graphAge->getOptions()->getLegend()->setPosition('center');

        return $graphAge;
    }

}