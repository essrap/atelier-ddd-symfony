<?php

declare(strict_types=1);

namespace App\Booking\Domain;

use App\Shared\Domain\DomainEvent;
use App\Shared\Domain\ListingId;

/**
 * A booking of a listing has been cancelled: its slot is free again.
 */
final readonly class BookingCancelled implements DomainEvent
{
    public function __construct(
        public ListingId $listingId,
        public BookingId $bookingId,
        public \DateTimeImmutable $occurredOn,
    ) {}
}
