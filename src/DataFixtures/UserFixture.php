<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'username' => 'JeanJean',
            'email' => 'jean@jean.fr',
            'password' => password_hash('jean', PASSWORD_BCRYPT),
            'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
            'isAdmin' => true,
        ]);

        UserFactory::createMany(25);
    }
}
