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
        UserFactory::createMany(10, ['isAdmin' => true]);
        UserFactory::createMany(20); 
        SponsorsFactory::createMany(10);
        EventsFactory::createMany(3);

        $I->amOnPage('/events');
        $I->seeResponseCodeIs(200);
        $I->see('Liste des événements', 'h1');
        $I->seeNumberOfElements('.card', 3);
    }

    public function testPagination(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        UserFactory::createMany(20); 
        SponsorsFactory::createMany(10);

        EventsFactory::createMany(10);

        $I->amOnPage('/events');
        $I->seeResponseCodeIs(200);
        
        $I->seeNumberOfElements('.card', 6);
        
        $I->see('Suivant');
        $I->click('Suivant');
        
        $I->seeInCurrentUrl('page=1');
        $I->seeNumberOfElements('.card', 4);
    }

    public function testSearchFilter(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        UserFactory::createMany(20); 
        SponsorsFactory::createMany(10);

        EventsFactory::createOne(['title' => 'Concert de Jazz']);
        EventsFactory::createOne(['title' => 'Match de Foot']);

        $I->amOnPage('/events?search=Jazz');
        $I->see('Concert de Jazz');
        $I->dontSee('Match de Foot');
    }

    public function testMapView(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        UserFactory::createMany(20); 
        SponsorsFactory::createMany(10);

        EventsFactory::createMany(2);
        
        $I->amOnPage('/events?display=c');
        $I->seeResponseCodeIs(200);
        $I->seeElement('#map');
    }
}