<?php

declare(strict_types=1);

namespace App\Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class ProjectDocsTest extends TestCase
{
    public function testItReadsTheContextsOfTheContextsSection(): void
    {
        $markdown = <<<'MD'
            # Title

            | Context | Subdomain |
            |---|---|
            | `Ignored` | before the section |

            ## Contexts

            | Context | Subdomain |
            |---|---|
            | `Listing` | core |
            | `Booking` | core |

            ## One word, two models

            | `AlsoIgnored` | after the section |
            MD;

        self::assertSame(['Listing', 'Booking'], ProjectDocs::declaredContexts($markdown));
    }

    public function testNoContextsSectionMeansNoDeclaredContext(): void
    {
        self::assertSame([], ProjectDocs::declaredContexts("# Title\n\nNothing here.\n"));
    }

    public function testItReadsTheWordsToAvoidFromTheGlossary(): void
    {
        $markdown = <<<'MD'
            | Term | Terme | Definition | Avoid (code) |
            |---|---|---|---|
            | `Booking` | Réservation | A commitment | `Reservation` |
            | `Listing` | Annonce | A published item | `Ad`, `Announcement` |
            | `Slot` | Créneau | A period | — |
            MD;

        self::assertSame(
            ['Reservation' => 'Booking', 'Ad' => 'Listing', 'Announcement' => 'Listing'],
            ProjectDocs::forbiddenWords($markdown),
        );
    }

    public function testAGlossaryWithoutAvoidColumnForbidsNothing(): void
    {
        $markdown = "| Term | Definition |\n|---|---|\n| `Booking` | A commitment |\n";

        self::assertSame([], ProjectDocs::forbiddenWords($markdown));
    }

    public function testItSplitsNamesIntoLowercaseWords(): void
    {
        self::assertSame(['picked', 'up'], ProjectDocs::words('PickedUp'));
        self::assertSame(['picked', 'up'], ProjectDocs::words('picked_up'));
        self::assertSame(['reservation'], ProjectDocs::words('Reservation'));
    }
}
