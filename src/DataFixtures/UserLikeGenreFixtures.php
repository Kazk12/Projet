<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use App\Entity\User;
use App\Entity\UserLikeGenre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserLikeGenreFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach (UserFixtures::USERS as $userKey => $userData) {
            if (in_array('ROLE_USER', $userData['roles'])) {
                foreach (GenreFixtures::GENRES as $genreKey => $genreName) {
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