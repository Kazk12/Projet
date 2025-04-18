<?php

namespace App\Controller;

use App\Entity\User;
use App\Factory\ConversationFactory;
use App\Repository\ConversationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @method User|null getUser()
 */
final class ConversationController extends AbstractController
{
    public function __construct(
        private readonly ConversationRepository $conversationRepository,
        private readonly ConversationFactory $factory
    )
    {
    }
    
    #[Route('/conversation/users/{recipient}', name: 'conversation.show')]
    public function show(?User $recipient): Response
    {
        if (!$recipient) {
            $this->addFlash('error', 'Utilisateur non trouvé');
            return $this->redirectToRoute('app_home');
        }

        $sender = $this->getUser();
        
        if (!$sender) {
            return $this->redirectToRoute('app_login');
        }
        
        // Vérifier si une conversation existe déjà entre ces utilisateurs
        $conversation = $this->conversationRepository->findByUsers($sender, $recipient);

        // Si la conversation n'existe pas, en créer une nouvelle
        if (!$conversation) {
            $conversation = $this->factory->create($sender, $recipient);
        }
        
        return $this->render('conversation/show.html.twig', [
            'conversation' => $conversation
        ]);
    }
}
