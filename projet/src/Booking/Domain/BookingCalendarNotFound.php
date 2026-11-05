<?php

declare(strict_types=1);

namespace App\Booking\Domain;

use App\Shared\Domain\ListingId;

/**
 * No booking calendar was saved for this listing.
 */
final class BookingCalendarNotFound extends \DomainException
{
    public static function forListing(ListingId $listingId): self
    {
        return new self(\sprintf('No booking calendar for listing %s.', $listingId->toString()));
    }
}
