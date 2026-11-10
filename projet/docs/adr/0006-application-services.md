# 6. Domain services for rules no object owns, thin application services for use cases

- Status: accepted
- Date: 2026-11-10

## Context

Since episode 8, aggregates can be found and saved, and since episode 7 they release events to publish once saved. Something must now run a use case: find the aggregate, make it act, save it, publish its events. At the same time, some rules belong to no object: the platform's commission on a payment is neither a value nor the state of an entity. Without a clear place for each kind of logic, every rule ends up in services and the objects only carry data (an anemic model, shown in `exemples/ep09-anemique/`).

## Decision

- A rule that belongs to an object stays in that object (`BookingCalendar` refuses an overlapping booking).
- A rule that belongs to no object goes in a domain service: stateless, named after what it does in the business, in the `Domain` folder of its context. `CommissionCalculator` (`Payment`) takes 10 % of a payment, rounded to the nearest cent, at least 1.00 of the same currency, and refuses a payment of 1.00 or less (`PaymentRuleBroken::tooSmallForCommission`) rather than leaving nothing to the owner.
- A use case goes in an application service, in the `Application` folder of its context: one invokable class per use case, no business rule, no condition on the business. `BookSlot` gets the calendar, books, saves, then publishes the released events, in that order.
- We keep the publishing contract, `DomainEventPublisher`, in the shared kernel next to `DomainEvent`: it only speaks the language of the domain (events), and every context may already use the shared kernel, so no Deptrac rule changes. Here the domain never calls it: application services use it, infrastructure implements it. Its first implementation, `InMemoryDomainEventPublisher`, keeps the events in a list; nobody listens yet.
- `OpenBookingCalendar` opens the calendar of a new listing and does nothing if it already exists, so that a fact received twice does not wipe the bookings. It tries `get` and saves an empty calendar only on `BookingCalendarNotFound`. The repository gets no `has()` method: a single caller does not justify widening the contract of episode 8, and `get` already says when nothing was saved.
- The caller still gives the time (`$now`), as in ADR 0004: no clock is injected while the caller can give the time.

## Alternatives

- A `Commission` value object computing itself: defensible, but what is being placed is a policy of the platform applied to an amount, not a value; the result stays a `Money`. `Money` is generic (shared kernel) and must not know the platform's policy; `Listing` neither, since the owner does not set the commission.
- Capping the commission at the payment instead of refusing it: the owner would get nothing, silently.
- The publishing contract as a port in the `Application` folder of each context: closer to who calls it, and the most common place, but one contract and one implementation per context. A shared application layer (`src/Shared/Application/`) would avoid that, but no Deptrac layer covers it today; the rules per layer come in episode 17.
- A `has()` method on `BookingCalendars`: one more method on every implementation, for one caller.
- Publishing before saving: a failed save would leave a published fact about a change that does not exist.

## Consequences

- Nothing calls `BookSlot`, `OpenBookingCalendar` or `CommissionCalculator` outside their tests yet. No controller, no command, no subscriber: `ListingPublished` will trigger `OpenBookingCalendar` once messages are wired.
- No transaction yet: one use case changes one aggregate and saves it once. The transaction comes with Doctrine (episode 18). Two openings at the same instant are left to locking (episode 19).
- Infection runs on `src/*/Domain` only (ADR 0003): it does not mutate `Application`. The order "save, then publish" is guarded by `BookSlotTest::testNothingIsPublishedWhenTheSaveFails`, whose save fails on purpose; `exemples/ep09-casse/` shows it turning red.
- Symfony binds `BookingCalendars` and `DomainEventPublisher` to their only implementation automatically: once there are two, the binding must be declared.
