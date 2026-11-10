<?php

declare(strict_types=1);

namespace Example\Ep09Anemic\Before;

/**
 * A booking taken by phone, typed in by hand.
 */
final class ManualBookingService
{
    public function add(
        BookingCalendar $calendar,
        string $renter,
        \DateTimeImmutable $start,
        \DateTimeImmutable $end,
        int $price,
    ): void {
        $bookings = $calendar->getBookings();
        $bookings[] = [
            'renter' => $renter,
            'start' => $start,
            'end' => $end,
            'commission' => intdiv($price, 10),
        ];
        $calendar->setBookings($bookings);
    }
}
