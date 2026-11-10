<?php

declare(strict_types=1);

namespace App\Booking\Application;

use App\Booking\Domain\BookingCalendar;
use App\Booking\Domain\BookingCalendarNotFound;
use App\Booking\Domain\BookingCalendars;
use App\Shared\Domain\ListingId;

/**
 * Use case: a listing can now be booked. Opening it again keeps its calendar, and its bookings, as they are.
 */
final readonly class OpenBookingCalendar
{
    public function __construct(
        private BookingCalendars $calendars,
    ) {}

    public function __invoke(ListingId $listingId): void
    {
        try {
            $this->calendars->get($listingId);
        } catch (BookingCalendarNotFound) {
            $this->calendars->save(BookingCalendar::forListing($listingId));
        }
    }
}
