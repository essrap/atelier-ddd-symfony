<?php

declare(strict_types=1);

namespace App\Tests\Unit\Booking\Application;

use App\Booking\Domain\BookingCalendar;
use App\Booking\Domain\BookingCalendars;
use App\Shared\Domain\ListingId;

final class BookingCalendarsWithStorageDown implements BookingCalendars
{
    public function get(ListingId $listingId): BookingCalendar
    {
        return BookingCalendar::forListing($listingId);
    }

    public function save(BookingCalendar $calendar): void
    {
        throw new StorageIsDown();
    }
}
