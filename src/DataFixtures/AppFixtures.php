<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setEmail('test@gmail.com')
            ->setLogin('test')
            ->setFirstname('luc')
            ->setLastname('martin')
            ->setRoles(['ROLE_User']);

        $password = $this->encoder->hashPassword($user, 'aaaaa');
        $user->setPassword($password);
        $manager->persist($user);

        $product = new Product();

        $product
            ->setName('some name')
            ->setDescription('some description')
            ->setPhoto('path/to/picture')
            ->setPrice(3000);

        $manager->persist($product);

        $cart = new Cart();

        $manager->persist($cart);
        $order = new Order();

        $manager->persist($order);


        $manager->flush();
    }
}