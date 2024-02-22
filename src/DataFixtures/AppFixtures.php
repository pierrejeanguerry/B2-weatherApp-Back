<?php

namespace App\DataFixtures;

use App\Entity\Building;
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

        $user = new User();
        $password = $this->hasher->hashPassword($user, 'pj');
        $user->setUsername("pj")
        ->setPassword($password)
        ->setEmail("pj@pj.fr")
        ->setRoles($user->getRoles());
           $buildings = Array();
           for ($i = 0; $i < 4; $i++) {
               $buildings[$i] = new Building();
               $buildings[$i]->setName($faker->userName);
               $buildings[$i]->setUser($user);
               $manager->persist($buildings[$i]);
           }
        $manager->persist($user);

        $user = new User();
        $password = $this->hasher->hashPassword($user, 'pj');
        $user->setUsername("pj")
        ->setPassword($password)
        ->setEmail("pj@pj.com")
        ->setRoles($user->getRoles());
        $manager->persist($user);

        $user = new User();
        $password = $this->hasher->hashPassword($user, 'pj');
        $user->setUsername("pj")
        ->setPassword($password)
        ->setEmail("pj@pj.org")
        ->setRoles($user->getRoles());
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