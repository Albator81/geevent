<?php

namespace App\Tests\Controller\Sponsors;

use App\Factory\SponsorsFactory;
use App\Factory\EventsFactory;
use App\Factory\UserFactory;
use App\Tests\Support\ControllerTester;

class ShowCest
{
    public function showDisplaysSponsorDetails(ControllerTester $I): void
    {
        $sponsor = SponsorsFactory::createOne([
            'name' => 'Acme Corp',
            'website_url' => 'https://acme.com'
        ]);

        $I->amOnPage('/sponsors/' . $sponsor->getId());
        $I->seeResponseCodeIsSuccessful();
        $I->see('Acme Corp', 'h1');
        $I->seeLink('Visiter le site web', 'https://acme.com');
    }
}

