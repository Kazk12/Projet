<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GenreFixtures extends Fixture
{
    public const GENRES = [
        [
            'name' => 'Shonen',
            'description' => 'Manga destiné principalement aux adolescents masculins, caractérisé par l\'action et l\'aventure.',
            'image' => 'shonen.jpg'
        ],
        [
            'name' => 'Shojo',
            'description' => 'Manga destiné principalement aux adolescentes, souvent centré sur les relations romantiques.',
            'image' => 'shojo.jpg'
        ],
        [
            'name' => 'Seinen',
            'description' => 'Manga pour un public adulte, abordant des thèmes matures et complexes.',
            'image' => 'seinen.jpg'
        ],
        [
            'name' => 'Josei',
            'description' => 'Manga destiné aux femmes adultes, traitant de la vie quotidienne et des relations réalistes.',
            'image' => 'josei.jpg'
        ],
        [
            'name' => 'Kodomo',
            'description' => 'Manga pour enfants, avec des histoires simples et éducatives.',
            'image' => 'kodomo.jpg'
        ],
        [
            'name' => 'Isekai',
            'description' => 'Genre où le protagoniste est transporté dans un autre monde fantastique.',
            'image' => 'isekai.jpg'
        ],
        [
            'name' => 'Mecha',
            'description' => 'Genre centré sur les robots géants et la science-fiction.',
            'image' => 'mecha.jpg'
        ],
        [
            'name' => 'Slice of Life',
            'description' => 'Manga représentant la vie quotidienne et les expériences ordinaires.',
            'image' => 'slice_of_life.jpg'
        ],
        [
            'name' => 'Fantasy',
            'description' => 'Univers imaginaires avec des éléments magiques et mythologiques.',
            'image' => 'fantasy.jpg'
        ],
        [
            'name' => 'Horror',
            'description' => 'Manga axé sur la peur et les éléments surnaturels terrifiants.',
            'image' => 'horror.jpg'
        ],
        [
            'name' => 'Sports',
            'description' => 'Manga centré sur les compétitions sportives et le dépassement de soi.',
            'image' => 'sports.jpg'
        ],
        [
            'name' => 'Romance',
            'description' => 'Genre axé sur les relations amoureuses et les émotions.',
            'image' => 'romance.jpg'
        ],
        [
            'name' => 'Cyberpunk',
            'description' => 'Univers futuriste dystopique avec des technologies avancées.',
            'image' => 'cyberpunk.jpg'
        ],
        [
            'name' => 'Post-Apocalyptique',
            'description' => 'Récits se déroulant après un cataclysme ayant détruit la civilisation.',
            'image' => 'post_apocalyptique.jpg'
        ],
        [
            'name' => 'Historical',
            'description' => 'Manga se déroulant dans des périodes historiques réelles ou alternatives.',
            'image' => 'historical.jpg'
        ],
        [
            'name' => 'Psychological',
            'description' => 'Manga explorant la psychologie des personnages et des thèmes profonds.',
            'image' => 'psychological.jpg'
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::GENRES as $key => $genreData) {
            $genre = new Genre();
            $genre->setName($genreData['name']);
            $genre->setDescription($genreData['description']);
            $genre->setImage($genreData['image']);
            $manager->persist($genre);

            // Ajouter une référence pour chaque genre
            $this->addReference('genre_' . $key, $genre);
        }

        $manager->flush();
    }
}