<?php

namespace App\Factory;

use App\Entity\Annonces;
use App\Enum\Target;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Annonces>
 */
final class AnnoncesFactory extends PersistentProxyObjectFactory
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
        return Annonces::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->sentence(4),
            'content' => self::faker()->paragraphs(3, true),
            'sentBy' => UserFactory::random(['isAdmin' => true]),
            'sentTo' => self::faker()->randomElement(Target::cases()),
            'sentAt' => self::faker()->dateTimeBetween('-1 month'),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Annonces $annonces): void {})
        ;
    }
}
