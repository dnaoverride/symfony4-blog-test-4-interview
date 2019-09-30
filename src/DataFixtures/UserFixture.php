<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // $product = manage Product();
        // $manager->persist($product);
        $this->setReference('user', $user);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
