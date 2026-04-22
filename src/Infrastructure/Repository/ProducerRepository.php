<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\Producer;
use App\Domain\Repository\ProducerRepositoryInterface;
use App\Infrastructure\ExternalApi\ExternalApi\ExternalApiClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class ProducerRepository implements ProducerRepositoryInterface
{
    private const ENDPOINT = '/producers';

    public function __construct(
        private readonly ExternalApiClient $apiClient,
        private readonly SerializerInterface $serializer
    ) {
    }

    public function findAll(): array
    {
        $response = $this->apiClient->request(Request::METHOD_GET, self::ENDPOINT);
        $data = $response->toArray();

        // Zakładam, że odpowiedź jest tablicą producentów
        return $this->serializer->deserialize(
            json_encode($data),
            Producer::class . '[]',
            'json'
        );
    }

    public function save(Producer $producer): Producer
    {
        $payload = $this->serializer->serialize($producer, 'json');
        
        $response = $this->apiClient->request(Request::METHOD_POST, self::ENDPOINT, [
            'body' => $payload,
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $data = $response->toArray();

        return $this->serializer->deserialize(
            json_encode($data),
            Producer::class,
            'json'
        );
    }
}
