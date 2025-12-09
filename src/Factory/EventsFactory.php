<?php

namespace App\Factory;

use App\Entity\Events;
use App\Enum\Type;
use Symfony\Component\HttpClient\HttpClient;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Events>
 */
final class EventsFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Events::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        $start = self::faker()->dateTimeBetween('-2 week', '+3 months');
        $end = (clone $start)->modify('+ '.rand(10, 128).' hours');

        // Coordonées dans Reims et ses alentours
        $lat = self::faker()->randomFloat(6, 49.20, 49.30);
        $lon = self::faker()->randomFloat(6, 3.97, 4.08);

        $address = HttpClient::create()->request('GET', "https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$lon&format=json")->toArray();

        $cover = self::faker()->boolean(75) ? HttpClient::create()->request('GET', "https://picsum.photos/1920/1080")->getContent() : null;

        return [
            'title' => self::faker()->sentence(5),
            'description' => self::faker()->paragraphs(2, true),
            'latitude' => $lat,
            'longitude' => $lon,
            'address' => $address['display_name'],
            'startDatetime' => $start,
            'endDatetime' => $end,
            'createdBy' => UserFactory::random(['isAdmin' => true]),
            'type' => self::faker()->randomElement(Type::cases()),
            'sponsors' => SponsorsFactory::randomSet(rand(3, 6)),
            'participants' => UserFactory::randomSet(rand(2, 15)),
            'cover' => $cover,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->afterPersist(function (Events $event, array $attributes): void {
                $sponsors = $attributes['sponsors'];
                foreach ($sponsors as $sponsor) {
                    $sponsor->addEvent($event);
                }
                $participants = $attributes['participants'];
                foreach ($participants as $participant) {
                    $participant->addEventsJoined($event);
                }
            })
        ;
    }
}
