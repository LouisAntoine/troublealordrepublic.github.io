<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\User;
use App\Enum\ReservationStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $customers = $manager->getRepository(User::class)->findByRole('ROLE_USER', false);
        $troubleMakers = $manager->getRepository(User::class)->findByRole('ROLE_TROUBLE_MAKER', false);
        $services = $manager->getRepository(Service::class)->findAll();
        $status = [
            ReservationStatusEnum::ACTIVE,
            ReservationStatusEnum::CANCELED,
            ReservationStatusEnum::FINISHED,
            ReservationStatusEnum::REFUNDED
        ];

        foreach ($customers as $customer) {

            foreach ($services as $service) {

                $serviceTroubleMakers = $service->getCompany()->getUsers();

                $randomValue = random_int(1, 2);

                for ($i = 0; $i < $randomValue; $i++) {
                    $reservation = new Reservation();
                    $reservation->setCustomer($customer)
                        ->setService($service)
                        ->setTroubleMaker($faker->randomElement($serviceTroubleMakers))
                        ->setAddress($faker->address)
                        ->setPrice($service->getPrice())
                        ->setDuration($service->getDuration())
                        ->setStatus($faker->randomElement($status))
                        ->setPaymentIntentId($faker->regexify('[A-Z]{2}[0-9]{3}'))
                        ->setDescription($service->getDescription())
                        ->setDate(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 months', '+ 5 days')))
                        ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 months', '-1 months')))
                        ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 months', '-1 months')));

                    $manager->persist($reservation);

                    $troubleMakerReservation = new Reservation();
                    $troubleMakerReservation->setCustomer($faker->randomElement($serviceTroubleMakers))
                        ->setService($service)
                        ->setTroubleMaker($faker->randomElement($serviceTroubleMakers))
                        ->setAddress($faker->address)
                        ->setPrice($service->getPrice())
                        ->setDuration($service->getDuration())
                        ->setStatus($faker->randomElement($status))
                        ->setPaymentIntentId($faker->regexify('[A-Z]{2}[0-9]{3}'))
                        ->setDescription($service->getDescription())
                        ->setDate(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 months', '+ 5 days')))
                        ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 months', '-1 months')))
                        ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 months', '-1 months')));

                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            MediaFixtures::class,
            ServiceFixtures::class,
        ];
    }
}
