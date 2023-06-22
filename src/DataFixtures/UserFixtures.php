<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public function __construct(UserPasswordHasherInterface $hasher) {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for($i = 1; $i <= 30; $i++) {
            $user = new User();
            $user   ->setFirstName($faker->firstName)
                    ->setLastName($faker->lastName)
                    ->setEmail($user->getFirstName() . '.' .$user->getLastName() . '@' . $faker->freeEmailDomain())
                    ->setPassword($this->hasher->hashPassword($user, 'password'))
                    ->setIsDisabled($faker->boolean(5))
                    ->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }

        // Super Admin Marie Dumont
        $user = new User();
        $user   ->setFirstName('Marie')
            ->setLastName('Dumont')
            ->setEmail('marie.dumont@gmail.com')
            ->setPassword($this->hasher->hashPassword($user, 'password'))
            ->setIsDisabled(false)
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $manager->flush();
    }
}
