<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Announce;
use App\Entity\User;
use App\Entity\UserLikeAnnounce;
use PHPUnit\Framework\TestCase;

class UserLikeAnnounceTest extends TestCase
{
    private UserLikeAnnounce $userLikeAnnounce;
    private User $user;
    private Announce $announce;

    protected function setUp(): void
    {
        $this->userLikeAnnounce = new UserLikeAnnounce();
        $this->user = new User();
        $this->announce = new Announce();
    }

    public function testGetterAndSetterForUser(): void
    {
        // Test avec un utilisateur null par défaut
        $this->assertNull($this->userLikeAnnounce->getUser());

        // Test avec l'assignation d'un utilisateur
        $this->userLikeAnnounce->setUser($this->user);
        $this->assertSame($this->user, $this->userLikeAnnounce->getUser());

        // Test avec la réinitialisation à null
        $this->userLikeAnnounce->setUser(null);
        $this->assertNull($this->userLikeAnnounce->getUser());
    }

    public function testGetterAndSetterForAnnounce(): void
    {
        // Test avec une annonce null par défaut
        $this->assertNull($this->userLikeAnnounce->getAnnounce());

        // Test avec l'assignation d'une annonce
        $this->userLikeAnnounce->setAnnounce($this->announce);
        $this->assertSame($this->announce, $this->userLikeAnnounce->getAnnounce());

        // Test avec la réinitialisation à null
        $this->userLikeAnnounce->setAnnounce(null);
        $this->assertNull($this->userLikeAnnounce->getAnnounce());
    }

    public function testIdIsNullByDefault(): void
    {
        $this->assertNull($this->userLikeAnnounce->getId());
    }

    public function testEntityRelationshipConsistency(): void
    {
        // Configuration des relations bidirectionnelles
        $this->user->addUserLikeAnnounce($this->userLikeAnnounce);
        $this->announce->addUserLikeAnnounce($this->userLikeAnnounce);

        // Vérification de la cohérence des relations
        $this->assertContains($this->userLikeAnnounce, $this->user->getUserLikeAnnounces());
        $this->assertContains($this->userLikeAnnounce, $this->announce->getUserLikeAnnounces());
        $this->assertSame($this->user, $this->userLikeAnnounce->getUser());
        $this->assertSame($this->announce, $this->userLikeAnnounce->getAnnounce());

        // Test de suppression des relations
        $this->user->removeUserLikeAnnounce($this->userLikeAnnounce);
        $this->announce->removeUserLikeAnnounce($this->userLikeAnnounce);

        $this->assertNotContains($this->userLikeAnnounce, $this->user->getUserLikeAnnounces());
        $this->assertNotContains($this->userLikeAnnounce, $this->announce->getUserLikeAnnounces());
    }
}