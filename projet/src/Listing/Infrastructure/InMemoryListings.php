<?php

declare(strict_types=1);

namespace App\Listing\Infrastructure;

use App\Listing\Domain\Listing;
use App\Listing\Domain\ListingNotFound;
use App\Listing\Domain\Listings;
use App\Shared\Domain\ListingId;

/**
 * Keeps listings in memory, like a database would: only what is saved, never the events.
 */
final class InMemoryListings implements Listings
{
    /** @var array<string, Listing> */
    private array $listings = [];

    public function get(ListingId $id): Listing
    {
        $listing = $this->listings[$id->toString()] ?? throw ListingNotFound::withId($id);

        return $this->copy($listing);
    }

    public function save(Listing $listing): void
    {
        $copy = $this->copy($listing);
        $copy->releaseEvents();

        $this->listings[$listing->id->toString()] = $copy;
    }

    private function copy(Listing $listing): Listing
    {
        $copy = unserialize(serialize($listing));
        \assert($copy instanceof Listing);

        return $copy;
    }
}
