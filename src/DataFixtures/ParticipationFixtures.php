<?php

namespace App\DataFixtures;

use App\Entity\Events;
use App\Factory\ParticipantsFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ParticipationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $events = $manager->getRepository(Events::class)->findAll();

        foreach ($events as $event) {
            $participants = UserFactory::randomSet(rand(3, 15));

            foreach ($participants as $user) {
                ParticipantsFactory::createOne([
                    'participant' => $user,
                    'event' => $event,
                    'registrationDatetime' => \DateTime::createFromFormat(
                        'U',
                        rand(
                            $event->getStartDatetime()->getTimestamp() - 86400 * 7,
                            $event->getStartDatetime()->getTimestamp()
                        )
                    ),
                ]);
            }
        }
    }

    public function getDependencies(): array
    {
        return [UserFixture::class, EventsFixtures::class];
    }
}
