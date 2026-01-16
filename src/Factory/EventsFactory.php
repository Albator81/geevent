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
        // Date de début de l'event entre 2 semaines avant
        $start = self::faker()->dateTimeBetween('-2 week', '+3 months');
        $end = (clone $start)->modify('+ '.rand(10, 128).' hours');

        // Coordonées dans Reims et ses alentours
        $lat = self::faker()->randomFloat(6, 49.20, 49.30);
        $lon = self::faker()->randomFloat(6, 3.97, 4.08);

        $address = HttpClient::create()->request('GET', "https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$lon&format=json")->toArray();

        $coverFilename = null;

        // 75% de chance d'avoir une image
        if (self::faker()->boolean(75)) {
            $publicDir = __DIR__.'/../../public/uploads/events';

            // Générer un nom unique
            $filename = 'event-'.uniqid().'.png';

            // Télécharger l'image ou la laisse a null
            try {
                $content = HttpClient::create()->request('GET', 'https://picsum.photos/1920/1080')->getContent();
                file_put_contents($publicDir.'/'.$filename, $content);
                $coverFilename = $filename;
            } catch (\Exception $e) {
                // Si le téléchargement échoue, on laisse null
            }
        }

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
            'cover' => $coverFilename,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        // Ajouter l'event aux sponsors et aux participants
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
