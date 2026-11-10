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
| `Listings` | Dépôt des annonces | Repository of the listings: finds and saves a whole listing, without the domain knowing how it is stored | — |
| `Availability` | Disponibilité | Time slots during which the equipment can be rented | — |
| `Slot` | Créneau | Dated period, start included, end excluded: a slot ending at 2 pm and one starting at 2 pm do not overlap | — |
| `Booking` | Réservation | A renter's commitment on a slot of a listing | `Reservation` |
| `BookingCalendar` | Calendrier de réservations | The bookings of one listing; aggregate root that refuses any booking overlapping another one | — |
| `BookingCalendars` | Dépôt des calendriers | Repository of the booking calendars, one per listing; a booking is never found or saved on its own | — |
| `SlotBooked` | Créneau réservé | Event: a slot of a listing has been booked | — |
| `BookingCancelled` | Réservation annulée | Event: a booking has been cancelled, its slot is free again | — |
| `Pickup` / `Return` | Retrait / Restitution | Handing the equipment over at the start / back at the end, confirmed by a code | — |
| `Rental` | Location | Actual period between pickup and return | — |
| `Deposit` | Caution | Amount held until the equipment is returned without dispute | — |
| `Money` | Montant | Amount in minor units (cents) with its currency: 4.92 EUR is 492 | — |
| `Commission` | Commission | Share of the paid price kept by the platform; computed by `CommissionCalculator` | — |
| `CommissionCalculator` | Calcul de la commission | Domain service that computes the `Commission` due on a payment: 10 % of the paid amount, rounded to the nearest cent, at least 1.00 of the same currency; a payment of 1.00 or less is refused | — |
| `Dispute` | Litige | Claim opened by a party (damage, delay, non-compliant equipment) | — |
| `Review` | Avis | Rating and comment left after a rental | — |
| `DomainEvent` | Événement de domaine | Something that happened in the domain, named in the past tense, immutable; recorded by an aggregate only once its rules allowed the change | — |
| `DomainEventPublisher` | Point de publication | Where an application service publishes the events an aggregate released, once it is saved; nobody listens yet | — |
| Domain service | Service de domaine | Stateless operation, named after what it does in the business, for a rule that belongs to no object (`CommissionCalculator`) | — |
| Application service | Service applicatif | Coordinates a use case (here, one class per use case): finds the aggregate, makes it act, saves it, then publishes its events; holds no business rule (`BookSlot`, `OpenBookingCalendar`) | — |

The `Avoid (code)` column lists words that must not appear in folder, file or enum case names under `src/` (checked by `tests/Architecture/ContextNamingTest.php`).

Bounded contexts and subdomains: [contexts.md](contexts.md); how they relate: [context-map.md](context-map.md).
Contextes et sous-domaines : [contexts.md](contexts.md) ; leurs relations : [context-map.md](context-map.md).

