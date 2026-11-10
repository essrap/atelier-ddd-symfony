<?php

declare(strict_types=1);

namespace Example\Ep09Anemic\Before;

final class BookingService
{
    /** @var list<BookingCalendar> */
    public array $saved = [];
    /** @var list<string> */
    public array $sent = [];

    public function book(
        BookingCalendar $calendar,
        string $renter,
        \DateTimeImmutable $start,
        \DateTimeImmutable $end,
        int $price,
    ): void {
        foreach ($calendar->getBookings() as $booking) {
            if ($start < $booking['end'] && $booking['start'] < $end) {
                throw new \DomainException('This slot is already booked.');
            }
        }
        $commission = max((int) round($price * 0.1), 100);
        $bookings = $calendar->getBookings();
        $bookings[] = ['renter' => $renter, 'start' => $start, 'end' => $end, 'commission' => $commission];
        $calendar->setBookings($bookings);
        $this->saved[] = $calendar;
        $this->sent[] = \sprintf('%s booked your %s.', $renter, $calendar->listing);
    }
}
