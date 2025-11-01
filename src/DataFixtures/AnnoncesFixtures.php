<?php

namespace App\DataFixtures;

use App\Factory\AnnoncesFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AnnoncesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        AnnoncesFactory::createMany(rand(30, 80));
    }

    public function getDependencies(): array
    {
        return [UserFixture::class];
    }
}
