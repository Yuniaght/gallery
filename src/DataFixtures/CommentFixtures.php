<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Work;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            WorkFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $users = $manager->getRepository(User::class)->findAll();
        $works = $manager->getRepository(Work::class)->findAll();

        foreach ($works as $work) {
            $numberOfComments = mt_rand(0, 15);
            shuffle($users);
            $selectedUsers = array_slice($users, 0, $numberOfComments);
            foreach ($selectedUsers as $user) {
                $comment = new Comment();
                $comment->setTitle($faker->sentence(3))
                        ->setNote(mt_rand(0, 5)) // Note entre 1 et 5 généralement
                        ->setDescription($faker->paragraph(1, true))
                        ->setPublishedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-2years", "today")))
                        ->setIsPublic($faker->boolean(90))
                        ->setWork($work)
                        ->setUser($user);
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
}
