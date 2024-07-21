<?php

namespace App\DataFixtures;

use App\Entity\Follow;
use App\Entity\Like;
use App\Entity\Playlist;
use App\Entity\Reader;
use App\Entity\Song;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private Generator $faker;

    private UserPasswordHasherInterface $userPasswordHasher; 
    public function __construct(UserPasswordHasherInterface $userPasswordHasher) {
        $this->faker = Factory::create("fr_FR");
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        //Users
        $users = [];
        for ($i=0; $i < 10; $i++) {
            $user = new User();
            $created = $this->faker->dateTime();
            $updated = $this->faker->dateTimeBetween($created, 'now');
            $username = $this->faker->userName();
            $email = $this->faker->email();
            $password = $this->faker->password();

            $user->setUsername($email."|".$password)->setEmail($email)->setPassword($this->userPasswordHasher->hashPassword($user, $password))->setCreatedAt($created)->setUpdatedAt($updated)->setStatus("on");
            array_push($users, $user);
            $manager->persist($user);
        }

        //Songs
        $songs = [];
        for ($i=0; $i < 100; $i++) { 
            $song = new Song();
            $created = $this->faker->dateTime();
            $updated = $this->faker->dateTimeBetween($created, 'now');
            $song->setOwner($users[array_rand($users)]);
            $song->setTitle($this->faker->word())->setStatus("on")->setCreatedAt($created)->setUpdatedAt($updated)->setAuthor($this->faker->name());
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
            $playlist->setUser($users[array_rand($users)]);
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

        //Reader
        $reader = new Reader();
        $reader->setHtmlCode('
    <style>
        #reader {
            margin-top: 10rem;
            height: 10rem;
            border-radius: 0.5rem;
            background-color: #0C0F0A;
        }

        #buttons {
            position: absolute;
            margin-top: 1rem;
            left: 42%;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 20rem;
            height: 4rem;
        }

        #informations {
            position: absolute;
            left: 1rem;
            padding-top: 2.5rem;
            color: white;
            margin-top: auto;
            margin-bottom: auto;
        }

        #song {
            font-size: 1rem;
        }

        #autor {
            font-size: 0.9rem;
            color: #AAAAAA;
        }

        #musicReader {
            display: flex;
            flex-direction: row;
            height: 3rem;
            width: 30rem;
            left: 35%;
            margin-top: 6rem;
            justify-content: space-around;
            position: absolute;
            color: white;
        }

        svg {
            cursor: pointer;
        }

        input[type="range"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 20rem;
            background-color: transparent;

            &:focus {
                outline-color: #f8b195;
            }
        }

        input[type="range"]::-webkit-slider-runnable-track {
            -webkit-appearance: none;
            appearance: none;
            height: 3px;
            background: rgb(246, 114, 128);
            background: -webkit-linear-gradient(left,
                    rgba(246, 114, 128, 1) 0%,
                    rgba(192, 108, 132, 1) 50%,
                    rgba(53, 92, 125, 1) 100%);
            background: linear-gradient(to right,
                    rgba(246, 114, 128, 1) 0%,
                    rgba(192, 108, 132, 1) 50%,
                    rgba(53, 92, 125, 1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#f67280",
                    endColorstr="#355c7d",
                    GradientType=1);
        }

        input[type="range"]::-moz-range-track {
            -moz-appearance: none;
            appearance: none;
            height: 3px;
            background: rgb(246, 114, 128);
            background: -moz-linear-gradient(left,
                    rgba(246, 114, 128, 1) 0%,
                    rgba(192, 108, 132, 1) 50%,
                    rgba(53, 92, 125, 1) 100%);
            background: linear-gradient(to right,
                    rgba(246, 114, 128, 1) 0%,
                    rgba(192, 108, 132, 1) 50%,
                    rgba(53, 92, 125, 1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#f67280",
                    endColorstr="#355c7d",
                    GradientType=1);
        }

        input[type="range"]::-ms-track {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            height: 3px;
            background: rgb(246, 114, 128);
            background: -moz-linear-gradient(left,
                    rgba(246, 114, 128, 1) 0%,
                    rgba(192, 108, 132, 1) 50%,
                    rgba(53, 92, 125, 1) 100%);
            background: -webkit-linear-gradient(left,
                    rgba(246, 114, 128, 1) 0%,
                    rgba(192, 108, 132, 1) 50%,
                    rgba(53, 92, 125, 1) 100%);
            background: linear-gradient(to right,
                    rgba(246, 114, 128, 1) 0%,
                    rgba(192, 108, 132, 1) 50%,
                    rgba(53, 92, 125, 1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#f67280",
                    endColorstr="#355c7d",
                    GradientType=1);
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            border: 2px solid #f8b195;
            border-radius: 50%;
            height: 20px;
            width: 20px;
            position: relative;
            bottom: 8px;
            background: #222 url("http://codemenatalie.com/wp-content/uploads/2019/09/slider-thumb.png") center no-repeat;
            background-size: 50%;
            box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.4);
            cursor: grab;

            &:active {
                cursor: grabbing;
            }
        }


        input[type="range"]::-moz-range-thumb {
            -moz-appearance: none;
            appearance: none;
            border: 2px solid #f8b195;
            border-radius: 50%;
            height: 20px;
            width: 20px;
            position: relative;
            bottom: 8px;
            background: #222 url("http://codemenatalie.com/wp-content/uploads/2019/09/slider-thumb.png") center no-repeat;
            background-size: 50%;
            box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.4);
            cursor: grab;

            &:active {
                cursor: grabbing;
            }
        }

        input[type="range"]::-ms-thumb {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border: 2px solid #f8b195;
            border-radius: 50%;
            height: 20px;
            width: 20px;
            position: relative;
            bottom: 8px;
            background: #222 url("http://codemenatalie.com/wp-content/uploads/2019/09/slider-thumb.png") center no-repeat;
            background-size: 50%;
            box-shadow: 0px 3px 5px 0px rgba(0, 0, 0, 0.4);
            cursor: grab;

            &:active {
                cursor: grabbing;
            }
        }
    </style>
    <div id="reader">
        <div id="informations">
            <p>
                <span id="song">Here Comes The Sun - REMASTERED</span>
                <br>
                <span id="autor">The Beatles</span>
            </p>
        </div>
        <div id="buttons">
            <svg id="previous" style="cursor:pointer;" xmlns="http://www.w3.org/2000/svg" height="48px"
                viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
                <path d="M220-240v-480h60v480h-60Zm520 0L394-480l346-240v480Z" />
            </svg>
            <span id="play">
                <svg style="cursor:pointer;" xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960"
                    width="48px" fill="#FFFFFF">
                    <path
                        d="m383-310 267-170-267-170v340Zm97 230q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-156t86-127Q252-817 325-848.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 82-31.5 155T763-197.5q-54 54.5-127 86T480-80Z" />
                </svg>
            </span>
            <svg id="skip" style="cursor:pointer" xmlns="http://www.w3.org/2000/svg" height="48px"
                viewBox="0 -960 960 960" width="48px" fill="#FFFFFF">
                <path d="M680-240v-480h60v480h-60Zm-460 0v-480l346 240-346 240Z" />
            </svg>
            <svg id="refresh" style="cursor:pointer" xmlns="http://www.w3.org/2000/svg" height="40px"
                viewBox="0 -960 960 960" width="45px" fill="#FFFFFF">
                <path
                    d="M451-122q-123-10-207-101t-84-216q0-77 35.5-145T295-695l43 43q-56 33-87 90.5T220-439q0 100 66 173t165 84v60Zm60 0v-60q100-12 165-84.5T741-439q0-109-75.5-184.5T481-699h-20l60 60-43 43-133-133 133-133 43 43-60 60h20q134 0 227 93.5T801-439q0 125-83.5 216T511-122Z" />
            </svg>
        </div>
        <div id="musicReader">
            <span id="elapsed">0:00</span>
            <audio id="audio" onloadeddata="correctInfo()" hidden
                src="Elden Ring Main Theme (The Final Battle)  EPIC VERSION.mp3"></audio>
            <div class="wrapper">
                <input id="timeInput" type="range" min="0" value="0">
            </div>
            <span id="ostDuration"></span>
        </div>
    </div>
    <script type="text/javascript">
        function correctInfo() {
            const audio = document.getElementById(\'audio\');
            const playButton = document.getElementById(\'play\');
            const previousButton = document.getElementById(\'previous\');;
            const skipButton = document.getElementById(\'skip\');
            const refreshButton = document.getElementById(\'refresh\');
            const ostDuration = document.getElementById(\'ostDuration\');
            const track = document.getElementById(\'timeInput\');
            const elapsed = document.getElementById(\'elapsed\');

            let duration = audio.duration;
            ostDuration.textContent = buildDuration(duration);
            track.max = audio.duration;

            function buildDuration(duration) {
                let minutes = Math.floor(duration / 60);
                let reste = duration % 60;
                let seconds = Math.floor(reste);
                seconds = String(seconds).padStart(2, \'0\');
                return minutes + ":" + seconds;
            }

            audio.addEventListener(\'timeupdate\', () => {
                track.value = audio.currentTime;
                elapsed.textContent = buildDuration(audio.currentTime);
            });

            track.addEventListener(\'input\', () => {
                audio.currentTime = track.value;
                elapsed.textContent = buildDuration(track.value);
            });

            function changeAudioStatus() {
                if (audio.paused) {
                    audio.play();
                    playButton.innerHTML = \'<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF"><path d="M360-320h80v-320h-80v320Zm160 0h80v-320h-80v320ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/></svg>\';
                } else {
                    audio.pause();
                    playButton.innerHTML = \'<svg id="play" style="cursor:pointer" xmlns="http://www.w3.org/2000/svg" height="48px" viewBox = "0 -960 960 960" width = "48px" fill = "#FFFFFF" > <path d="m383-310 267-170-267-170v340Zm97 230q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-156t86-127Q252-817 325-848.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 82-31.5 155T763-197.5q-54 54.5-127 86T480-80Z" /> </svg > \';
                }
            }
            playButton.addEventListener(\'click\', () => {
                if (audio.paused) {
                    audio.play();
                    playButton.innerHTML = \'<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FFFFFF"><path d="M360-320h80v-320h-80v320Zm160 0h80v-320h-80v320ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z"/></svg>\';
                } else {
                    audio.pause();
                    playButton.innerHTML = \'<svg id="play" style="cursor:pointer" xmlns="http://www.w3.org/2000/svg" height="48px" viewBox = "0 -960 960 960" width = "48px" fill = "#FFFFFF" > <path d="m383-310 267-170-267-170v340Zm97 230q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-156t86-127Q252-817 325-848.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 82-31.5 155T763-197.5q-54 54.5-127 86T480-80Z" /> </svg > \';
                }
            });
        }
    </script>');
        $manager->persist($reader);

        $manager->flush();
    }
}
