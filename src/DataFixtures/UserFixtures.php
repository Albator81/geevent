<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = (new User())
            ->setEmail("jean@jean.fr")
            ->setUsername("JeanJean")
            ->setPassword(password_hash("Jean123", PASSWORD_BCRYPT))
            ->setRoles(["ROLE_ADMIN"]);
        
        $manager->persist($user);
        $manager->flush();
    }
}
