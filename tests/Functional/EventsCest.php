<?php

declare(strict_types=1);


namespace Tests\Functional;

use Tests\Support\FunctionalTester;
use App\Factory\EventsFactory;
use App\Factory\UserFactory;

final class EventsCest
{
    public function _before(FunctionalTester $I): void
    {
        UserFactory::createMany(5);
    }

    /**
     * Teste l'affichage de la liste des événements
     */
    public function testIndexPage(FunctionalTester $I): void
    {
        EventsFactory::createMany(3);

        $I->amOnPage('/events');
        $I->seeResponseCodeIs(200);
        $I->see('Liste des événements', 'h1');
        $I->seeNumberOfElements('.card', 3);
    }
}
