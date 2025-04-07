<?php

namespace App\DataFixtures;

use App\Entity\Announce;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public const COMMENTS = [
        'Super manga, j\'ai adoré !',
        'Je recommande vivement cette lecture.',
        'Pas vraiment convaincu par cet opus...',
        'L\'histoire est captivante du début à la fin !',
        'Les dessins sont magnifiques, mais l\'intrigue est un peu faible.',
        'Un chef-d\'œuvre incontournable !',
        'Je l\'ai lu d\'une traite, impossible de le lâcher.',
        'Personnages bien développés, je me suis attaché à eux.',
        'Je suis impatient de lire la suite !',
        'Parfait pour les débutants en manga.',
        'L\'auteur nous surprend encore avec ce tome.',
        'J\'ai été déçu par la fin, ça méritait mieux.',
        'Les combats sont spectaculaires dans ce volume.',
        'C\'est devenu mon manga préféré !',
        'Le meilleur tome de la série selon moi.',
        'Je cherche à acheter les tomes suivants, si quelqu\'un en a.',
        'Bonne lecture mais un peu courte.',
        'Quel talent de dessin, c\'est impressionnant !',
        'J\'ai pleuré à la fin, tellement émouvant.',
        'Le scénario est un peu prévisible mais reste agréable.',
    ];

    public function load(ObjectManager $manager): void
    {
        // Récupérer les clés des utilisateurs avec le rôle ROLE_USER
        $userKeys = [];
        foreach (UserFixtures::USERS as $key => $userData) {
            if (in_array('ROLE_USER', $userData['roles'])) {
                $userKeys[] = $key;
            }
        }
        
        // Pour chaque annonce
        foreach (UserFixtures::USERS as $userKey => $userData) {
            if (in_array('ROLE_USER', $userData['roles'])) {
                foreach (AnnounceFixtures::ANNOUNCES as $announceKey => $announceData) {
                    // Créer entre 1 et 5 commentaires par annonce
                    $commentCount = rand(1, 5);
                    for ($i = 0; $i < $commentCount; $i++) {
                        $comment = new Comment();
                        $commentContent = self::COMMENTS[array_rand(self::COMMENTS)];
                        $comment->setContent($commentContent);
                        
                        // Date de création entre 1 et 60 jours dans le passé
                        $daysAgo = rand(1, 60);
                        $comment->setCreatedAt(new \DateTimeImmutable('-' . $daysAgo . ' days'));
                        
                        // Sélectionner un utilisateur aléatoire différent du créateur de l'annonce
                        do {
                            $randomUserKey = $userKeys[array_rand($userKeys)];
                        } while ($randomUserKey === $userKey);
                        
                        $comment->setUser($this->getReference('user_' . $randomUserKey, User::class));
                        $comment->setAnnounce($this->getReference('announce_' . $userKey . '_' . $announceKey, Announce::class));
                        
                        $manager->persist($comment);
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
            AnnounceFixtures::class,
        ];
    }
}
