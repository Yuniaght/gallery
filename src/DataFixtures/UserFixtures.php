<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserFixtures extends Fixture
{
    private object $hasher;

    /**
     * UserFixtures constructor
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(UserPasswordHasherInterface $hasher, private readonly SluggerInterface $slugger){
        $this->hasher = $hasher;
    }

    private array $gender = ["male", "female"];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 1; $i < 50; $i++) {
            $gender = $faker->randomElement($this->gender);
            $user = new User;
            $user->setFirstName($faker->firstName($gender))
                 ->setLastName($faker->lastName())
                 ->setUsername($faker->userName())
                 ->setEmail($this->slugger->slug($user->getFirstName()). $this->slugger->slug($user->getLastName())."@" .$faker->domainName())
                 ->setImage(rand(1,133).".png")
                 ->setPassword($this->hasher->hashPassword($user, "password"))
                 ->setIsActive($faker->boolean(80))
                 ->setJoinedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-3 years", "now")))
                 ->setEditedAt(new \DateTimeImmutable())
                 ->setRoles(["ROLE_USER"]);
            $manager->persist($user);
        }
        $manager->flush();

        $user = new User;
        $user->setFirstName("Bernard")
             ->setLastName("Minet")
             ->setUsername("Berminettes")
             ->setEmail("bernardminet@gmail.com")
             ->setImage("bm.jpg")
             ->setPassword($this->hasher->hashPassword($user, "password"))
             ->setIsActive(1)
             ->setJoinedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-3 years", "now")))
             ->setEditedAt(new \DateTimeImmutable())
             ->setRoles(["ROLE_MODERATOR"]);
        $manager->persist($user);
        $manager->flush();

        $user = new User;
        $user->setFirstName("Jean-Baptiste")
            ->setLastName("de Vulder")
            ->setUsername("Yuniaght")
            ->setEmail("devulder.jeanbaptiste@gmail.com")
            ->setImage("jbdv.jpg")
            ->setPassword($this->hasher->hashPassword($user, "password"))
            ->setIsActive(1)
            ->setJoinedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-3 years", "now")))
            ->setEditedAt(new \DateTimeImmutable())
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user);
        $manager->flush();
    }
}
