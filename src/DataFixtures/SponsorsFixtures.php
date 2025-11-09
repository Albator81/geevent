<?php

namespace App\DataFixtures;

use App\Factory\SponsorsFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SponsorsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        SponsorsFactory::createMany(rand(8, 15));
    }
}
