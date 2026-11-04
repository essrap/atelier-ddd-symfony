# Glossary — Tooloc ubiquitous language

The code is written in English; the videos speak French. This glossary is the single source of truth for both.
Le code est en anglais, les vidéos en français : ce glossaire fait foi dans les deux langues.

| Term (EN, code) | Terme (FR, voix) | Definition | Avoid (code) |
|---|---|---|---|
| `Owner` | Loueur | Person (individual or professional) who offers equipment for rent | — |
| `Renter` | Locataire | Person who books and uses the equipment | `Customer` |
| `Equipment` | Matériel | Item offered for rent, with a category, a description and a declared value | `Item` |
| `Listing` | Annonce | Equipment published with a price, availabilities and a deposit amount; born as a draft, then published, then withdrawn for good | `Announcement`, `Ad` |
| `ListingId` | Identifiant d'annonce | Identity of a listing, a lowercase UUID; other contexts refer to a listing by it | — |
| `ListingPublished` | Annonce publiée | Event: a listing has been published | — |
| `Availability` | Disponibilité | Time slots during which the equipment can be rented | — |
| `Slot` | Créneau | Dated period, start included, end excluded: a slot ending at 2 pm and one starting at 2 pm do not overlap | — |
| `Booking` | Réservation | A renter's commitment on a slot of a listing | `Reservation` |
| `BookingCalendar` | Calendrier de réservations | The bookings of one listing; aggregate root that refuses any booking overlapping another one | — |
| `SlotBooked` | Créneau réservé | Event: a slot of a listing has been booked | — |
| `BookingCancelled` | Réservation annulée | Event: a booking has been cancelled, its slot is free again | — |
| `Pickup` / `Return` | Retrait / Restitution | Handing the equipment over at the start / back at the end, confirmed by a code | — |
| `Rental` | Location | Actual period between pickup and return | — |
| `Deposit` | Caution | Amount held until the equipment is returned without dispute | — |
| `Money` | Montant | Amount in minor units (cents) with its currency: 4.92 EUR is 492 | — |
| `Commission` | Commission | Share of the paid price kept by the platform | — |
| `Dispute` | Litige | Claim opened by a party (damage, delay, non-compliant equipment) | — |
| `Review` | Avis | Rating and comment left after a rental | — |
| `DomainEvent` | Événement de domaine | Something that happened in the domain, named in the past tense, immutable; recorded by an aggregate only once its rules allowed the change | — |

The `Avoid (code)` column lists words that must not appear in folder, file or enum case names under `src/` (checked by `tests/Architecture/ContextNamingTest.php`).

Bounded contexts and subdomains: [contexts.md](contexts.md); how they relate: [context-map.md](context-map.md).
Contextes et sous-domaines : [contexts.md](contexts.md) ; leurs relations : [context-map.md](context-map.md).

