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
     * Load.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < 10; ++$i) {
            $shorten = new Shorten();
            $shorten->setShortenIn($this->faker->sentence);
            $shorten->setShortenOut($this->faker->randomNumber(4));
            $shorten->setClickCounter($this->faker->numerify);
            $guestUser = new Guest();
            $guestUser->setGuestEmail('guest@example.com');
            $shorten->setGuest($guestUser);
            $shorten->setAddDate(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $manager->persist($shorten);
            $manager->persist($guestUser);
        }

        $manager->flush();
    }
}
