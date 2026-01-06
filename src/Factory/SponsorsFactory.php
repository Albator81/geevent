<?php

namespace App\Factory;

use App\Entity\Sponsors;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Symfony\Component\String\Slugger\AsciiSlugger;

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
        $sourceDir = __DIR__ . '/images';
        $publicDir = __DIR__ . '/../../public/uploads/sponsors';
        $imageName = null;
        $slugger = new AsciiSlugger(); //utilisé pour normalisé le nomage

        $imagesDir = __DIR__ . '/images';
        $images = array_diff(scandir($imagesDir), ['.', '..']);
        $randomImage = $images[array_rand($images)];

        $fileInfo = pathinfo($randomImage);
        $filename = $fileInfo['filename'];
        $extension = $fileInfo['extension'];

        $sluggedFileName = $slugger->slug($filename)->lower();
        $sluggedImage = $sluggedFileName . '.' . strtolower($extension);

        copy($sourceDir . '/' . $randomImage, $publicDir . '/' . $sluggedImage);

        return [
            'name' => self::faker()->company(),
            'websiteUrl' => self::faker()->url(),
            'logoPath' => $sluggedImage,
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
