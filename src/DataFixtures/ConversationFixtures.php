<?php

namespace App\DataFixtures;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ConversationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userKeys = array_keys(UserFixtures::USERS);
        
        // Créer des conversations entre différents utilisateurs
        for ($i = 0; $i < count($userKeys) - 1; $i++) {
            for ($j = $i + 1; $j < count($userKeys); $j++) {
                $userKey1 = $userKeys[$i];
                $userKey2 = $userKeys[$j];
                
                // Vérifier que les deux utilisateurs ont le rôle ROLE_USER
                $userData1 = UserFixtures::USERS[$userKey1];
                $userData2 = UserFixtures::USERS[$userKey2];
                
                if (in_array('ROLE_USER', $userData1['roles']) && in_array('ROLE_USER', $userData2['roles'])) {
                    $conversation = new Conversation();
                    $conversation->addUser($this->getReference('user_' . $userKey1, User::class));
                    $conversation->addUser($this->getReference('user_' . $userKey2, User::class));
                    
                    $manager->persist($conversation);
                    
                    // Ajouter une référence pour chaque conversation
                    $this->addReference('conversation_' . $userKey1 . '_' . $userKey2, $conversation);
                }
            }
        }
        
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}