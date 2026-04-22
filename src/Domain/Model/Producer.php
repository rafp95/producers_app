<?php

declare(strict_types=1);

namespace App\Domain\Model;

use DateTimeImmutable;

class Producer extends BaseEntity
{
    public function __construct(
        ?int $id = null,
        private readonly string $name = '',
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
    ) {
        parent::__construct($id, $createdAt, $updatedAt);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
