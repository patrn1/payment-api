<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $productDataList = [
            ['id' => 1, 'name' => 'Iphone', 'price' => 100],
            ['id' => 2, 'name' => 'Наушники', 'price' => 20],
            ['id' => 3, 'name' => 'Чехол', 'price' => 10],
        ];

        foreach ($productDataList as $productData) {
            $product = new Product();
            $product->setId($productData['id']);
            $product->setName($productData['name']);
            $product->setPrice($productData['price']);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
