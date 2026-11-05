<?php

declare(strict_types=1);

namespace App\Listing\Domain;

use App\Shared\Domain\ListingId;

/**
 * Where the domain finds and saves its listings, without knowing how they are stored.
 */
interface Listings
{
    /**
     * @throws ListingNotFound
     */
    public function get(ListingId $id): Listing;

    public function save(Listing $listing): void;
}
