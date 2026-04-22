<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\ProducerResource;
use App\Domain\Repository\ProducerRepositoryInterface;

class ProducerProvider implements ProviderInterface
{
    public function __construct(
        private ProducerRepositoryInterface $repository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $producers = $this->repository->findAll();
        
        // Handle pagination from API Platform context
        $filters = $context['filters'] ?? [];
        $page = (int) ($filters['page'] ?? 1);
        $limit = (int) ($filters['limit'] ?? 10);
        
        $offset = ($page - 1) * $limit;
        $pagedProducers = array_slice($producers, $offset, $limit);
        
        return array_map(
            fn($p) => new ProducerResource(
                $p->getId(),
                $p->getName(),
                $p->getCreatedAt()?->format('Y-m-d\TH:i:s'),
                $p->getUpdatedAt()?->format('Y-m-d\TH:i:s')
            ),
            $pagedProducers
        );
    }
}
