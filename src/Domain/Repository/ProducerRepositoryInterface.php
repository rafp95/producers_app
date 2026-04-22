<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Producer;

interface ProducerRepositoryInterface
{
    /**
     * @return Producer[]
     */
    public function findAll(): array;

    public function save(Producer $producer): Producer;
}
