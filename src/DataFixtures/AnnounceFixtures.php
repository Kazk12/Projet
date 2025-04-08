<?php

namespace App\DataFixtures;

use App\Entity\Announce;
use App\Entity\User;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AnnounceFixtures extends Fixture implements DependentFixtureInterface
{
    public const ANNOUNCES = [
        ['content' => 'First announce', 'imageUrl' => 'image1.jpg', 'rate' => 5, 'book' => 'book_0'],
        ['content' => 'Second announce', 'imageUrl' => 'image2.jpg', 'rate' => 4, 'book' => 'book_1'],
        ['content' => 'Third announce', 'imageUrl' => 'image3.jpg', 'rate' => 3, 'book' => 'book_2'],
        ['content' => 'Fourth announce', 'imageUrl' => 'image4.jpg', 'rate' => 2, 'book' => 'book_3'],
        ['content' => 'Fifth announce', 'imageUrl' => 'image5.jpg', 'rate' => 1, 'book' => 'book_4'],
        ['content' => 'New announce for Boys Over Flowers', 'imageUrl' => 'boys_over_flowers.jpg', 'rate' => 5, 'book' => 'book_13'],
        ['content' => 'New announce for Emma', 'imageUrl' => 'emma.jpg', 'rate' => 4, 'book' => 'book_14'],
        ['content' => 'New announce for Sword Art Online', 'imageUrl' => 'sword_art_online.jpg', 'rate' => 5, 'book' => 'book_15'],
        ['content' => 'New announce for Fushigi Yugi', 'imageUrl' => 'fushigi_yugi.jpg', 'rate' => 4, 'book' => 'book_16'],
        ['content' => 'New announce for Chobits', 'imageUrl' => 'chobits.jpg', 'rate' => 5, 'book' => 'book_17'],
        ['content' => 'New announce for Fairy Tail', 'imageUrl' => 'fairy_tail.jpg', 'rate' => 5, 'book' => 'book_18'],
        ['content' => 'New announce for Uzumaki', 'imageUrl' => 'uzumaki.jpg', 'rate' => 5, 'book' => 'book_19'],
        ['content' => 'New announce for Slam Dunk', 'imageUrl' => 'slam_dunk.jpg', 'rate' => 5, 'book' => 'book_20'],
        ['content' => 'New announce for Vinland Saga', 'imageUrl' => 'vinland_saga.jpg', 'rate' => 5, 'book' => 'book_21'],
        ['content' => 'Announce for My Hero Academia', 'imageUrl' => 'my_hero_academia.jpg', 'rate' => 5, 'book' => 'book_0'],
        ['content' => 'Announce for Boruto', 'imageUrl' => 'boruto.jpg', 'rate' => 4, 'book' => 'book_1'],
        ['content' => 'Announce for One Piece', 'imageUrl' => 'one_piece.jpg', 'rate' => 5, 'book' => 'book_2'],
        ['content' => 'Announce for Attack on Titan', 'imageUrl' => 'attack_on_titan.jpg', 'rate' => 4, 'book' => 'book_3'],
        ['content' => 'Announce for Bleach', 'imageUrl' => 'bleach.jpg', 'rate' => 5, 'book' => 'book_4'],
        ['content' => 'Announce for Dragon Ball', 'imageUrl' => 'dragon_ball.jpg', 'rate' => 5, 'book' => 'book_5'],
        ['content' => 'Announce for Sailor Moon', 'imageUrl' => 'sailor_moon.jpg', 'rate' => 4, 'book' => 'book_6'],
        ['content' => 'Announce for Cardcaptor Sakura', 'imageUrl' => 'cardcaptor_sakura.jpg', 'rate' => 5, 'book' => 'book_7'],
        ['content' => 'Announce for Inuyasha', 'imageUrl' => 'inuyasha.jpg', 'rate' => 4, 'book' => 'book_8'],
        ['content' => 'Announce for Hunter x Hunter', 'imageUrl' => 'hunter_x_hunter.jpg', 'rate' => 5, 'book' => 'book_9'],
        ['content' => 'Announce for Death Note', 'imageUrl' => 'death_note.jpg', 'rate' => 5, 'book' => 'book_10'],
        ['content' => 'Announce for Berserk', 'imageUrl' => 'berserk.jpg', 'rate' => 5, 'book' => 'book_11'],
        ['content' => 'Announce for Black Clover', 'imageUrl' => 'black_clover.jpg', 'rate' => 5, 'book' => 'book_12'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (UserFixtures::USERS as $key => $userData) {
            if (in_array('ROLE_USER', $userData['roles'])) {
                foreach (self::ANNOUNCES as $announceKey => $announceData) {
                    $announce = new Announce();
                    $announce->setContent($announceData['content']);
                    $announce->setImageUrl($announceData['imageUrl']);
                    $announce->setRate($announceData['rate']);
                    $announce->setCreatedAt(new \DateTimeImmutable());
                    $announce->setUpdatedAt(new \DateTimeImmutable());
                    $announce->setUser($this->getReference('user_' . $key, User::class));
                    $announce->setBook($this->getReference($announceData['book'], Book::class));

                    $manager->persist($announce);

                    // Ajouter une référence pour chaque annonce
                    $this->addReference('announce_' . $key . '_' . $announceKey, $announce);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            BookFixtures::class,
        ];
    }
}