<?php

declare(strict_types=1);

namespace App\Domain\Interface;

use DateTimeImmutable;

interface TimestampableInterface
{
    public function getCreatedAt(): ?DateTimeImmutable;
    public function getUpdatedAt(): ?DateTimeImmutable;
    public function setUpdatedAt(DateTimeImmutable $updatedAt): void;
}
