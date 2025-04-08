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
            'img' => 'my_hero_academia.jpg',
        ],
        [
            'author' => 'Masashi Kishimoto',
            'title' => 'Boruto: Naruto Next Generations',
            'createdAt' => '2016-05-09',
            'img' => 'boruto.jpg',
        ],
        [
            'author' => 'Eiichiro Oda',
            'title' => 'One Piece',
            'createdAt' => '1997-07-22',
            'img' => 'one_piece.jpg',
        ],
        [
            'author' => 'Hajime Isayama',
            'title' => 'Attack on Titan',
            'createdAt' => '2009-09-09',
            'img' => 'attack_on_titan.jpg',
        ],
        [
            'author' => 'Tite Kubo',
            'title' => 'Bleach',
            'createdAt' => '2001-08-07',
            'img' => 'bleach.jpg',
        ],
        [
            'author' => 'Akira Toriyama',
            'title' => 'Dragon Ball',
            'createdAt' => '1984-12-03',
            'img' => 'dragon_ball.jpg',
        ],
        [
            'author' => 'Naoko Takeuchi',
            'title' => 'Sailor Moon',
            'createdAt' => '1991-12-28',
            'img' => 'sailor_moon.jpg',
        ],
        [
            'author' => 'Clamp',
            'title' => 'Cardcaptor Sakura',
            'createdAt' => '1996-05-01',
            'img' => 'cardcaptor_sakura.jpg',
        ],
        [
            'author' => 'Rumiko Takahashi',
            'title' => 'Inuyasha',
            'createdAt' => '1996-11-13',
            'img' => 'inuyasha.jpg',
        ],
        [
            'author' => 'Yoshihiro Togashi',
            'title' => 'Hunter x Hunter',
            'createdAt' => '1998-03-03',
            'img' => 'hunter_x_hunter.jpg',
        ],
        [
            'author' => 'Tsugumi Ohba',
            'title' => 'Death Note',
            'createdAt' => '2003-12-01',
            'img' => 'death_note.jpg',
        ],
        [
            'author' => 'Kentaro Miura',
            'title' => 'Berserk',
            'createdAt' => '1989-08-25',
            'img' => 'berserk.jpg',
        ],
        [
            'author' => 'Yūki Tabata',
            'title' => 'Black Clover',
            'createdAt' => '2015-02-16',
            'img' => 'black_clover.jpg',
        ],
        [
            'author' => 'Yoko Kamio',
            'title' => 'Boys Over Flowers',
            'createdAt' => '1992-10-01',
            'img' => 'boys_over_flowers.jpg',
        ],
        [
            'author' => 'Kaoru Mori',
            'title' => 'Emma',
            'createdAt' => '2002-08-30',
            'img' => 'emma.jpg',
        ],
        [
            'author' => 'Reki Kawahara',
            'title' => 'Sword Art Online',
            'createdAt' => '2009-04-10',
            'img' => 'sword_art_online.jpg',
        ],
        [
            'author' => 'Yuu Watase',
            'title' => 'Fushigi Yugi',
            'createdAt' => '1992-05-18',
            'img' => 'fushigi_yugi.jpg',
        ],
        [
            'author' => 'Clamp',
            'title' => 'Chobits',
            'createdAt' => '2000-09-01',
            'img' => 'chobits.jpg',
        ],
        [
            'author' => 'Hiro Mashima',
            'title' => 'Fairy Tail',
            'createdAt' => '2006-08-02',
            'img' => 'fairy_tail.jpg',
        ],
        [
            'author' => 'Junji Ito',
            'title' => 'Uzumaki',
            'createdAt' => '1998-01-12',
            'img' => 'uzumaki.jpg',
        ],
        [
            'author' => 'Takehiko Inoue',
            'title' => 'Slam Dunk',
            'createdAt' => '1990-10-01',
            'img' => 'slam_dunk.jpg',
        ],
        [
            'author' => 'Makoto Yukimura',
            'title' => 'Vinland Saga',
            'createdAt' => '2005-04-13',
            'img' => 'vinland_saga.jpg',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::BOOKS as $key => $bookData) {
            $book = new Book();
            $book->setAuthor($bookData['author']);
            $book->setTitle($bookData['title']);
            $book->setCreatedAt(new \DateTimeImmutable($bookData['createdAt']));
            $book->setImg($bookData['img']);
            $manager->persist($book);

            // Ajouter une référence pour chaque livre
            $this->addReference('book_' . $key, $book);
        }

        $manager->flush();
    }
}