<?php
/**
 * Shorten fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Shorten;
use App\Entity\Guest;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Class ShortenFixtures.
 */
class ShortenFixtures extends Fixture
{
    /**
     * Faker.
     */
    protected Generator $faker;

    /**
     * Persistence object manager.
     */
    protected ObjectManager $manager;

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 10; ++$i) {
            // Create a new Guest entity
            $guestUser = new Guest();
            $guestUser->setGuestEmail($this->faker->email);
            $guestUser->setIdentifier($this->faker->ipv4);
            $guestUser->setCreationCount($this->faker->numberBetween(0, 2));
            $manager->persist($guestUser);

            // Create a new Shorten entity
            $shorten = new Shorten();
            $shorten->setShortenIn($this->faker->sentence);
            $shorten->setShortenOut($this->faker->randomNumber(4));
            $shorten->setClickCounter($this->faker->numberBetween(0, 100));
            $shorten->setGuest($guestUser);
            $shorten->setAddDate(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $manager->persist($shorten);
        }

        $manager->flush();
    }
}
