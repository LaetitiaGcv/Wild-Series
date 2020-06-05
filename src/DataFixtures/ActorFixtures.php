<?php


namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use  Faker;

class ActorFixtures extends Fixture implements DependentFixtureInterface

{
    const ACTOR = [
        'Andrew Lincoln' => ['program_0', 'program_5'],
        'Norman Reedus'  => ['program_0'],
        'Lauren Cohan'   => ['program_0'],
        'Danai Gurira'   => ['program_0'],
    ];

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $i = 0;
        foreach (self::ACTOR as $name => $programs) {
            $actor = new Actor();
            $actor->setName($name);
            foreach ($programs as $program) {
                $actor->addProgram($this->getReference($program));
            }
            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);
            $i++;
        }

        $faker  =  Faker\Factory::create('en_UK');
        for ($i = 5; $i < 51; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name);
            $actor->addProgram($this->getReference('program_' . rand(0,5)));
            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);

        }

        $manager->flush();
    }

}