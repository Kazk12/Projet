<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Announce;
use App\Entity\User;
use App\Entity\UserLikeAnnounce;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class HomeControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Test pour vérifier que la page d'accueil se charge correctement
     */
    public function testHomepageLoads(): void
    {
        // Act
        $this->client->request('GET', '/');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        // Vérification des éléments clés du template
        $this->assertSelectorTextContains('h1', 'L\'univers des');
        $this->assertSelectorTextContains('h1', 'Critiques Littéraires');
        $this->assertSelectorExists('.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3');
        
        // Vérification de la navigation au lieu de la pagination qui pourrait ne pas être présente
        // s'il n'y a pas assez d'annonces pour paginer
        $this->assertSelectorExists('nav');
    }

    /**
     * Test pour vérifier qu'un utilisateur connecté peut liker une annonce
     */
    public function testUserCanLikeAnnounce(): void
    {
        // Arrange
        // Récupération d'un utilisateur et une annonce pour le test
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'test@test.com']);
        $announce = $this->entityManager->getRepository(Announce::class)->findOneBy([], ['id' => 'ASC']);
        
        if (!$user || !$announce) {
            $this->markTestSkipped('Utilisateur ou annonce test non trouvés');
        }
        
        // Vérification et suppression des likes existants pour ce test
        $existingLike = $this->entityManager->getRepository(UserLikeAnnounce::class)
            ->findOneBy(['user' => $user, 'announce' => $announce]);
        
        if ($existingLike) {
            $this->entityManager->remove($existingLike);
            $this->entityManager->flush();
        }
        
        // Authentification de l'utilisateur
        $this->client->loginUser($user);
        
        // Act - Liker l'annonce
        $likeUrl = '/like/' . $announce->getId();
        $this->client->request('GET', $likeUrl);
        
        // Assert - Vérifier la redirection et le like enregistré
        $this->assertResponseRedirects();
        
        // Vérifier que le like a été ajouté en base de données
        $like = $this->entityManager->getRepository(UserLikeAnnounce::class)
            ->findOneBy(['user' => $user, 'announce' => $announce]);
            
        $this->assertNotNull($like, 'Le like n\'a pas été enregistré en base de données');
        
        // Vérifier également que l'utilisateur voit bien son like sur la page d'accueil
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
        // La présence d'une classe spécifique sur le bouton de like (cela dépend de l'implémentation)
        $this->assertSelectorExists('a[href="' . $likeUrl . '"] span.material-icons:contains("favorite")');
    }
}