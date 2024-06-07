<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Room;
use App\Entity\Station;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $t = microtime(true);

        $user = new User();
        $password = $this->hasher->hashPassword($user, '123456789Pj');
        $user->setUsername("pj")
   ->setPassword($password)
   ->setEmail("truc@truc.fr")
   ->setRoles($user->getRoles());
        $buildings = Array();
        for ($i = 0; $i < 4; $i++) {
            $buildings[$i] = new Building();
            $buildings[$i]->setName($faker->streetAddress);
            $buildings[$i]->setUser($user);
            $buildings[$i]->setDate(\DateTime::createFromFormat('U.u', sprintf('%f', $t)));
            for ($k = 0; $k < 2; $k++) {
                if ($k == 0 && $i == 0){
                    $stations[$k] = new Station;
                    $stations[$k]
                        ->setName($faker->country)
                        ->setBuilding($buildings[$i])
                        ->setActivationDate(\DateTime::createFromFormat('U.u', sprintf('%f', $t)))
                        ->setState(1)
                        ->setMac("D4:8A:FC:A7:76:FC");

                    $manager->persist($stations[$k]);
                        } else {
                            $stations[$k] = new Station;
                            $stations[$k]->setName($faker->country);
                            $stations[$k]->setBuilding($buildings[$i]);
                            $stations[$k]->setActivationDate(\DateTime::createFromFormat('U.u', sprintf('%f', $t)));
                            $stations[$k]->setMac($faker->macAddress);
                            $stations[$k]->setState(0);

                            $manager->persist($stations[$k]);
                        }
               }
               $manager->persist($buildings[$i]);
           }
        $manager->persist($user);

           $auteurs = Array();
           for ($i = 0; $i < 4; $i++) {
               $auteurs[$i] = new User();
               $password = $this->hasher->hashPassword($auteurs[$i], $faker->password);
               $auteurs[$i]->setUsername($faker->userName);
               $auteurs[$i]->setEmail($faker->email);
               $auteurs[$i]->setPassword($password);
               $auteurs[$i]->setRoles($auteurs[$i]->getRoles());

               $manager->persist($auteurs[$i]);
           }

        $manager->flush();
    }
}
