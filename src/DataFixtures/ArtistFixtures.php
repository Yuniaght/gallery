<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArtistFixtures extends Fixture
{
    public function __construct(private readonly SluggerInterface $slugger)
    {

    }
    private array $gender = ["male", "female"];
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');
        for ($i = 1; $i <= 10; $i++) {
            $artistGender = $faker->randomElement($this->gender);
            $artist = new Artist();
            $artist->setFirstName($faker->firstName($artistGender))
                   ->setLastName($faker->lastName())
                   ->setPseudonym(substr($artist->getFirstName(), 0, 3) . substr($artist->getLastName(), 0, 3))
                   ->setDateOfBirth(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-200 years', '-50 years')));
            if(rand(0,1) === 1) {
                $artist->setDateOfDeath(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-200 years', '-50 years')));
            }
            $artist->setAddedAt(new \DateTimeImmutable())
                   ->setEditedAt(new \DateTimeImmutable())
                   ->setImage($artistGender .$i .".jpg")
                   ->setDescription($faker->paragraph(8, true))
                   ->setSlug($this->slugger->slug($artist->getFirstName() .$artist->getLastName()));
            $manager->persist($artist);
        }
        $manager->flush();
    }
}
