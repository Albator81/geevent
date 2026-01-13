<?php

namespace App\Tests\Controller\Events;

use App\Factory\EventsFactory;
use App\Factory\UserFactory;
use App\Factory\SponsorsFactory;
use App\Tests\Support\ControllerTester;
use App\Enum\Type;
use App\Entity\Events;

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

    public function testSearchApi(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        UserFactory::createMany(20); 
        SponsorsFactory::createMany(10);

        EventsFactory::createOne(['title' => 'Unique Event']);

        $I->amOnPage('/events/search?search=Unique');
        $I->seeResponseCodeIs(200);
    }

    public function testShowEvent(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        UserFactory::createMany(20); 
        SponsorsFactory::createMany(10);

        $event = EventsFactory::createOne([
            'title' => 'Les tests c nul',
            'description' => 'Super description de test',
            'type' => Type::APERO,
        ]);

        $I->amOnPage('/events/' . $event->getId());
        $I->seeResponseCodeIs(200);
        $I->see('Les tests c nul', 'h1');
        $I->see('Super description de test');
        $I->see(Type::APERO->value);
    }

    public function testJoinEventAnonymous(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        UserFactory::createMany(20); 
        SponsorsFactory::createMany(10);

        $event = EventsFactory::createOne();
        
        $I->amOnPage('/events/' . $event->getId() . '/join');
        $I->seeCurrentRouteIs('app_login');
    }

    public function testJoinEventSuccessEmailOk(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true, 'verified' => true]);
        UserFactory::createMany(20, ['verified' => true]); 
        SponsorsFactory::createMany(10);

        $user = UserFactory::createOne(['verified' => true]);
        $event = EventsFactory::createOne([
            'endDatetime' => new \DateTime('+2 days')
        ]);

        $I->amLoggedInAs($user->_real());
        $I->amOnPage('/events/' . $event->getId());
        $I->click('Rejoindre l\'évènement');

        $I->seeInCurrentUrl('/events/' . $event->getId());
        $I->see('Vous avez bien rejoint l\'évènement !', '.alert-success');
        
        $I->seeInRepository(Events::class, [
            'id' => $event->getId(),
        ]);
    }
}