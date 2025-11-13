<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture
{
    public function __construct(private readonly SluggerInterface $slugger)
    {

    }
    private array $categories = [
        'Peinture',
        'Sculpture',
    ];
    public function load(ObjectManager $manager): void
    {
        foreach ($this->categories as $category) {
            $newCat = new Category();
            $newCat->setName($category)
                   ->setSlug($this->slugger->slug($category));
            $manager->persist($newCat);
        }
        $manager->flush();
    }
}
