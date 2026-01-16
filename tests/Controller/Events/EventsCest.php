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

    /**
     * Teste la pagination des events dans la page des events
     */
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

    /**
     * Teste la feature d'autocomplétion des events pour la barre de recherche
     */
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

    /**
     * Teste si la map apparait bien dans dans la carte des events 
     */
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

    /**
     * Teste la route qui recherche les events contenant un morceau de la recherche voulue
     */
    public function testSearchApi(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        UserFactory::createMany(20); 
        SponsorsFactory::createMany(10);

        EventsFactory::createOne(['title' => 'Unique Event']);

        $I->amOnPage('/events/search?search=Unique');
        $I->seeResponseCodeIs(200);
    }

    /**
     * Teste l'affichage de la page des détails d'un event
     */
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

    /**
     * Teste le fait que un utilisateur non connecté ne peut rejoindre un event
     */
    public function testJoinEventAnonymous(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        UserFactory::createMany(20); 
        SponsorsFactory::createMany(10);

        $event = EventsFactory::createOne([
            'startDatetime' => new \DateTime('+2 days')
        ]);
        
        $I->amOnPage('/events/' . $event->getId() . '/join');
        $I->seeCurrentRouteIs('app_login');
    }

    /**
     * Teste le fait que un utilisateur avec un email vérifié peut rejoindre un event
     */
    public function testJoinEventSuccessEmailOk(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        UserFactory::createMany(20); 
        SponsorsFactory::createMany(10);

        $user = UserFactory::createOne(['verified' => true]);
        $event = EventsFactory::createOne([
            'startDatetime' => new \DateTime('+2 days')
        ]);

        $I->amLoggedInAs($user->_real());

        $I->amOnPage('/events/' . $event->getId() . '/join');
        $I->see('Vous avez bien rejoint l\'évènement ! ', '.alert-success');
    }

    /**
     * Teste le fait que un utilisateur avec un email non vérifié ne peut pas rejoindre un event
     */
    public function testJoinEventEchecEmailNon(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        UserFactory::createMany(20); 
        SponsorsFactory::createMany(10);

        $user = UserFactory::createOne(['verified' => false]);
        $event = EventsFactory::createOne([
            'startDatetime' => new \DateTime('+2 days')
        ]);

        $I->amLoggedInAs($user->_real());

        $I->amOnPage('/events/' . $event->getId() . '/join');
        $I->see('Impossible de rejoindre : Votre email n\'est pas vérifié', '.alert-danger');
    }

    /**
     * Teste le fait que un utilisateur ne peut pas rejoindre un event qui est fini
     */
    public function testJoinEventDatePasse(ControllerTester $I): void
    {
        UserFactory::createMany(10, ['isAdmin' => true]);
        UserFactory::createMany(20); 
        SponsorsFactory::createMany(10);

        $user = UserFactory::createOne(['verified' => true]);
        $event = EventsFactory::createOne([
            'endDatetime' => new \DateTime('-2 days')
        ]);

        $I->amLoggedInAs($user->_real());
        $I->amOnPage('/events/' . $event->getId() . '/join');

        $I->seeInCurrentUrl('/events/' . $event->getId());
        $I->see('Impossible de rejoindre : l\'évènement est terminé ! ', '.alert-danger');
    }
}