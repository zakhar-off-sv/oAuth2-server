<?php

namespace App\Application\DataFixtures;

use App\Domain\Model\Client;
use App\Domain\Model\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create("email-{$i}@dot818.com", "User{$i} Name");
            $password = $this->encoder->encodePassword($user, "pass_{$i}");
            $user->setPassword($password);
            $user->setActive(true);
            $manager->persist($user);
        }

        for ($i = 1; $i <= 5; $i++) {
            $client = Client::create("Client {$i}");
            $client->setSecret(hash('sha512', $client->getName()));
            $client->setRedirect(['https://google.com/cb']);
            $client->setConfidential(rand(0, 1) ? true : false);
            $client->setActive(true);
            $manager->persist($client);
        }

        $manager->flush();
    }
}
