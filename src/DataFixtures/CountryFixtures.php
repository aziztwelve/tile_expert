<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Modules\Common\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    public const COUNTRY_DE = 'country_de';
    public const COUNTRY_PL = 'country_pl';

    public function load(ObjectManager $manager): void
    {
        $countries = [
            ['DE', 'Germany'],
            ['PL', 'Poland'],
            ['FR', 'France'],
        ];

        foreach ($countries as [$code, $name]) {
            $country = new Country();
            $country->setCode($code);
            $country->setName($name);

            $manager->persist($country);

            if ($code === 'DE') {
                $this->addReference(self::COUNTRY_DE, $country);
            }

            if ($code === 'PL') {
                $this->addReference(self::COUNTRY_PL, $country);
            }
        }

        $manager->flush();
    }
}
