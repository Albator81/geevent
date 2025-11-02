<?php

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
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
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        $a = self::faker()->boolean(10);
        $roles = $a ? ['ROLE_USER', 'ROLE_ADMIN'] : ['ROLE_USER'];

        $imagesDir = __DIR__ . '/images';
        $images = array_diff(scandir($imagesDir), ['.', '..']);
        $randomImage = $images[array_rand($images)];
        $avatarData = self::faker()->boolean(chanceOfGettingTrue: 25) ? null : file_get_contents($imagesDir . '/' . $randomImage);

        return [
            'email' => self::faker()->email(),
            'password' => password_hash('Test123', PASSWORD_BCRYPT),
            'roles' => $roles,
            'isAdmin' => $a,
            'username' => self::faker()->unique()->userName(),
            'avatar' => $avatarData,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(User $user): void {})
        ;
    }
}
