<?php

declare(strict_types=1);

namespace App\Shared\Domain;

/**
 * The currencies Tooloc accepts. No conversion between them.
 */
enum Currency: string
{
    case EUR = 'EUR';
    case CHF = 'CHF';
}
