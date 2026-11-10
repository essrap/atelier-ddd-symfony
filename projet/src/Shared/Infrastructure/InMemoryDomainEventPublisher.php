<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Domain\DomainEvent;
use App\Shared\Domain\DomainEventPublisher;

/**
 * Keeps the published events in a list, in the order they were published. Nobody listens yet.
 */
final class InMemoryDomainEventPublisher implements DomainEventPublisher
{
    /** @var list<DomainEvent> */
    private array $published = [];

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $this->published[] = $event;
        }
    }

    /**
     * @return list<DomainEvent>
     */
    public function published(): array
    {
        return $this->published;
    }
}
