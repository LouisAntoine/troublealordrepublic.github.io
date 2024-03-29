<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ServiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $companies = $manager->getRepository(Company::class)->findAll();

        foreach ($companies as $company) {
            $randomValue = random_int(1, 4);

            for ($i = 0; $i < $randomValue; $i++) {
                $service = new Service();
                $service->setName($faker->word)
                    ->setCompany($company)
                    ->setDescription($faker->text)
                    ->setDuration($faker->numberBetween(300, 7200))
                    ->setPrice($faker->randomFloat(2, 0, 1000))
                    ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 months', '-1 months')))
                    ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 months', '-1 months')));

                $manager->persist($service);
            }

        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            MediaFixtures::class
        ];
    }
}
