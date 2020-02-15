<?php

namespace App\DataFixtures;

use Faker\Factory;
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

        for($i = 1; $i<=5; $i++){
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

        $manager->flush();
    }
}
