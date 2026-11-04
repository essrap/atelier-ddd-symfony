<?php

declare(strict_types=1);

namespace App\Booking\Domain;

use App\Shared\Domain\DomainEvent;
use App\Shared\Domain\ListingId;

/**
 * A slot of a listing has been booked.
 */
final readonly class SlotBooked implements DomainEvent
{
    public function __construct(
        public ListingId $listingId,
        public BookingId $bookingId,
        public Slot $slot,
        public \DateTimeImmutable $occurredOn,
    ) {}
}
