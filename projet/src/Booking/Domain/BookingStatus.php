<?php

declare(strict_types=1);

namespace App\Booking\Domain;

/**
 * A booking is a renter's commitment on a slot of a listing.
 */
enum BookingStatus: string
{
    case Requested = 'requested';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
}
