<?php

declare(strict_types=1);

namespace App\Shared\Domain;

/**
 * Where the application publishes the events an aggregate released, once it is saved.
 */
interface DomainEventPublisher
{
    public function publish(DomainEvent ...$events): void;
}
