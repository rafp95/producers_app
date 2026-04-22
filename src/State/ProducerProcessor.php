<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\ProducerResource;
use App\Domain\Model\Producer;
use App\Domain\Repository\ProducerRepositoryInterface;

class ProducerProcessor implements ProcessorInterface
{
    public function __construct(
        private ProducerRepositoryInterface $repository
    ) {
    }

    /**
     * @param ProducerResource $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ProducerResource
    {
        $producer = new Producer(null, $data->name);
        $savedProducer = $this->repository->save($producer);

        return new ProducerResource(
            $savedProducer->getId(),
            $savedProducer->getName(),
            $savedProducer->getCreatedAt()?->format('Y-m-d\TH:i:s'),
            $savedProducer->getUpdatedAt()?->format('Y-m-d\TH:i:s')
        );
    }
}
