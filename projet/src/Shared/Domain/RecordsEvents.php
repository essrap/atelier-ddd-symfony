<?php

declare(strict_types=1);

namespace App\Shared\Domain;

/**
 * Keeps the events of an aggregate until the caller releases them, once the aggregate is saved.
 */
trait RecordsEvents
{
    /** @var list<DomainEvent> */
    private array $events = [];

    /**
     * @return list<DomainEvent>
     */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    private function record(DomainEvent $event): void
    {
        $this->events[] = $event;
    }
}
