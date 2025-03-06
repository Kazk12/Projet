<?php 

namespace App\Factory;

use App\Entity\Conversation;
use App\Entity\User;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;

class MessageFactory
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        
    }

    public function create(Conversation $conversation, User $author, string $content): Message
    {
        $message = new Message();

        $message->setConversation($conversation);
        $message->setAuthor($author);
        $message->setContent($content);
        $message->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }
}