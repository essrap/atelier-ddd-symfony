<?php

declare(strict_types=1);

namespace App\Listing\Domain;

/**
 * Lifecycle of a listing: from the owner's draft to the withdrawal from the catalogue.
 */
enum ListingStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Withdrawn = 'withdrawn';
}
