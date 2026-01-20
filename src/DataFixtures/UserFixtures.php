<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Modules\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const USER_CLIENT = 'user_client';
    public const USER_MANAGER = 'user_manager';

    public function load(ObjectManager $manager): void
    {
        $client = new User();
        $client->setEmail('client@test.local');
        $client->setName('Test Client');
        $client->setRole('ROLE_USER');
        $client->setCreatedAt(new \DateTimeImmutable());

        $managerUser = new User();
        $managerUser->setEmail('manager@test.local');
        $managerUser->setName('Order Manager');
        $managerUser->setRole('ROLE_MANAGER');
        $managerUser->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($client);
        $manager->persist($managerUser);

        $this->addReference(self::USER_CLIENT, $client);
        $this->addReference(self::USER_MANAGER, $managerUser);

        $manager->flush();
    }
}
