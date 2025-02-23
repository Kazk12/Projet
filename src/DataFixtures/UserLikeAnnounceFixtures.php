<?php

namespace App\DataFixtures;

use App\Entity\Announce;
use App\Entity\User;
use App\Entity\UserLikeAnnounce;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserLikeAnnounceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach (UserFixtures::USERS as $userKey => $userData) {
            if (in_array('ROLE_USER', $userData['roles'])) {
                foreach (AnnounceFixtures::ANNOUNCES as $announceKey => $announceData) {
                    $userLikeAnnounce = new UserLikeAnnounce();
$userLikeAnnounce->setUser($this->getReference('user_' . $userKey, User::class));
$userLikeAnnounce->setAnnounce($this->getReference('announce_' . $userKey . '_' . $announceKey, Announce::class));
                    $manager->persist($userLikeAnnounce);
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