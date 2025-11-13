<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Technic;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class TechnicFixtures extends Fixture
{
    public function __construct(private readonly SluggerInterface $slugger)
    {

    }
    private array $technics = [
        'Peinture à l\'huile',
        'Feutre',
        'Burain',
        'Métaux',
        'Bois',
        'Animatronique',
    ];
    public function load(ObjectManager $manager): void
    {
        foreach ($this->technics as $technic) {
            $newTech = new Technic();
            $newTech->setName($technic)
                ->setSlug($this->slugger->slug($technic));
            $manager->persist($newTech);
        }
        $manager->flush();
    }
}
