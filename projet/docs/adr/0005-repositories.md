# 5. One repository per aggregate, in the domain, saved explicitly

- Status: accepted
- Date: 2026-11-05

## Context

An aggregate object lives in memory for one request only. To book a slot, the next request must find the calendar of the listing as the previous one left it, and the events of episode 7 must be published only once the aggregate is saved. The domain must not know how aggregates are stored: no SQL, no Doctrine in `Domain`.

## Decision

- One repository per aggregate, never per entity or per table: `Listings` for `Listing`, `BookingCalendars` for `BookingCalendar`. A `Booking` has no repository: it is found and saved with its calendar.
- The repository is an interface in the domain of its context, named as a plural, with two methods only: `get(id)` returns a whole aggregate, `save(aggregate)` stores it. Anything else waits for a named need.
- `get` throws a dedicated exception when nothing was saved under this identity (`ListingNotFound`, `BookingCalendarNotFound`), never returns `null`.
- Saving is explicit (persistence-oriented style): the caller finds the aggregate, changes it, saves it, then publishes the events it releases.
- The first implementation is in memory, in the `Infrastructure` folder of the context. By choice, it stores and returns deep copies (`serialize`/`unserialize`), so that, like a database from one request to the next, it keeps only what was saved. It never keeps the events of the aggregate. The serialized string never leaves the repository, so `unserialize` needs no `allowed_classes`.

## Alternatives

- Collection-oriented repository: `add` for a new aggregate, and no `save` for a change, because the repository tracks the changes and returns the same object. Valid, but the moment of saving a change is no longer visible in the code, and the in-memory version would have to track changes too.
- A generic `findBy(array $criteria)`: an untyped array of criteria, often column names, and a method that answers no named need. Queries by criteria are not wrong in themselves (a typed method or a specification can express one), but none is needed today.
- `get` returning `null`: every caller must remember to check it.
- `clone` instead of a deep copy: the bookings inside a calendar would stay shared between the stored calendar and the caller's one.

## Consequences

- An aggregate can be found and saved without a database; the in-memory versions have their own tests.
- Nothing calls the repositories outside their tests yet: the application service will (episode 9).
- The Doctrine version will implement the same interfaces (episode 18); both versions will share one contract test suite (episode 14). Symfony binds an interface to its only implementation automatically: once there are two, the binding must be declared.
