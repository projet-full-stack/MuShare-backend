<?php

namespace App\DataFixtures;

use App\Entity\Playlist;
use App\Entity\Song;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{

    private Generator $faker;

    public function __construct() {
        $this->faker = Factory::create("fr_FR");
    }
    public function load(ObjectManager $manager): void
    {
        //Songs
        $songs = [];
        for ($i=0; $i < 100; $i++) { 
            $song = new Song();
            $created = $this->faker->dateTime();
            $updated = $this->faker->dateTimeBetween($created, 'now');
            $song->setTitle($this->faker->word())->setStatus("on")->setCreatedAt($created)->setUpdatedAt($updated)->setAuthor($this->faker->name())->setPath($this->faker->imageUrl());
            array_push($songs, $song);
            $manager->persist($song);
        }

        //Playlists
        for ($i=0; $i < 100; $i++) { 
            $playlist = new Playlist();
            $created = $this->faker->dateTime();
            $updated = $this->faker->dateTimeBetween($created, 'now');
            shuffle($songs);
            $fiveRandomElements = array_slice($songs, 0, 5);
            $playlist->setTitle($this->faker->word())->setStatus("on")->setCreatedAt($created)->setUpdatedAt($updated)->addSong($fiveRandomElements[0])->addSong($fiveRandomElements[1])->addSong($fiveRandomElements[2])->addSong($fiveRandomElements[3])->addSong($fiveRandomElements[4]);
            $manager->persist($playlist);
        }
        
        //Likes
        // for ($i=0; $i < 100; $i++) { 
            // $like = new Like();
            // $created = $this->faker->dateTime();
            // $updated = $this->faker->dateTimeBetween($created, 'now');
            // $like->setStatus("on")->setCreatedAt($created)->setUpdatedAt($updated);
            // $manager->persist($like);
        // }

        $manager->flush();
    }
}
