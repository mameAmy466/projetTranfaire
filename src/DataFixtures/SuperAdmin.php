<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SuperAdmin extends Fixture
{
     private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
        $this->passwordEncoder = $passwordEncoder;
     }

    public function load(ObjectManager $manager)
    {
        $user = new User();

         $user->setPassword($this->passwordEncoder->encodePassword(
             $user,
            '123'
      ));
      $user->setRoles(["ROLE_ADMIN"]);
      $user->setUsername("mame@gueye12");
      $user->setNom("gueye");
      $user->setPrenom("mame amy");
      $user->setImageName("mame.jpeg");
        $manager->persist($user);
        $manager->flush();
    }
}
