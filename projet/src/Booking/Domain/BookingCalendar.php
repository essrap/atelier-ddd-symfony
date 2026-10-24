<?php

declare(strict_types=1);

namespace App\Booking\Domain;

use App\Shared\Domain\ListingId;

/**
 * The bookings of one listing. Aggregate root: the only way in, so the only place
 * that can guarantee that two bookings of the listing never overlap.
 */
final class BookingCalendar
{
    /** @var array<string, Booking> */
    private array $bookings = [];

    private function __construct(
        public readonly ListingId $listingId,
    ) {}

    public static function forListing(ListingId $listingId): self
    {
        return new self($listingId);
    }

    public function book(BookingId $id, Slot $slot): void
    {
        if (isset($this->bookings[$id->toString()])) {
            throw BookingRuleBroken::alreadyBooked($id);
        }
        foreach ($this->bookings as $booking) {
            if ($booking->blocks($slot)) {
                throw BookingRuleBroken::slotTaken();
            }
        }

        $this->bookings[$id->toString()] = Booking::confirmed($id, $slot);
    }

    public function cancel(BookingId $id): void
    {
        $booking = $this->bookings[$id->toString()] ?? throw BookingRuleBroken::unknownBooking($id);

        $booking->cancel();
    }
}
