<?php

namespace App\DataFixtures;

use App\Factory\EventsFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        EventsFactory::createMany(rand(20, 50));
    }

    public function getDependencies(): array
    {
        return [UserFixture::class, SponsorsFixtures::class];
    }
}
