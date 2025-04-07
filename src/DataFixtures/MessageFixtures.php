<?php

namespace App\DataFixtures;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    public const MESSAGES = [
        'Bonjour, comment vas-tu ?',
        'Salut ! Ça va bien, et toi ?',
        'Très bien merci ! Tu as lu le dernier chapitre de One Piece ?',
        'Pas encore, j\'attends d\'avoir un peu de temps ce weekend',
        'Tu verras, il est incroyable !',
        'Tu as aimé le dernier tome de My Hero Academia ?',
        'Oui, j\'ai adoré ! Les dessins sont magnifiques',
        'Est-ce que tu serais intéressé par un échange de mangas ?',
        'Bien sûr, qu\'est-ce que tu proposes ?',
        'J\'ai tous les tomes de Bleach, ça t\'intéresserait ?',
        'Je cherche à vendre ma collection de Dragon Ball, ça t\'intéresse ?',
        'Je viens de terminer Berserk, c\'est vraiment un chef-d\'œuvre',
        'Tu connais des boutiques de mangas pas trop chères ?',
        'Je te conseille le nouveau manga que j\'ai découvert, c\'est génial !',
        'Est-ce que tu vas à la convention manga le mois prochain ?'
    ];

    public function load(ObjectManager $manager): void
    {
        $userKeys = array_keys(UserFixtures::USERS);
        
        // Pour chaque paire d'utilisateurs qui ont une conversation
        for ($i = 0; $i < count($userKeys) - 1; $i++) {
            for ($j = $i + 1; $j < count($userKeys); $j++) {
                $userKey1 = $userKeys[$i];
                $userKey2 = $userKeys[$j];
                
                // Vérifier que les deux utilisateurs ont le rôle ROLE_USER
                $userData1 = UserFixtures::USERS[$userKey1];
                $userData2 = UserFixtures::USERS[$userKey2];
                
                if (in_array('ROLE_USER', $userData1['roles']) && in_array('ROLE_USER', $userData2['roles'])) {
                    // Récupérer la référence de la conversation entre ces deux utilisateurs
                    $conversation = $this->getReference('conversation_' . $userKey1 . '_' . $userKey2, Conversation::class);
                    
                    // Créer plusieurs messages dans cette conversation
                    for ($k = 0; $k < 5; $k++) {
                        $message = new Message();
                        $message->setContent(self::MESSAGES[array_rand(self::MESSAGES)]);
                        
                        // Alterner l'auteur des messages entre les deux utilisateurs
                        if ($k % 2 == 0) {
                            $message->setAuthor($this->getReference('user_' . $userKey1, User::class));
                        } else {
                            $message->setAuthor($this->getReference('user_' . $userKey2, User::class));
                        }
                        
                        $message->setCreatedAt(new \DateTimeImmutable('-' . rand(1, 30) . ' days'));
                        $message->setConversation($conversation);
                        
                        $manager->persist($message);
                    }
                }
            }
        }
        
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ConversationFixtures::class,
        ];
    }
}