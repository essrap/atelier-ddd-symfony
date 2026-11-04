<?php

declare(strict_types=1);

namespace App\Shared\Domain;

/**
 * Something that happened in the domain, named in the past tense. Immutable: a fact cannot change.
 */
interface DomainEvent
{
    public \DateTimeImmutable $occurredOn { get; }
}
