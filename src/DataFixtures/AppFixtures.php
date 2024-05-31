<?php

namespace App\DataFixtures;

use App\Entity\Song;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\en_US\Person;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct() {
        $this->faker = Factory::create("fr_FR");
    }
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 100; $i++) { 
            // $song = new Song();
            // $created = $this->faker->dateTime();
            // $updated = $this->faker->dateTimeBetween($created, 'now');
            // $song->setName($this->faker->word())->setStatus("on")->setCreatedAt($created)->setUpdatedAt($updated)->setAuthor($this->faker->name())->setPath($this->faker->imageUrl());
            // $manager->persist($song);


        }


        $manager->flush();
    }
}
