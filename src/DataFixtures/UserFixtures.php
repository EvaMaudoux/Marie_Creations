<?php

namespace App\DataFixtures;

use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private object $hasher;
    private array $genders = ['male', 'female'];

    public function __construct(UserPasswordHasherInterface $hasher) {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $slug = new Slugify();

        for($i = 1; $i <= 30; $i++) {
            $user = new User();
            $gender = $faker->randomElement($this->genders);
            $user   ->setFirstName($faker->firstName)
                    ->setLastName($faker->lastName)
                    ->setEmail($slug->slugify($user->getFirstName()) . '.' . $slug->slugify($user->getLastName()) . '@' . $faker->freeEmailDomain());
            $gender = $gender == 'male' ? 'm' : 'f';
            $user   ->setImageName($i . $gender . '.jpg')
                    ->setPassword($this->hasher->hashPassword($user, 'password'))
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setUpdatedAt(new \DateTimeImmutable())
                    ->setIsDisabled($faker->boolean(5))
                    ->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }

        // Admin Marie Dumont
        $user = new User();
        $user   ->setFirstName('Marie')
                ->setLastName('Dumont')
                ->setEmail('marie.dumont@gmail.com')
                ->setImageName('admin.jpg')
                ->setPassword($this->hasher->hashPassword($user, 'password'))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setIsDisabled(false)
                ->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        // Super admin Eva Maudoux
        $user = new User();
        $user   ->setFirstName('Eva')
                ->setLastName('Maudoux')
                ->setEmail('evamaudoux@gmail.com')
                ->setImageName('superadmin.jpg')
                ->setPassword($this->hasher->hashPassword($user, 'password'))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setIsDisabled(false)
                ->setRoles(['ROLE_SUPER_ADMIN']);
        $manager->persist($user);

        $manager->flush();
    }
}
