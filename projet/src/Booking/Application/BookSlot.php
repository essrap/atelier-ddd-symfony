<?php

declare(strict_types=1);

namespace App\Booking\Application;

use App\Booking\Domain\BookingCalendars;
use App\Booking\Domain\BookingId;
use App\Booking\Domain\Slot;
use App\Shared\Domain\DomainEventPublisher;
use App\Shared\Domain\ListingId;

/**
 * Use case: a renter books a slot of a listing. No business rule here, only the steps.
 */
final readonly class BookSlot
{
    public function __construct(
        private BookingCalendars $calendars,
        private DomainEventPublisher $publisher,
    ) {}

    public function __invoke(
        ListingId $listingId,
        BookingId $bookingId,
        Slot $slot,
        \DateTimeImmutable $now,
    ): void {
        $calendar = $this->calendars->get($listingId);
        $calendar->book($bookingId, $slot, $now);
        $this->calendars->save($calendar);
        $this->publisher->publish(...$calendar->releaseEvents());
    }
}
