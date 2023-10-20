<?php

namespace App\DataFixtures;

use App\Entity\Coupon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CouponFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $couponDataList = [
            ['id' => 1, 'code' => 'D15', 'amount' => 20, 'type' => Coupon::DiscountTypes::FIXED_DISCOUNT],
            ['id' => 2, 'code' => '02I4JR', 'amount' => 15, 'type' => Coupon::DiscountTypes::PERCENT_DISCOUNT],
        ];

        foreach ($couponDataList as $productData) {
            $product = new Coupon();
            $product->setId($productData['id']);
            $product->setCode($productData['code']);
            $product->setAmount($productData['amount']);
            $product->setType($productData['type']);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
