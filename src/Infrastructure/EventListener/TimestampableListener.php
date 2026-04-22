<?php

declare(strict_types=1);

namespace App\Infrastructure\EventListener;

use App\Domain\Interface\TimestampableInterface;
use DateTimeImmutable;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;

class TimestampableListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        
        if ($entity instanceof TimestampableInterface) {
            if ($entity->getCreatedAt() === null) {
                $entity->setUpdatedAt(new DateTimeImmutable());
            }
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        
        if ($entity instanceof TimestampableInterface) {
            $entity->setUpdatedAt(new DateTimeImmutable());
        }
    }
}
