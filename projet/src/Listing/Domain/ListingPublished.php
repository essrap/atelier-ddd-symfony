<?php

declare(strict_types=1);

namespace App\Listing\Domain;

use App\Shared\Domain\DomainEvent;
use App\Shared\Domain\ListingId;

/**
 * A listing has been published: renters can now find it.
 */
final readonly class ListingPublished implements DomainEvent
{
    public function __construct(
        public ListingId $listingId,
        public \DateTimeImmutable $occurredOn,
    ) {}
}
