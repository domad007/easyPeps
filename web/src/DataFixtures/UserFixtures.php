<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');
        //Gestion des utilisateurs
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();

        $adminUser
            ->setNom("Fiedorczuk")
            ->setPrenom("Dominik")
            ->setNomUser("domad007")
            ->setMail("dominikfiedorczuk69@gmail.com")
            ->setMdp( $this->encoder->encodePassword($adminUser, 'domad1997'))
            ->setSexe("Homme")
            ->setDateNaiss(new \DateTime('16-12-1997'))
            ->addUserRole($adminRole)
            ->setResetToken(NULL);

        $manager->persist($adminUser);

        $manager->flush();

        /*for($i = 1; $i<=5; $i++){
            $user = new User();
            $password = $this->encoder->encodePassword($user, 'password');
            
            $user->setNom($faker->firstname)
                    ->setPrenom($faker->lastname)
                    ->setNomUser($faker->name)
                    ->setMail($faker->email)
                    ->setDateNaiss('15-12-2000')
                    ->setMdp($password)
                    ->setSexe('femme');
            
            $manager->persist($user);  
        }

        $manager->flush();*/
    }
}
