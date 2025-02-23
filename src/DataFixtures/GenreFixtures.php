<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GenreFixtures extends Fixture
{
    public const GENRES = [
        'Shonen',
        'Shojo',
        'Seinen',
        'Kodomo',
        'Isekai',
        'Mecha',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::GENRES as $key => $genreName) {
            $genre = new Genre();
            $genre->setName($genreName);
            $manager->persist($genre);

            // Ajouter une référence pour chaque genre
            $this->addReference('genre_' . $key, $genre);
        }

        $manager->flush();
    }
}