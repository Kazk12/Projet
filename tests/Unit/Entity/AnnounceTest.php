<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Announce;
use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\UserLikeAnnounce;
use PHPUnit\Framework\TestCase;

class AnnounceTest extends TestCase
{
    private Announce $announce;
    private User $user;
    private Book $book;
    
    protected function setUp(): void
    {
        $this->announce = new Announce();
        $this->user = new User();
        $this->book = new Book();
    }
    
    public function testConstructor(): void
    {
        // Vérification que les propriétés sont correctement initialisées
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->announce->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->announce->getUpdatedAt());
        $this->assertEmpty($this->announce->getUserLikeAnnounces());
        $this->assertEmpty($this->announce->getComments());
    }
    
    public function testImageUrlGetterAndSetter(): void
    {
        // Valeur null par défaut
        $this->assertNull($this->announce->getImageUrl());
        
        // Test avec valeur
        $imageUrl = 'image.jpg';
        $this->announce->setImageUrl($imageUrl);
        $this->assertSame($imageUrl, $this->announce->getImageUrl());
        
        // Test avec null
        $this->announce->setImageUrl(null);
        $this->assertNull($this->announce->getImageUrl());
    }
    
    public function testRateGetterAndSetter(): void
    {
        // Valeur null par défaut
        $this->assertNull($this->announce->getRate());
        
        // Test avec valeur
        $rate = 5;
        $this->announce->setRate($rate);
        $this->assertSame($rate, $this->announce->getRate());
        
        // Test avec null
        $this->announce->setRate(null);
        $this->assertNull($this->announce->getRate());
    }
    
    public function testContentGetterAndSetter(): void
    {
        // Valeur null par défaut
        $this->assertNull($this->announce->getContent());
        
        // Test avec valeur
        $content = 'Description de test';
        $this->announce->setContent($content);
        $this->assertSame($content, $this->announce->getContent());
        
        // Test avec null
        $this->announce->setContent(null);
        $this->assertNull($this->announce->getContent());
    }
    
    public function testUserGetterAndSetter(): void
    {
        // Valeur null par défaut
        $this->assertNull($this->announce->getUser());
        
        // Test avec un utilisateur
        $this->announce->setUser($this->user);
        $this->assertSame($this->user, $this->announce->getUser());
        
        // Test avec null
        $this->announce->setUser(null);
        $this->assertNull($this->announce->getUser());
    }
    
    public function testBookGetterAndSetter(): void
    {
        // Valeur null par défaut
        $this->assertNull($this->announce->getBook());
        
        // Test avec un livre
        $this->announce->setBook($this->book);
        $this->assertSame($this->book, $this->announce->getBook());
        
        // Test avec null
        $this->announce->setBook(null);
        $this->assertNull($this->announce->getBook());
    }
    
    public function testDateTimeGettersAndSetters(): void
    {
        $createdAt = new \DateTimeImmutable('2023-01-01');
        $updatedAt = new \DateTimeImmutable('2023-01-02');
        
        $this->announce->setCreatedAt($createdAt);
        $this->announce->setUpdatedAt($updatedAt);
        
        $this->assertSame($createdAt, $this->announce->getCreatedAt());
        $this->assertSame($updatedAt, $this->announce->getUpdatedAt());
    }
    
    public function testAddAndRemoveUserLikeAnnounce(): void
    {
        $userLikeAnnounce = new UserLikeAnnounce();
        
        // Test d'ajout
        $this->announce->addUserLikeAnnounce($userLikeAnnounce);
        $this->assertCount(1, $this->announce->getUserLikeAnnounces());
        $this->assertContains($userLikeAnnounce, $this->announce->getUserLikeAnnounces());
        $this->assertSame($this->announce, $userLikeAnnounce->getAnnounce());
        
        // Test de non-duplication
        $this->announce->addUserLikeAnnounce($userLikeAnnounce);
        $this->assertCount(1, $this->announce->getUserLikeAnnounces());
        
        // Test de suppression
        $this->announce->removeUserLikeAnnounce($userLikeAnnounce);
        $this->assertCount(0, $this->announce->getUserLikeAnnounces());
        $this->assertNull($userLikeAnnounce->getAnnounce());
    }
    
    public function testAddAndRemoveComment(): void
    {
        $comment = new Comment();
        
        // Test d'ajout
        $this->announce->addComment($comment);
        $this->assertCount(1, $this->announce->getComments());
        $this->assertContains($comment, $this->announce->getComments());
        $this->assertSame($this->announce, $comment->getAnnounce());
        
        // Test de non-duplication
        $this->announce->addComment($comment);
        $this->assertCount(1, $this->announce->getComments());
        
        // Test de suppression
        $this->announce->removeComment($comment);
        $this->assertCount(0, $this->announce->getComments());
        $this->assertNull($comment->getAnnounce());
    }
    
    public function testThumbnailFileGetterAndSetter(): void
    {
        // Par défaut null
        $this->assertNull($this->announce->getThumbnailFile());
        
        // Mock d'un objet File
        $file = $this->createMock(\Symfony\Component\HttpFoundation\File\File::class);
        
        // Test de la mise à jour avec fichier
        $beforeUpdate = $this->announce->getUpdatedAt();
        
        // Attendre pour garantir une différence de timestamp
        usleep(1000);
        
        $this->announce->setThumbnailFile($file);
        
        $this->assertSame($file, $this->announce->getThumbnailFile());
        $this->assertNotSame($beforeUpdate, $this->announce->getUpdatedAt());
        
        // Test avec null
        $this->announce->setThumbnailFile(null);
        $this->assertNull($this->announce->getThumbnailFile());
    }
    
    public function testIdGetter(): void
    {
        $this->assertNull($this->announce->getId());
        
        // On ne peut pas tester l'ID généré sans interaction avec la base de données
    }
}