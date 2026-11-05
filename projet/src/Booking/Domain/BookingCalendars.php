<?php

declare(strict_types=1);

namespace App\Booking\Domain;

use App\Shared\Domain\ListingId;

/**
 * Where the domain finds and saves the booking calendar of a listing, without knowing how it is stored.
 * One repository per aggregate: a booking is never found or saved on its own.
 */
interface BookingCalendars
{
    /**
     * @throws BookingCalendarNotFound
     */
    public function get(ListingId $listingId): BookingCalendar;

    public function save(BookingCalendar $calendar): void;
}
