<?php

namespace App\DataFixtures;

use App\Entity\Follow;
use App\Entity\Like;
use App\Entity\Playlist;
use App\Entity\Song;
use App\Entity\User;
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
        //Users
        $users = [];
        for ($i=0; $i < 10; $i++) {
            $user = new User();
            $created = $this->faker->dateTime();
            $updated = $this->faker->dateTimeBetween($created, 'now');
            $user->setUsername($this->faker->userName())->setEmail($this->faker->email())->setPassword($this->faker->password())->setCreatedAt($created)->setUpdatedAt($updated)->setStatus("on");
            array_push($users, $user);
            $manager->persist($user);
        }

        //Songs
        $songs = [];
        for ($i=0; $i < 100; $i++) { 
            $song = new Song();
            $created = $this->faker->dateTime();
            $updated = $this->faker->dateTimeBetween($created, 'now');
            $song->setUser($users[array_rand($users)]);
            $song->setTitle($this->faker->word())->setStatus("on")->setCreatedAt($created)->setUpdatedAt($updated)->setAuthor($this->faker->name())->setPath($this->faker->imageUrl());
            array_push($songs, $song);
            $manager->persist($song);
        }

        //Playlists
        $playlists = [];
        for ($i=0; $i < 100; $i++) { 
            $playlist = new Playlist();
            $created = $this->faker->dateTime();
            $updated = $this->faker->dateTimeBetween($created, 'now');
            shuffle($songs);
            $fiveRandomElements = array_slice($songs, 0, 5);
            $playlist->setOwner($users[array_rand($users)]);
            $playlist->setTitle($this->faker->word())->setStatus("on")->setCreatedAt($created)->setUpdatedAt($updated)->addSong($fiveRandomElements[0])->addSong($fiveRandomElements[1])->addSong($fiveRandomElements[2])->addSong($fiveRandomElements[3])->addSong($fiveRandomElements[4]);
            array_push($playlists, $playlist);
            $manager->persist($playlist);
        }
        
        //Likes
        for ($i=0; $i < 100; $i++) { 
            $like = new Like();
            $created = $this->faker->dateTime();
            $updated = $this->faker->dateTimeBetween($created, 'now');
            $like->setUser($users[array_rand($users)])->setSong($songs[array_rand($songs)]);
            $like->setStatus("on")->setCreatedAt($created)->setUpdatedAt($updated);
            $manager->persist($like);
        }
        //Follows
        for ($i=0; $i<50; $i++) {
            $user = $users[array_rand($users)];
            $follow = new Follow();
            $follow->setUser($user);
            $follow->setPlaylist($playlists[array_rand($playlists)]);
            $created = $this->faker->dateTime();
            $updated = $this->faker->dateTimeBetween($created, 'now');
            $follow->setCreatedAt($created)->setUpdatedAt($updated)->setStatus("on");
            $manager->persist($follow);
        }

        $manager->flush();
    }
}
