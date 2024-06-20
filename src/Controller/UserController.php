<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Playlist;
use App\Entity\Song;
use App\Entity\User;
use App\Repository\LikeRepository;
use App\Repository\PlaylistRepository;
use App\Repository\SongRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/api/users', name: 'user.getAll', methods: ['GET'])]
    public function getAllUsers(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $users = $entityManager->getRepository(User::class)->findAllActive("on");
        // dd($users);
        $jsonUsers = $serializer->serialize($users, 'json', ['groups' => 'user']);
        return new JsonResponse($jsonUsers, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/users', name: 'user.create', methods: ['POST'])]
    public function createUser(Request $req, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $user = $serializer->deserialize($req->getContent(), User::class, 'json');
        $user->setStatus("on");
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $entityManager->persist($user);
        $entityManager->flush();
        $jsonUser = $serializer->serialize($user, 'json');
        return new JsonResponse($jsonUser, JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('/api/users/{userId}/likes', name: 'user.getLikes', methods: ['GET'])]
    public function getLikes($userId, LikeRepository $likeRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        //$user = $entityManager->getRepository(User::class)->find($userId);
        $likes = $likeRepository->findAllActive($userId, "on");
        $jsonLikes = $serializer->serialize($likes, 'json', ['groups' => 'like']);
        return new JsonResponse($jsonLikes, JsonResponse::HTTP_OK, [], true);
    }
    
    #[Route('api/users/{userId}/playlists', name: 'user.getPlaylists', methods: ['GET'])]
    public function getPlaylists($userId, PlaylistRepository $playlistRepository, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        //$user = $entityManager->getRepository(User::class)->find($userId);
        $playlists = $playlistRepository->findAllActive($userId, "on");
        $jsonPlaylists = $serializer->serialize($playlists, 'json', ['groups' => 'playlist']);
        return new JsonResponse($jsonPlaylists, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('api/users/{userId}/songs', name: 'user.getSongs', methods: ['GET'])]
    public function getSongs($userId, SongRepository $songRepository,EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        //$user = $entityManager->getRepository(User::class)->find($userId);
        $songs = $songRepository->findAllActive($userId, "on");
        $jsonSongs = $serializer->serialize($songs, 'json', ['groups' => 'song']);
        return new JsonResponse($jsonSongs, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('api/users/{userId}/songs/{songId}/likes', name: 'user.createLike', methods: ['POST'])]
    public function createLike($userId, $songId, Request $req, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($userId);
        $song = $entityManager->getRepository(Song::class)->find($songId);
        $like = $serializer->deserialize($req->getContent(), Like::class, 'json');
        $like->setUser($user);
        $like->setSong($song);
        $like->setStatus("on");
        $like->setCreatedAt(new \DateTime());
        $like->setUpdatedAt(new \DateTime());
        $entityManager->persist($like);
        $entityManager->flush();
        $jsonLike = $serializer->serialize($like, 'json', ['groups' => 'like']);
        return new JsonResponse($jsonLike, JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('api/users/{userId}/likes/{likeId}', name: 'user.deleteLike', methods: ['DELETE'])]
    public function deleteLike($userId, $likeId, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($userId);
        $like = $entityManager->getRepository(Like::class)->find($likeId);
        $user->removeLike($like);
        $like->setStatus("off");
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('api/users/{userId}/songs', name: 'user.createSong', methods: ['POST'])]
    public function createSong($userId, Request $req, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($userId);
        $song = $serializer->deserialize($req->getContent(), Song::class, 'json');
        $song->setUser($user);
        $song->setStatus("on");
        $song->setCreatedAt(new \DateTime());
        $song->setUpdatedAt(new \DateTime());
        $entityManager->persist($song);
        $entityManager->flush();
        $jsonSong = $serializer->serialize($song, 'json', ['groups' => 'song']);
        return new JsonResponse($jsonSong, JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('api/users/{userId}/songs/{songId}', name: 'user.deleteSong', methods: ['DELETE'])]
    public function deleteSong($userId, $songId, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($userId);
        $song = $entityManager->getRepository(Song::class)->find($songId);
        $user->removeSong($song);
        $song->setStatus("off");
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('api/users/{userId}/playlists', name: 'user.createPlaylist', methods: ['POST'])]
    public function createPlaylist($userId, Request $req, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($userId);
        $playlist = $serializer->deserialize($req->getContent(), Playlist::class, 'json');
        $playlist->setOwner($user);
        $playlist->setStatus("on");
        $playlist->setCreatedAt(new \DateTime());
        $playlist->setUpdatedAt(new \DateTime());
        $entityManager->persist($playlist);
        $entityManager->flush();
        $jsonPlaylist = $serializer->serialize($playlist, 'json', ['groups' => 'playlist']);
        return new JsonResponse($jsonPlaylist, JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('api/users/{userId}/playlists/{playlistId}', name: 'user.deletePlaylist', methods: ['DELETE'])]
    public function deletePlaylist($userId, $playlistId, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($userId);
        $playlist = $entityManager->getRepository(Playlist::class)->find($playlistId);
        $user->removePlaylist($playlist);
        $playlist->setStatus("off");
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
