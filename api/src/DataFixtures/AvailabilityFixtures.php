<?php

namespace App\DataFixtures;

use App\Entity\Availability;
use App\Entity\Company;
use DateInterval;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use function Symfony\Component\Clock\now;

class AvailabilityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $companies = $manager->getRepository(Company::class)->findAll();
        foreach ($companies as $company) {
            for ($i=1; $i<=7;$i++) {
                $availibility = (new Availability())
                    ->setCompany($company)
                    ->setDay($i)
                    ->setCompanyStartTime('08:30')
                    ->setCompanyEndTime('19:00')
                    ->setCreatedAt(now());

                $manager->persist($availibility);
            }

            foreach ($company->getCompanyActiveTroubleMakers() as $troubleMaker) {
                for ($i=0; $i<3; $i++) {
                    $startDate = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-5 days', '+15 days'));
                    $startDate->setTime(random_int(0, 11), random_int(0, 59));
                    $endDate = $startDate->setTime(random_int(12, 23), random_int(0, 59));

                    $availibility = (new Availability())
                        ->setTroubleMaker($troubleMaker)
                        ->setStartTime($startDate)
                        ->setEndTime($endDate)
                        ->setCreatedAt(now());

                    $manager->persist($availibility);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class,
        ];
    }
}
