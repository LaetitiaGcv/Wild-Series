<?php


namespace App\DataFixtures;


use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('en_UK');

        for ($i = 0; $i < 100; $i++) {
            $episode = new Episode();
            $episode->setSeason($this->getReference('season_' . rand(0,10)));
            $episode->setTitle($faker->words(rand(1, 3), true));
            $episode->setNumber(rand(1, 10));
            $episode->setSynopsis($faker->text(200));
            $manager->persist($episode);

        }
        $manager->flush();
    }
}