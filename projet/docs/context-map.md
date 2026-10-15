# Context map — Tooloc

How the bounded contexts of [contexts.md](contexts.md) relate to each other. Like the split itself, this map is a choice made for this workshop.

Comment les contextes de [contexts.md](contexts.md) sont reliés. Comme le découpage, cette carte est un choix assumé pour l'atelier.

## Map

Arrows go from upstream (U) to downstream (D): the direction of influence. A change upstream reaches downstream, never the other way round.

```
            [Identity] ── upstream of every context (conformist) ──┐
                                                                   │
 [Listing] ─────────▶ [Booking] ─────────▶ [Rental]
     U   customer/supplier   D/U  customer/supplier  D
                          │                     │ EquipmentReturned (published language)
                          │                     ├──────────────▶ [Payment] ── anticorruption ──▶ (payment provider)
                          │                     └──────────────▶ [Review]
                          ▼
 [Dispute] ◀── conformist to Booking, Rental, Payment
```

## Relationships

| Upstream | Downstream | Relationship | Why |
|---|---|---|---|
| `Listing` | `Booking` | Customer / supplier | Two core teams that plan together: the needs of `Booking` go into the plans of `Listing` |
| `Booking` | `Rental` | Customer / supplier | A rental starts from a confirmed booking; `Booking` takes the needs of `Rental` into account |
| `Rental` | `Payment`, `Review` | Published language | `Rental` publishes a documented, stable fact: `EquipmentReturned`. Listeners do not know `Rental` |
| `Booking`, `Rental`, `Payment` | `Dispute` | Conformist | The core contexts will not change their model for a supporting one, and that model suits disputes: `Dispute` takes their concepts as they are |
| Payment provider (external) | `Payment` | Anticorruption layer | The provider does not listen to us and its model is not ours: a layer translates it (episode 10) |
| `Identity` | every context | Conformist | Built on Symfony Security: every context takes its user as it is |

## Needs recorded, not yet built

- `Rental` learns that a booking is confirmed from a published fact (episode 7), never by importing `BookingStatus`.

## Shared kernel

Kept minimal and owned by every context: identifiers and `Money` (episode 4), in `src/Shared/Domain/`. Any change there involves every context, so nothing else goes in.

`src/Shared/Infrastructure/` is technical plumbing (HTTP, kernel), not a shared kernel.

## Rule in the code

A relationship describes meaning, not imports: a conformist context reuses the concepts of its upstream (its words, its states), not its classes. So no context imports a class from another one; they only share the shared kernel, and will exchange published facts (episode 7).

Deptrac enforces this rule (`deptrac.yaml`). It checks PHP imports only: neither the kind of relationship, nor shared tables, nor HTTP calls.
