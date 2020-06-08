<?php


namespace App\DataFixtures;


use App\Entity\Episode;
use App\Service\Slugify;
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

        for ($j = 0; $j<6; $j++) {

            $episodeCount = rand(3,12);

            for ($i = 0; $i < $episodeCount; $i++) {
                $episode = new Episode();
                $episode->setSeason($this->getReference('season_' . $j));
                $episode->setTitle($faker->words(rand(1, 3), true));
                $episode->setNumber($i);
                $episode->setSynopsis($faker->text(200));
                $slugify = new Slugify();
                $slug = $slugify->generate($episode->getTitle());
                $episode->setSlug($slug);
                $manager->persist($episode);

            }
        }
        $manager->flush();
    }
}