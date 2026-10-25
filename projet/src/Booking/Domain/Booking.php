<?php

declare(strict_types=1);

namespace App\Booking\Domain;

/**
 * A renter's commitment on a slot. By convention, only its BookingCalendar creates and changes it.
 */
final class Booking
{
    private function __construct(
        public readonly BookingId $id,
        public readonly Slot $slot,
        public private(set) BookingStatus $status,
    ) {}

    /**
     * @internal only BookingCalendar books a slot, after checking it is free
     */
    public static function confirmed(BookingId $id, Slot $slot): self
    {
        return new self($id, $slot, BookingStatus::Confirmed);
    }

    public function blocks(Slot $slot): bool
    {
        return BookingStatus::Cancelled !== $this->status && $this->slot->overlaps($slot);
    }

    public function cancel(): void
    {
        if (BookingStatus::Cancelled === $this->status) {
            throw BookingRuleBroken::alreadyCancelled();
        }

        $this->status = BookingStatus::Cancelled;
    }
}
