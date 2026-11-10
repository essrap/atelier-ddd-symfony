<?php

declare(strict_types=1);

namespace Example\Ep09Anemic\Before;

/**
 * @phpstan-type Booking array{
 *     renter: string,
 *     start: \DateTimeImmutable,
 *     end: \DateTimeImmutable,
 *     commission: int,
 * }
 */
final class BookingCalendar
{
    /** @var list<Booking> */
    private array $bookings = [];

    public function __construct(
        public readonly string $listing,
    ) {}

    /** @return list<Booking> */
    public function getBookings(): array
    {
        return $this->bookings;
    }

    /** @param list<Booking> $bookings */
    public function setBookings(array $bookings): void
    {
        $this->bookings = $bookings;
    }
}
