<?php

namespace App\Controller;

use App\Entity\DownloadedFile;
use App\Entity\Song;
use App\Repository\SongRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use DateTime;

class SongController extends AbstractController
{
    #[Route('/', name: 'app_song')]
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
        $jsonSongs = $serializer->serialize($songs, 'json', ['groups' => 'song']);
        return new JsonResponse($jsonSongs, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/songs/last', name: 'song.getLastTen', methods: ['GET'])]
    public function getLastTenSongs(SongRepository $songRepository, SerializerInterface $serializer): JsonResponse
    {
        $songs = $songRepository->findLastTenSong();
        $jsonSongs = $serializer->serialize($songs, 'json', ['groups' => 'song']);
        return new JsonResponse($jsonSongs, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/songs/{songId}', name: 'song.get', methods: ['GET'])]
    public function getOneSong($songId, SongRepository $songRepository,UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer): JsonResponse
    {
        $song = $songRepository->find($songId);
        $downloadedFile = $song->getDownloadedFile();
        $location = $downloadedFile->getPublicPath() . '/' . $downloadedFile->getRealPath();
        $location = $urlGenerator->generate("app_song", [], UrlGeneratorInterface::ABSOLUTE_URL);
        $location = $location . str_replace("/public/", "", $downloadedFile->getPublicPath() . '/' . $downloadedFile->getRealPath());
        $jsonSong = $serializer->serialize($song, 'json', ['groups' => 'song']);
        return new JsonResponse($jsonSong, JsonResponse::HTTP_OK, ["Location" => $location], true);
    }

    #[Route('/api/songs', name: 'song.create', methods: ['POST'])]
    public function createSong(Request $req, EntityManagerInterface $entityManager,UrlGeneratorInterface $urlGenerator, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $file = new DownloadedFile();
        $song = new Song();

        $song->setCreatedAt(new DateTime());
        $song->setUpdatedAt(new DateTime());
        $song->setStatus("on");
        $song->setTitle($req->request->get('title'));
        $song->setAuthor($req->request->get('author'));
        $owner = $userRepository->find($req->request->get('owner'));
        $song->setOwner($owner);
        $song->setDownloadedFile($file);
        
        
        $files = $req->files->get('file');
        $file->setFile($files);
        
        $file->setMimeType($files->getClientMimeType());
        $file->setRealName($files->getClientOriginalName());
        $file->setPublicPath('files/songs');
        $file->setUpdatedAt(new DateTime());
        $file->setCreatedAt(new DateTime());
        $file->setStatus("on");
        $file->setFileSize(0);
        $file->setSong($song);
        $file->setRealPath($file->getPublicPath() . '/' . $file->getRealName() . '.' . $file->getFile()->guessExtension());

        $entityManager->persist($file);
        $entityManager->persist($song);
        
        $entityManager->flush();

        $location = $file->getPublicPath() . '/' . $file->getRealPath();
        $location = $urlGenerator->generate("app_song", [], UrlGeneratorInterface::ABSOLUTE_URL);
        $location = $location . str_replace("/public/", "", $file->getPublicPath() . '/' . $file->getRealPath());

        $jsonSong = $serializer->serialize($song, 'json', ['groups' => 'song']);   
        return new JsonResponse($jsonSong, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
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
