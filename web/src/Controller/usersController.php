<?php 

namespace App\Controller;
use App\Repository\UserRepository;
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
     * @Route("/admin/userStatistiques", name="users_statistiques")
     */
    public function userStatistiques(){
        return $this->render(
            'admin/gestionUser/statistiquesUsers.html.twig',
            [
                'piechart' => $this->statistiqueSexe()['piechart']
            ]
        );
    }

    private function statistiqueSexe(){
        $manager = $this->getDoctrine()->getManager();
        $getHomme = $manager->createQueryBuilder();
        $getFemme = $manager->createQueryBuilder();
        $getHomme
        ->select('count(u.id)')
        ->from('App:User', 'u')
        ->where('u.sexe = :sexe')
        ->setParameter('sexe', "H");

        $getFemme
        ->select('count(u.id)')
        ->from('App:User', 'u')
        ->where('u.sexe = :sexe')
        ->setParameter('sexe', "F");
        
        $resultHomme = $getHomme->getQuery()->getSingleScalarResult();
        $resultFemme= $getFemme->getQuery()->getSingleScalarResult();


        /*$pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
            [
                ['Sexe', 'Hommes et femmes inscrits sur le site'],
                ['Hommes',  $resultHomme],
                ['Femmes',  $resultFemme],
            ]
        );
        $pieChart->getOptions()->setPieSliceText('label');
        $pieChart->getOptions()->setTitle('Hommes et femmes inscrits sur le site');
        $pieChart->getOptions()->setPieStartAngle(100);
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getLegend()->setPosition('none');*/

        return array(
            'piechart' => $pieChart
        );
    }

}