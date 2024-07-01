<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class PlaylistController extends AbstractController
{
    #[Route('/playlist', name: 'app_playlist')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PlaylistController.php',
        ]);
    }

    #[Route('/api/playlist', name: 'playlist.getAll', methods: ['GET'])]
    public function getAllSongs(PlaylistRepository $playlistRepository, SerializerInterface $serializer): JsonResponse
    {
        $playlist = $playlistRepository->findAll();
        $jsonPlaylist = $serializer->serialize($playlist, 'json', ['groups' => 'playlist']);
        return new JsonResponse($jsonPlaylist, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/playlist/{playlistId}', name: 'playlist.get', methods: ['GET'])]
    public function getOneSong($playlistId, PlaylistRepository $playlistRepository, SerializerInterface $serializer): JsonResponse
    {
        $song = $playlistRepository->find($playlistId);
        $jsonPlaylist = $serializer->serialize($song, 'json', ['groups' => 'playlist']);
        return new JsonResponse($jsonPlaylist, JsonResponse::HTTP_OK, [], true);
    }

    // #[Route('/api/playlist', name: 'playlist.create', methods: ['POST'])]
    // public function createSong(Request $req, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    // {
    //     $playlist = $serializer->deserialize($req->getContent(), Playlist::class, 'json');
    //     $playlist = $playlist->setStatus("on");
    //     $playlist->setCreatedAt(new \DateTime());
    //     $playlist->setUpdatedAt(new \DateTime());
    //     $entityManager->persist($playlist);
    //     $entityManager->flush();
    //     $jsonPlaylist = $serializer->serialize($playlist, 'json');
    //     return new JsonResponse($jsonPlaylist, JsonResponse::HTTP_CREATED, [], true);
    // }

    #[Route('/api/playlist/{playlist}', name: 'playlist.update', methods: ['PUT'])]
    public function updateSong(Request $req, Playlist $playlist, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $updatedPlaylist = $serializer->deserialize($req->getContent(), Playlist::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $playlist]);
        if(isset($req->toArray()['delete']))
        {
            $entityManager->remove($playlist);
            $entityManager->flush();
            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        }
        $entityManager->persist($updatedPlaylist);
        $entityManager->flush();
        $jsonPlaylist = $serializer->serialize($updatedPlaylist, 'json');
        return new JsonResponse($jsonPlaylist, JsonResponse::HTTP_OK, [], true);
    }
    
    #[Route('api/playlist/{idPlaylist}/song/{idSong}', name: 'playlist.addSong', methods: ['POST'])]
    public function addSongToPlaylist($idPlaylist, $idSong, PlaylistRepository $playlistRepository, SongRepository $songRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $playlist = $playlistRepository->find($idPlaylist);
        $song = $songRepository->find($idSong);
        $playlist->addSong($song);
        $playlist->setUpdatedAt(new \DateTime());
        $entityManager->persist($playlist);
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('api/playlist/{idPlaylist}/song/{idSong}', name: 'playlist.removeSong', methods: ['DELETE'])]
    public function removeSongFromPlaylist($idPlaylist, $idSong, PlaylistRepository $playlistRepository, SongRepository $songRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $playlist = $playlistRepository->find($idPlaylist);
        $song = $songRepository->find($idSong);
        $playlist->removeSong($song);
        $playlist->setUpdatedAt(new \DateTime());
        $entityManager->persist($playlist);
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
