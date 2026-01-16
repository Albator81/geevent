<?php

namespace App\Tests\Controller\Sponsors;

use App\Factory\SponsorsFactory;
use App\Factory\UserFactory;
use App\Tests\Support\ControllerTester;

class easyAdminCest
{
    public function dbOnSponsorAdminTab(ControllerTester $I): void
    {
        $admin = UserFactory::createOne(['isAdmin' => true, 'roles' => ['ROLE_ADMIN']]);
        $I->amLoggedInAs($admin->_real());
        SponsorsFactory::createOne(['name' => 'Je mange du kak']);

        $I->amOnPage('/admin/sponsors');

        $I->see('Je mange du kak');
    }
}
