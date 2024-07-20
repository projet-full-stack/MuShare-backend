<?php

namespace App\Controller;

use App\Repository\ReaderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ReaderController extends AbstractController
{
    #[Route('/reader', name: 'app_reader')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ReaderController.php',
        ]);
    }

    #[Route('/api/readers', name: 'reader.get', methods: ['GET'])]
    public function getReader(ReaderRepository $readerRepository, SerializerInterface $serializer): JsonResponse
    {
        $reader = $readerRepository->findAll();
        $jsonReader = $serializer->serialize($reader, 'json');
        return new JsonResponse($jsonReader, JsonResponse::HTTP_OK, [], true);
    }
}
