<?php

namespace App\DataFixtures;

use App\Entity\Statut;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class StatutFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $statuts = ['ami', 'bloquer'];

        foreach (UserFixtures::USERS as $key1 => $userData1) {
            foreach (UserFixtures::USERS as $key2 => $userData2) {
                if ($key1 !== $key2) {
                    foreach ($statuts as $statutName) {
                        $statut = new Statut();
                        $statut->setStatut($statutName);
                        $statut->setUser($this->getReference('user_' . $key1, User::class));
                        $statut->setOtherUser($this->getReference('user_' . $key2, User::class));

                        $manager->persist($statut);
                    }
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}