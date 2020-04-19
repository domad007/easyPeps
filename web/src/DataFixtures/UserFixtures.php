<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Ecole;
use App\Entity\Eleve;
use App\Entity\Classe;
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

        $actifRole = new Role();
        $actifRole->setTitle('ROLE_ACTIF');
        $manager->persist($actifRole);

        $inactifRole = new Role();
        $inactifRole->setTitle('ROLE_INACTIF');
        $manager->persist($inactifRole);

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
            ->setUserActif(0)
            ->setResetToken(NULL); 

        $manager->persist($actifRole);

        $ecole1 = new Ecole();
        $ecole1
        ->setNomEcole('IPW');

        $manager->persist($ecole1);

        $ecole2 = new Ecole();
        $ecole2
        ->setNomEcole("Technique Saint Jean");

        $manager->persist($ecole2);

        for($i = 1; $i<=5; $i++){
            $userFemme = new User();
            $userHomme = new User();
            $passwordFemme = $this->encoder->encodePassword($userFemme, 'password');
            $passwordHomme = $this->encoder->encodePassword($userHomme, 'password');

            $userFemme
            ->setNom($faker->firstname)
            ->setPrenom($faker->lastname)
            ->setNomUser($faker->name)
            ->setMail($faker->email)
            ->setDateNaiss($faker->dateTimeBetween($startDate = '-60 years', $endDate = '-20 years', $timezone = null))
            ->setMdp($passwordFemme)
            ->setSexe('femme')
            ->addUserRole($actifRole)
            ->setUserActif(0)
            ->setResetToken(NULL);

            $manager->persist($userFemme);  
            $userHomme
            ->setNom($faker->firstname)
            ->setPrenom($faker->lastname)
            ->setNomUser($faker->name)
            ->setMail($faker->email)
            ->setDateNaiss($faker->dateTime($max = 'now', $timezone = null))
            ->setMdp($passwordHomme)
            ->setSexe('homme')
            ->addUserRole($actifRole)
            ->setUserActif(0)
            ->setResetToken(NULL);  
            
            $manager->persist($userHomme);
        }

        for($i = 1; $i<5; $i++){
            $classesIpw = new Classe();
            $classesIpw
            ->setTitulaire($faker->lastname)
            ->setProfesseur($adminUser)
            ->setNomClasse('6G'.$i)
            ->setEcole($ecole1);
            $manager->persist($classesIpw);
            
        }
        for($i = 1; $i<5; $i++){
            $classesIpw = new Classe();
            $classesIpw
            ->setTitulaire($faker->lastname)
            ->setProfesseur($adminUser)
            ->setNomClasse('5G'.$i)
            ->setEcole($ecole2);
            $manager->persist($classesIpw);
            
        }

        $manager->flush();

        $getClasses1 = $manager
        ->getRepository(Classe::class)
        ->findByecole($ecole1);

        $getClasses2 = $manager
        ->getRepository(Classe::class)
        ->findByecole($ecole2);

        foreach($getClasses1 as $classe1){
            for($i = 1; $i<15; $i++){
                $eleve = new Eleve();
                $eleve
                ->setNom($faker->firstname)
                ->setPrenom($faker->lastname)
                ->setDateNaissance($faker->dateTimeBetween($startDate = '-20 years', $endDate = '-17 years', $timezone = null));
                $classe1->addEleve($eleve);
            }
            $manager->persist($classe1);
        }

        foreach($getClasses2 as $classe){
            for($i = 1; $i<15; $i++){
                $eleve = new Eleve();
                $eleve
                ->setNom($faker->firstname)
                ->setPrenom($faker->lastname)
                ->setDateNaissance($faker->dateTimeBetween($startDate = '-20 years', $endDate = '-17 years', $timezone = null));
                $classe->addEleve($eleve);
            }
            $manager->persist($classe);
        }
        $manager->flush();
    }
}
