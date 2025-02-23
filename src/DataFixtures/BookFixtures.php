<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public const BOOKS = [
        [
            'author' => 'Kohei Horikoshi',
            'title' => 'My Hero Academia',
            'createdAt' => '2014-07-07',
        ],
        [
            'author' => 'Masashi Kishimoto',
            'title' => 'Boruto: Naruto Next Generations',
            'createdAt' => '2016-05-09',
        ],
        [
            'author' => 'Eiichiro Oda',
            'title' => 'One Piece',
            'createdAt' => '1997-07-22',
        ],
        [
            'author' => 'Hajime Isayama',
            'title' => 'Attack on Titan',
            'createdAt' => '2009-09-09',
        ],
        [
            'author' => 'Tite Kubo',
            'title' => 'Bleach',
            'createdAt' => '2001-08-07',
        ],
        [
            'author' => 'Akira Toriyama',
            'title' => 'Dragon Ball',
            'createdAt' => '1984-12-03',
        ],
        [
            'author' => 'Naoko Takeuchi',
            'title' => 'Sailor Moon',
            'createdAt' => '1991-12-28',
        ],
        [
            'author' => 'Clamp',
            'title' => 'Cardcaptor Sakura',
            'createdAt' => '1996-05-01',
        ],
        [
            'author' => 'Rumiko Takahashi',
            'title' => 'Inuyasha',
            'createdAt' => '1996-11-13',
        ],
        [
            'author' => 'Yoshihiro Togashi',
            'title' => 'Hunter x Hunter',
            'createdAt' => '1998-03-03',
        ],
        [
            'author' => 'Tsugumi Ohba',
            'title' => 'Death Note',
            'createdAt' => '2003-12-01',
        ],
        [
            'author' => 'Kentaro Miura',
            'title' => 'Berserk',
            'createdAt' => '1989-08-25',
        ],
        [
            'author' => 'Yūki Tabata',
            'title' => 'Black Clover',
            'createdAt' => '2015-02-16',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::BOOKS as $key => $bookData) {
            $book = new Book();
            $book->setAuthor($bookData['author']);
            $book->setTitle($bookData['title']);
            $book->setCreatedAt(new \DateTimeImmutable($bookData['createdAt']));
            $manager->persist($book);

            // Ajouter une référence pour chaque livre
            $this->addReference('book_' . $key, $book);
        }

        $manager->flush();
    }
}