<?php

namespace App\Tests\Controller\Sponsors;

use App\Factory\UserFactory;
use App\Tests\Support\ControllerTester;
use Codeception\Util\HttpCode;

class AdminAccessCest
{
    public function accessIsRestrictedToAuthenticatedUsers(ControllerTester $I): void
    {
        $I->amOnPage('/admin');
        $I->seeCurrentRouteIs('app_login');
    }

}
