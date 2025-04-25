<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Announce;
use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Test d'intégration pour l'entité Announce
 */
class AnnounceIntegrationTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private User $testUser;
    private Book $testBook;

    /**
     * Configuration initiale pour les tests
     */
    protected function setUp(): void
    {
        self::bootKernel();
        
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        
        // Génération d'un email unique pour éviter les erreurs de contrainte
        $uniqueEmail = 'test_' . uniqid() . '@example.com';
        
        // Création d'un utilisateur de test
        $this->testUser = new User();
        $this->testUser->setEmail($uniqueEmail);
        $this->testUser->setPseudo('TestUser');
        $this->testUser->setRoles(['ROLE_USER']);
        $this->testUser->setIsVerified(true);
        $this->testUser->setCreatedAt(new \DateTimeImmutable());
        
        // Hashage du mot de passe
        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $hashedPassword = $passwordHasher->hashPassword($this->testUser, 'password123');
        $this->testUser->setPassword($hashedPassword);
        
        // Création d'un livre de test avec les propriétés correctes
        $this->testBook = new Book();
        $this->testBook->setTitle('Test Book');
        $this->testBook->setAuthor('Test Author');
        $this->testBook->setImg('test.jpg');
        $this->testBook->setCreatedAt(new \DateTimeImmutable());
        
        // Persistance des entités de test
        $this->entityManager->persist($this->testUser);
        $this->entityManager->persist($this->testBook);
        $this->entityManager->flush();
    }

    /**
     * Test de création d'une nouvelle annonce
     */
    public function testCreateAnnounce(): void
    {
        // Création d'une nouvelle annonce
        $announce = new Announce();
        $announce->setContent('Contenu de test pour l\'annonce');
        $announce->setRate(5);
        $announce->setBook($this->testBook);
        $announce->setUser($this->testUser);
        
        // Persistance de l'annonce
        $this->entityManager->persist($announce);
        $this->entityManager->flush();
        
        // Vérification du résultat
        $this->assertNotNull($announce->getId(), 'L\'ID de l\'annonce devrait être défini après la persistance');
        
        // Récupération depuis la base de données pour confirmer la persistance
        $savedAnnounce = $this->entityManager->getRepository(Announce::class)->find($announce->getId());
        
        $this->assertNotNull($savedAnnounce);
        $this->assertEquals('Contenu de test pour l\'annonce', $savedAnnounce->getContent());
        $this->assertEquals(5, $savedAnnounce->getRate());
        $this->assertEquals($this->testBook->getId(), $savedAnnounce->getBook()->getId());
        $this->assertEquals($this->testUser->getId(), $savedAnnounce->getUser()->getId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $savedAnnounce->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $savedAnnounce->getUpdatedAt());
    }

    /**
     * Test de mise à jour d'une annonce existante
     */
    public function testUpdateAnnounce(): void
    {
        // Création d'une annonce pour le test
        $announce = new Announce();
        $announce->setContent('Contenu initial');
        $announce->setRate(3);
        $announce->setBook($this->testBook);
        $announce->setUser($this->testUser);
        
        $this->entityManager->persist($announce);
        $this->entityManager->flush();
        
        // Récupération de l'ID
        $id = $announce->getId();
        $this->assertNotNull($id);
        
        // Mémorisation de la date de mise à jour
        $originalUpdatedAt = $announce->getUpdatedAt();
        
        // Attendre un peu pour s'assurer que la date de mise à jour sera différente
        sleep(1);
        
        // Modification de l'annonce
        $announce->setContent('Contenu modifié');
        $announce->setRate(4);
        $announce->setUpdatedAt(new \DateTimeImmutable());
        
        $this->entityManager->flush();
        
        // Effacer le contexte d'EntityManager pour forcer un rechargement depuis la BD
        $this->entityManager->clear();
        
        // Récupérer l'annonce mise à jour depuis la base de données
        $updatedAnnounce = $this->entityManager->getRepository(Announce::class)->find($id);
        
        // Vérification des modifications
        $this->assertEquals('Contenu modifié', $updatedAnnounce->getContent());
        $this->assertEquals(4, $updatedAnnounce->getRate());
        $this->assertGreaterThan($originalUpdatedAt, $updatedAnnounce->getUpdatedAt());
    }

    /**
     * Test de suppression d'une annonce
     */
    public function testRemoveAnnounce(): void
    {
        // Création d'une annonce pour le test
        $announce = new Announce();
        $announce->setContent('Annonce à supprimer');
        $announce->setRate(2);
        $announce->setBook($this->testBook);
        $announce->setUser($this->testUser);
        
        $this->entityManager->persist($announce);
        $this->entityManager->flush();
        
        $id = $announce->getId();
        $this->assertNotNull($id);
        
        // Suppression de l'annonce
        $this->entityManager->remove($announce);
        $this->entityManager->flush();
        
        // Vérification que l'annonce a été supprimée
        $deletedAnnounce = $this->entityManager->getRepository(Announce::class)->find($id);
        $this->assertNull($deletedAnnounce, 'L\'annonce devrait avoir été supprimée');
    }

    /**
     * Test d'ajout d'un like à une annonce
     */
    public function testAddLikeToAnnounce(): void
    {
        // Création d'une annonce
        $announce = new Announce();
        $announce->setContent('Annonce pour test de like');
        $announce->setRate(5);
        $announce->setBook($this->testBook);
        $announce->setUser($this->testUser);
        
        $this->entityManager->persist($announce);
        $this->entityManager->flush();
        
        // Création d'un nouveau UserLikeAnnounce
        $userLikeAnnounce = new \App\Entity\UserLikeAnnounce();
        $userLikeAnnounce->setUser($this->testUser);
        $userLikeAnnounce->setAnnounce($announce);
        
        // Ajout à la relation
        $announce->addUserLikeAnnounce($userLikeAnnounce);
        
        // Persistance
        $this->entityManager->persist($userLikeAnnounce);
        $this->entityManager->flush();
        
        // Vérification
        $this->assertCount(1, $announce->getUserLikeAnnounces());
        $this->assertSame($this->testUser, $announce->getUserLikeAnnounces()->first()->getUser());
    }

    /**
     * Nettoyage après les tests
     */
    protected function tearDown(): void
    {
        try {
            // Il faut supprimer les commentaires avant les annonces
            // à cause des contraintes de clé étrangère
            $this->entityManager->createQuery('DELETE FROM App\Entity\Comment')->execute();
            
            // Ensuite on peut supprimer les likes et les annonces
            $this->entityManager->createQuery('DELETE FROM App\Entity\UserLikeAnnounce')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Announce')->execute();
            
            // Suppression des entités de test
            if ($this->testUser && $this->testUser->getId()) {
                $this->entityManager->remove($this->testUser);
            }
            
            if ($this->testBook && $this->testBook->getId()) {
                $this->entityManager->remove($this->testBook);
            }
            
            $this->entityManager->flush();
        } catch (\Exception $e) {
            // En cas d'erreur pendant le nettoyage, on l'affiche mais on ne fait pas échouer le test
            echo "Erreur lors du nettoyage: " . $e->getMessage() . PHP_EOL;
        }
        
        // Fermeture de l'EntityManager
        $this->entityManager->close();
        
        parent::tearDown();
    }
}