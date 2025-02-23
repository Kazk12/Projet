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