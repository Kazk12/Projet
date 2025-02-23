<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Genre;
use App\Entity\TypeBook;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TypeBookFixtures extends Fixture implements DependentFixtureInterface
{
    public const TYPE_BOOKS = [
        ['book' => 'book_0', 'genre' => 'genre_0'], // My Hero Academia - Shonen
        ['book' => 'book_1', 'genre' => 'genre_0'], // Boruto - Shonen
        ['book' => 'book_2', 'genre' => 'genre_0'], // One Piece - Shonen
        ['book' => 'book_3', 'genre' => 'genre_2'], // Attack on Titan - Seinen
        ['book' => 'book_4', 'genre' => 'genre_0'], // Bleach - Shonen
        ['book' => 'book_5', 'genre' => 'genre_0'], // Dragon Ball - Shonen
        ['book' => 'book_6', 'genre' => 'genre_1'], // Sailor Moon - Shojo
        ['book' => 'book_7', 'genre' => 'genre_1'], // Cardcaptor Sakura - Shojo
        ['book' => 'book_8', 'genre' => 'genre_2'], // Inuyasha - Seinen
        ['book' => 'book_9', 'genre' => 'genre_0'], // Hunter x Hunter - Shonen
        ['book' => 'book_10', 'genre' => 'genre_2'], // Death Note - Seinen
        ['book' => 'book_11', 'genre' => 'genre_2'], // Berserk - Seinen
        ['book' => 'book_12', 'genre' => 'genre_0'], // Black Clover - Shonen
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TYPE_BOOKS as $typeBookData) {
            $typeBook = new TypeBook();
            
            $typeBook->setBook($this->getReference($typeBookData['book'], Book::class));
            $typeBook->setGenre($this->getReference($typeBookData['genre'], Genre::class));

            $manager->persist($typeBook);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BookFixtures::class,
            GenreFixtures::class,
        ];
    }
}