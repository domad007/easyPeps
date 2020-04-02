<?php 

namespace App\Controller;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class userAccountsController extends AbstractController {

    /**
     * @Route("/admin/accounts", name="admin_accounts")
     */
    public function accounts(UserRepository $users){
        return 
            $this->render(
                '/admin/userAccount/userAccount.html.twig',
                [
                    'users' => $users->findAll()
                ]
            );
    }
}