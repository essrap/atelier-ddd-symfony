<?php

declare(strict_types=1);

namespace App\Booking\Infrastructure;

use App\Booking\Domain\BookingCalendar;
use App\Booking\Domain\BookingCalendarNotFound;
use App\Booking\Domain\BookingCalendars;
use App\Shared\Domain\ListingId;

/**
 * Keeps booking calendars in memory, like a database would: only what is saved, never the events.
 */
final class InMemoryBookingCalendars implements BookingCalendars
{
    /** @var array<string, BookingCalendar> */
    private array $calendars = [];

    public function get(ListingId $listingId): BookingCalendar
    {
        $calendar = $this->calendars[$listingId->toString()] ?? throw BookingCalendarNotFound::forListing($listingId);

        return $this->copy($calendar);
    }

    public function save(BookingCalendar $calendar): void
    {
        $copy = $this->copy($calendar);
        $copy->releaseEvents();

        $this->calendars[$calendar->listingId->toString()] = $copy;
    }

    private function copy(BookingCalendar $calendar): BookingCalendar
    {
        $copy = unserialize(serialize($calendar));
        \assert($copy instanceof BookingCalendar);

        return $copy;
    }
}
