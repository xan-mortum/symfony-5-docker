<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
     private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('user@mail.dd');
        $user->setRoles(['ROLE_USER']);

        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            '123456'
        ));
        $manager->persist($user);

        $admin = new User();
        $admin->setEmail('admin@mail.dd');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'qwerty'
        ));
        $manager->persist($admin);

        $manager->flush();
    }
}
