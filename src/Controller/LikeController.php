<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Song;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class LikeController extends AbstractController
{
    #[Route('/like', name: 'app_like')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/LikeController.php',
        ]);
    }

    #[Route('api/likes/{songId}', name: 'like.createLike', methods: ['POST'])]
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
}
