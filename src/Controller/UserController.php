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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    
    #[Route('/api/users/{userId}', name: 'user.getOne', methods: ['GET'])]
    public function getOneUsers($userId, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($userId);
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'user']);
        return new JsonResponse($jsonUser, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/register', name: 'user.create', methods: ['POST'])]
    public function createUser(Request $req, UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $user = $serializer->deserialize($req->getContent(), User::class, 'json');
        $user->setStatus("on");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword()));
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $entityManager->persist($user);
        $entityManager->flush();
        $jsonUser = $serializer->serialize($user, 'json');
        return new JsonResponse($jsonUser, JsonResponse::HTTP_CREATED, [], true);
    }

    #[Route('/api/users/{userId}', name: 'user.update', methods: ['PUT'])]
    public function updateUser($userId, Request $req, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($userId);
        $data = json_decode($req->getContent(), true);
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setUpdatedAt(new \DateTime());
        $entityManager->flush();
        $jsonUser = $serializer->serialize($user, 'json');
        return new JsonResponse($jsonUser, JsonResponse::HTTP_OK, [], true);
    }


}
