<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Interface\TimestampableInterface;
use DateTimeImmutable;

abstract class BaseEntity implements TimestampableInterface
{
    public function __construct(
        protected ?int $id = null,
        protected ?DateTimeImmutable $createdAt = null,
        protected ?DateTimeImmutable $updatedAt = null,
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function __clone(): void
    {
        // Kopiujemy obiekty DateTime przy klonowaniu
        if ($this->createdAt !== null) {
            $this->createdAt = clone $this->createdAt;
        }
        if ($this->updatedAt !== null) {
            $this->updatedAt = clone $this->updatedAt;
        }
    }

    public function withCreatedAt(): static
    {
        if ($this->createdAt !== null) {
            return $this;
        }
        
        $instance = clone $this;
        $instance->createdAt = new DateTimeImmutable();
        $instance->updatedAt = null;
        return $instance;
    }

    public function withUpdatedAt(): static
    {
        $instance = clone $this;
        $instance->updatedAt = new DateTimeImmutable();
        $instance->createdAt = null;
        return $instance;
    }
}
