<?php

declare(strict_types=1);


namespace Tests\Functional;

use Tests\Support\FunctionalTester;
use App\Factory\UserFactory;

final class LoginCest
{
    public function _before(FunctionalTester $I): void
    {
        // Code here will be executed before each test.
    }

    public function tryLogin(FunctionalTester $I): void
        {
            $u = UserFactory::createOne([
                "username" => "Testing", 
                "password" => "passwordtest",
            ]);

            $I->amOnPage("/login");
            
            $I->submitForm('form[name="login"]', [
                'username' => 'Testing',
                'password' => 'passwordtest',
            ]);

            $I->seeCurrentRouteIs("app_profile");
        }
}
