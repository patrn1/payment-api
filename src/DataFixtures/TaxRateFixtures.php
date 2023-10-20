<?php

namespace App\DataFixtures;

use App\Entity\TaxRate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaxRateFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $taxRateFixtureList = [
            ['country_code' => 'DE', 'value' => 19, 'pattern' => '[0-9]{9}'],
            ['country_code' => 'IT', 'value' => 22, 'pattern' => '[0-9]{11}'],
            ['country_code' => 'FR', 'value' => 20, 'pattern' => '[A-Z]{2}[0-9]{9}'],
            ['country_code' => 'GR', 'value' => 24, 'pattern' => '[0-9]{9}'],
        ];

        foreach ($taxRateFixtureList as $taxRateData) {
            $taxRate = new TaxRate();
            $taxRate->setCountryCode($taxRateData['country_code']);
            $taxRate->setValue($taxRateData['value']);
            $taxRate->setPattern($taxRateData['pattern']);

            $manager->persist($taxRate);
        }

        $manager->flush();
    }
}
