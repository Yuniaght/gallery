<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\Technic;
use App\Entity\Work;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class WorkFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            TechnicFixtures::class,
            ArtistFixtures::class,
        ];
    }

    public function __construct(private readonly SluggerInterface $slugger)
    {

    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $categories = $manager->getRepository(Category::class)->findAll();
        $technics = $manager->getRepository(Technic::class)->findAll();
        $artists = $manager->getRepository(Artist::class)->findAll();
        for ($i = 1; $i <= 30; $i++) {
            $work = new Work();
            $work->setTitle($faker->sentence($nbWords = 3, $variableNbWords = true))
                 ->setImage($i .".jpg")
                 ->setDescription($faker->paragraph(rand(2,5), true))
                 ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween("-150 years","today")))
                 ->setAddedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 years', 'now')))
                 ->setEditedAt($work->getAddedAt())
                 ->setCategory($categories[array_rand($categories)])
                 ->setTechnic($technics[array_rand($technics)])
                 ->setArtist($artists[array_rand($artists)])
                 ->setSlug($this->slugger->slug($work->getTitle()));
            $manager->persist($work);
        }
        $manager->flush();
    }
}
