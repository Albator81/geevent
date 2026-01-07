<?php

namespace Tests\Unit;

use App\Entity\Annonces;
use App\Entity\Events;
use App\Entity\User;
use Tests\Support\UnitTester;

class UserTest extends \Codeception\Test\Unit
{
    protected UnitTester $tester;

    public function testUsername()
    {
        $user = new User();
        $user->setUsername('testuser');
        $this->assertEquals('testuser', $user->getUsername());
        $this->assertEquals('testuser', $user->getUserIdentifier());
    }

    public function testEmail()
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $this->assertEquals('test@example.com', $user->getEmail());
    }

    public function testRoles()
    {
        $user = new User();
        $this->assertContains('ROLE_USER', $user->getRoles());

        $user->setRoles(['ROLE_ADMIN']);
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testPassword()
    {
        $user = new User();
        $user->setPassword('hashedpassword');
        $this->assertEquals('hashedpassword', $user->getPassword());
    }

    public function testAdminStatus()
    {
        $user = new User();
        $user->setIsAdmin(true);
        $this->assertTrue($user->isAdmin());
        $user->setIsAdmin(false);
        $this->assertFalse($user->isAdmin());
    }

    public function testVerifiedStatus()
    {
        $user = new User();
        $user->setVerified(true);
        $this->assertTrue($user->isVerified());
        $user->setVerified(false);
        $this->assertFalse($user->isVerified());
    }

    public function testVerificationToken()
    {
        $user = new User();
        $user->setVerificationToken('token123');
        $this->assertEquals('token123', $user->getVerificationToken());
    }

    public function testToString()
    {
        $user = new User();
        $user->setUsername('testuser');
        $this->assertEquals('testuser', (string)$user);
    }

    public function testAnnoncesCollection()
    {
        $user = new User();
        $annonce = new Annonces();
        
        $user->addAnnonce($annonce);
        $this->assertCount(1, $user->getAnnonces());
        $this->assertTrue($user->getAnnonces()->contains($annonce));
        $this->assertSame($user, $annonce->getSentBy());

        $user->removeAnnonce($annonce);
        $this->assertCount(0, $user->getAnnonces());
        $this->assertFalse($user->getAnnonces()->contains($annonce));
        $this->assertNull($annonce->getSentBy());
    }

    public function testEventsCollection()
    {
        $user = new User();
        $event = new Events();
        
        $user->addEvent($event);
        $this->assertCount(1, $user->getEvents());
        $this->assertTrue($user->getEvents()->contains($event));
        $this->assertSame($user, $event->getCreatedBy());

        $user->removeEvent($event);
        $this->assertCount(0, $user->getEvents());
        $this->assertFalse($user->getEvents()->contains($event));
        $this->assertNull($event->getCreatedBy());
    }

    public function testEventsJoinedCollection()
    {
        $user = new User();
        $event = new Events();
        
        $user->addEventsJoined($event);
        $this->assertCount(1, $user->getEventsJoined());
        $this->assertTrue($user->getEventsJoined()->contains($event));

        $user->removeEventsJoined($event);
        $this->assertCount(0, $user->getEventsJoined());
        $this->assertFalse($user->getEventsJoined()->contains($event));
    }
}