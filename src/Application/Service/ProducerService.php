<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Model\Producer;
use App\Domain\Repository\ProducerRepositoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProducerService
{
    public function __construct(
        private ProducerRepositoryInterface $producerRepository,
        private ValidatorInterface $validator
    ) {
    }

    /**
     * @return Producer[]
     */
    public function getAllProducers(): array
    {
        return $this->producerRepository->findAll();
    }

    /**
     * @return array{data: Producer[], total: int, page: int, limit: int, pages: int}
     */
    public function getAllProducersPaginated(int $page = 1, int $limit = 10): array
    {
        $allProducers = $this->producerRepository->findAll();
        $total = count($allProducers);
        
        $offset = ($page - 1) * $limit;
        $data = array_slice($allProducers, $offset, $limit);
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => (int) ceil($total / $limit)
        ];
    }

    /**
     * @param string $name
     * @return Producer
     * @throws \InvalidArgumentException
     */
    public function createProducer(string $name): Producer
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('Producer name cannot be empty');
        }

        $producer = new Producer(null, $name);
        
        $errors = $this->validator->validate($producer);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException('Invalid producer data: ' . implode(', ', array_map(fn($error) => $error->getMessage(), iterator_to_array($errors))));
        }

        $this->producerRepository->save($producer);
        
        return $producer;
    }
}
