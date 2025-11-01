<?php

namespace App\Factory;

use App\Entity\Events;
use App\Enum\Type;
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
        $start = self::faker()->dateTimeBetween('+1 week', '+3 months');
        $end = (clone $start)->modify('+ '.rand(1, 8).' hours');

        return [
            'title' => self::faker()->sentence(5),
            'description' => self::faker()->paragraphs(2, true),
            'latitude' => self::faker()->latitude(),
            'longitude' => self::faker()->longitude(),
            'startDatetime' => $start,
            'endDatetime' => $end,
            'createdBy' => UserFactory::randomOrCreate(["roles" => ["ROLE_USER", "ROLE_ADMIN"]]),
            'type' => self::faker()->randomElement(Type::cases()),
            'sponsors' => SponsorsFactory::randomRange(0, 3),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Events $events): void {})
        ;
    }
}
