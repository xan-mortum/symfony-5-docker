<?php

namespace App\DataFixtures;

use App\Entity\Category;
use ArrayObject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $categoryNames = [
            'Board',
            'Management',
            'Accounting',
            'Development',
        ];
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $categories[$name] = $category;

            $manager->persist($category);
            $manager->flush();
            $this->setReference($name, $category);
        }
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 1;
    }
}
