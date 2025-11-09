<?php

namespace App\Factory;

use App\Entity\Sponsors;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Sponsors>
 */
final class SponsorsFactory extends PersistentProxyObjectFactory
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
        return Sponsors::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        $imagesDir = __DIR__.'/images';
        $images = array_diff(scandir($imagesDir), ['.', '..']);
        $randomImage = $images[array_rand($images)];
        $avatarData = file_get_contents($imagesDir.'/'.$randomImage);

        return [
            'name' => self::faker()->company(),
            'websiteUrl' => self::faker()->url(),
            'logo' => $avatarData,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Sponsors $sponsors): void {})
        ;
    }
}
