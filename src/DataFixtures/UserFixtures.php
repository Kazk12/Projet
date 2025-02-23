<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public const USERS = [
        'admin' => [
            'email' => 'admin@admin.com',
            'pseudo' => 'admin',
            'roles' => ['ROLE_ADMIN'],
            'password' => 'adminadmin',
            'isVerified' => true
        ],
        'test' => [
            'email' => 'test@test.com',
            'pseudo' => 'testuser',
            'roles' => ['ROLE_USER'],
            'password' => 'testtest',
            'isVerified' => true
        ],
        'test2' => ['email' => 'test2@test2.com', 'pseudo' => 'testuser2', 'roles' => ['ROLE_USER'], 'password' => 'test2test2', 'isVerified' => true],
        'test3' => ['email' => 'test3@test3.com', 'pseudo' => 'testuser3', 'roles' => ['ROLE_USER'], 'password' => 'test3test3', 'isVerified' => true],
        'test4' => ['email' => 'test4@test4.com', 'pseudo' => 'testuser4', 'roles' => ['ROLE_USER'], 'password' => 'test4test4', 'isVerified' => true],
        'test5' => ['email' => 'test5@test5.com', 'pseudo' => 'testuser5', 'roles' => ['ROLE_USER'], 'password' => 'test5test5', 'isVerified' => true],
        'test6' => ['email' => 'test6@test6.com', 'pseudo' => 'testuser6', 'roles' => ['ROLE_USER'], 'password' => 'test6test6', 'isVerified' => true],
        'notverified' => [
            'email' => 'notverified@notverified.com',
            'pseudo' => 'notverifieduser',
            'roles' => ['ROLE_USER'],
            'password' => 'notverified',
            'isVerified' => false
        ],
        'deletedone' => [
            'email' => 'deletedone@deletedone.com',
            'pseudo' => 'deleteduser',
            'roles' => ['ROLE_USER'],
            'password' => 'deletedone',
            'isVerified' => true
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $key => $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setPseudo($userData['pseudo']);
            $user->setRoles($userData['roles']);
            $user->setIsVerified($userData['isVerified']);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $userData['password']
            );
            $user->setPassword($hashedPassword);

            $manager->persist($user);

            // Créer une référence pour une utilisation ultérieure si nécessaire
            $this->addReference('user_' . $key, $user);
        }

        $manager->flush();
    }
}