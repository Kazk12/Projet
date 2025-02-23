<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $messages = [
            ['content' => 'Hello, how are you?', 'user' => 'test', 'receiver' => 'test2'],
            ['content' => 'I am fine, thank you!', 'user' => 'test2', 'receiver' => 'test'],
            ['content' => 'What are you doing?', 'user' => 'test3', 'receiver' => 'test4'],
            ['content' => 'Just working on a project.', 'user' => 'test4', 'receiver' => 'test3'],
            ['content' => 'Do you need any help?', 'user' => 'test5', 'receiver' => 'test6'],
            ['content' => 'Yes, that would be great!', 'user' => 'test6', 'receiver' => 'test5'],
        ];

        foreach ($messages as $messageData) {
            $message = new Message();
            $message->setContent($messageData['content']);
            $message->setUser($this->getReference('user_' . $messageData['user'], User::class));
            $message->setReceiver($this->getReference('user_' . $messageData['receiver'], User::class));
            $message->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($message);
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