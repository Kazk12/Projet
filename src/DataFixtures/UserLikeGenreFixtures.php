<?php

namespace App\DataFixtures;

use App\Entity\UserLikeGenre;
use App\Entity\User;
use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserLikeGenreFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Récupérer les clés des utilisateurs avec le rôle ROLE_USER
        $userKeys = [];
        foreach (UserFixtures::USERS as $key => $userData) {
            if (in_array('ROLE_USER', $userData['roles'])) {
                $userKeys[] = $key;
            }
        }
        
        // Pour chaque utilisateur, ajouter des genres qu'il aime (50% de chance)
        foreach ($userKeys as $userKey) {
            foreach (GenreFixtures::GENRES as $genreKey => $genreData) {
                // 50% de chance que l'utilisateur aime ce genre
                if (rand(0, 1) === 1) {
                    $userLikeGenre = new UserLikeGenre();
                    $userLikeGenre->setUser($this->getReference('user_' . $userKey, User::class));
                    $userLikeGenre->setGenre($this->getReference('genre_' . $genreKey, Genre::class));
                    $manager->persist($userLikeGenre);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            GenreFixtures::class,
        ];
    }
}