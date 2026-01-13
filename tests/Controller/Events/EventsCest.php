<?php

namespace App\Tests\Controller\Events;

use App\Factory\EventsFactory;
use App\Factory\UserFactory;
use App\Factory\SponsorsFactory;
use App\Tests\Support\ControllerTester;

class EventsCest
{
    /**
     * Teste l'affichage de la liste des événements
     */
    public function testIndexPage(ControllerTester $I): void
    {
        UserFactory::createMany(6, ['roles' => ['ROLE_ADMIN']]);
        UserFactory::createMany(6);
        SponsorsFactory::createMany(6);
        EventsFactory::createMany(3);

        $I->amOnPage('/events');
        $I->seeResponseCodeIs(200);
        $I->see('Liste des événements', 'h1');
        $I->seeNumberOfElements('.card', 3);
    }

    
}