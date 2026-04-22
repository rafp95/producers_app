<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Service\ProducerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[AsController]
readonly class ProducerController
{
    public function __construct(
        private ProducerService $producerService,
        private SerializerInterface $serializer
    ) {
    }

    #[Route('/get_all', name: 'api_get_all_producers', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $producers = $this->producerService->getAllProducers();
        
        $data = $this->serializer->serialize($producers, 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__']
        ]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/create_one', name: 'api_create_producer', methods: ['POST'])]
    public function createOne(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) {
            return new JsonResponse([
                'message' => 'Name field is required',
                'code' => Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $producer = $this->producerService->createProducer($data['name']);

            $data = $this->serializer->serialize($producer, 'json', [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__']
            ]);

            return new JsonResponse($data, Response::HTTP_CREATED, [], true);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'code' => Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
