# 4. Record domain events in the aggregate, publish them after saving

- Status: accepted
- Date: 2026-11-04

## Context

Other parts of Tooloc need to know when something happens in an aggregate: the owner must be told that a slot of their listing is booked or freed, billing must count the bookings, a catalogue will show the published listings. The aggregate must not call them itself: it would depend on every part that listens to it, and it could announce a change before it is saved.

## Decision

- A domain event is an immutable `final readonly` class, named in the past tense, that implements `App\Shared\Domain\DomainEvent` (shared kernel) and carries what it needs to be understood alone: identifiers, values and `occurredOn`.
- Events live flat in the domain of their context: `SlotBooked` and `BookingCancelled` in `Booking`, `ListingPublished` in `Listing`.
- An aggregate records its events with the `App\Shared\Domain\RecordsEvents` trait, only after its rules have allowed the change. `releaseEvents()` returns them in order and empties the list.
- The caller gives the time: `book`, `cancel` and `publish` take a `\DateTimeImmutable $now` argument, copied into `occurredOn`. The domain stays free of any clock dependency and the tests are deterministic.
- The caller publishes the released events once the aggregate is saved, never before.
- These are domain events: they stay inside their bounded context. Another context, such as billing, will later receive a published integration event with its own contract, never `SlotBooked` itself.
- An event is added only when a named business need waits for that fact. `changeDailyPrice` and `withdraw` record nothing for now.

## Alternatives

- The aggregate calls the listeners (e-mail, billing) directly: the domain would depend on them.
- The aggregate publishes through an injected bus or publisher: unless the publication shares the saving transaction, it risks announcing facts before the aggregate is saved, and the domain would depend on that publisher.
- A clock service (PSR-20) injected in the domain: one more concept, for no gain while the caller can pass the time.
- A base class instead of a trait: it would take the only parent slot of every aggregate.

## Consequences

- Each aggregate test can check the events it releases.
- `book`, `cancel` and `publish` have one more argument; their existing tests pass a fixed date.
- Until repositories exist, nothing saves the aggregates and nobody publishes or listens to the events. A message bus and an outbox come later.
