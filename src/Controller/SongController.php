<?php

namespace App\Controller;

use App\Entity\Song;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class SongController extends AbstractController
{
    #[Route('/song', name: 'app_song')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SongController.php',
        ]);
    }

    #[Route('/api/songs', name: 'song.getAll', methods: ['GET'])]
    public function getAllSongs(SongRepository $songRepository, SerializerInterface $serializer): JsonResponse
    {
        $songs = $songRepository->findAll();
        $jsonSongs = $serializer->serialize($songs, 'json');
        return new JsonResponse($jsonSongs, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/songs/{songId}', name: 'song.get', methods: ['GET'])]
    public function getOneSong($songId, SongRepository $songRepository, SerializerInterface $serializer): JsonResponse
    {
        $song = $songRepository->find($songId);
        $jsonSong = $serializer->serialize($song, 'json');
        return new JsonResponse($jsonSong, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/songs', name: 'song.create', methods: ['POST'])]
    public function createSong(Request $req, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $song = $serializer->deserialize($req->getContent(), Song::class, 'json');
        $song = $song->setStatus("on");
        $song->setCreatedAt(new \DateTime());
        $song->setUpdatedAt(new \DateTime());
        $entityManager->persist($song);
        $entityManager->flush();
        $jsonSong = $serializer->serialize($song, 'json');
        return new JsonResponse($jsonSong, JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('/api/songs/{song}', name: 'song.update', methods: ['PUT'])]
    public function updateSong(Request $req, Song $song, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $updatedSong = $serializer->deserialize($req->getContent(), Song::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $song]);
        if(isset($req->toArray()['delete']))
        {
            $updatedSong->setStatus("off");
        }

        $updatedSong->setUpdatedAt(new \DateTime());
        $entityManager->persist($updatedSong);
        $entityManager->flush();

        $jsonSong = $serializer->serialize($song, 'json');
        return new JsonResponse($jsonSong, JsonResponse::HTTP_OK, [], true);
    }
}