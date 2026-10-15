# Subdomains and bounded contexts — Tooloc

The split is a choice made for this workshop, not the only valid answer. Each context owns a folder `src/<Context>/` and speaks one language: the glossary ([glossary.md](glossary.md)).

Le découpage est un choix assumé pour cet atelier, pas la seule réponse possible. Chaque contexte a son dossier `src/<Context>/` et parle une seule langue : le glossaire.

## Contexts

| Context | Contexte (FR) | Subdomain | Implementation style |
|---|---|---|---|
| `Listing` | Annonces | core | Rich model, tested in depth |
| `Booking` | Réservation | core | Rich model, tested in depth |
| `Rental` | Location | supporting | Simple, CRUD allowed |
| `Payment` | Paiement et caution | supporting | Simple, CRUD allowed |
| `Dispute` | Litiges | supporting | Simple, CRUD allowed |
| `Review` | Notation | generic | CRUD, nothing to differentiate |
| `Identity` | Identité | generic | Reuse Symfony Security |

`Shared/` holds technical code used by every context (HTTP, kernel). It is not a context.

Coded so far: `Listing`, `Booking`, `Rental`. The other four are declared here and built in later episodes.

How the contexts relate to each other: [context-map.md](context-map.md).

## One word, two models: "Booking"

| Context | What the word means | Code |
|---|---|---|
| `Booking` | A renter's commitment on a slot of a listing | `BookingStatus` |
| `Rental` | The state "booked", before the pickup | `RentalStatus::Booked` |

The same French word, « réservation », leads to two models with two lifecycles. Neither is wrong: each context owns its own meaning.
