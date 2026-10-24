<?php

declare(strict_types=1);

namespace App\Booking\Domain;

/**
 * A business rule of the booking calendar refused the change.
 */
final class BookingRuleBroken extends \DomainException
{
    public static function slotTaken(): self
    {
        return new self('This slot overlaps another booking of the listing.');
    }

    public static function alreadyBooked(BookingId $id): self
    {
        return new self(\sprintf('Booking %s already exists.', $id->toString()));
    }

    public static function unknownBooking(BookingId $id): self
    {
        return new self(\sprintf('Booking %s does not exist in this calendar.', $id->toString()));
    }

    public static function alreadyCancelled(): self
    {
        return new self('This booking is already cancelled.');
    }
}
