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

        for ($i = 1; $i <= 200; $i++) {
            $comment = new Comment();
            $comment->setTitle($faker->sentence($nbWords = 3, $variableNbWords = true))
                    ->setNote(rand(0,5))
                    ->setDescription($faker->paragraph(1, true))
                    ->setPublishedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-2years","today")))
                    ->setIsPublic($faker->boolean(90))
                    ->setWork($works[array_rand($works)])
                    ->setUser($users[array_rand($users)]);
                    $manager->persist($comment);
        }
        $manager->flush();
    }
}
