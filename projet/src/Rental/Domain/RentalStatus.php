<?php

declare(strict_types=1);

namespace App\Rental\Domain;

/**
 * A rental starts once a booking is confirmed. Here "booked" is only the state
 * before the pickup: the same word as in the Booking context, another model.
 */
enum RentalStatus: string
{
    case Booked = 'booked';
    case PickedUp = 'picked_up';
    case Returned = 'returned';
}
