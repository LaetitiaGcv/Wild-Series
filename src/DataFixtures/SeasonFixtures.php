<?php


namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Service\Slugify;
use Faker;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [ProgramFixtures::class];

    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('en_US');

        for ($i = 0; $i < 6; $i++) {
            $season = new Season();
            $program = $this->getReference('program_' . $i);
            $season->setProgram($program);
            $season->setNumber(1);
            $season->setYear($program->getYear());
            $season->setDescription($faker->sentence);
            $slugify = new Slugify();
            $slug = $slugify->generate($season->getYear());
            $season ->setSlug($slug);
            $manager->persist($season);
            $this->addReference('season_' . $i, $season);
        }


        $manager->flush();
    }
}