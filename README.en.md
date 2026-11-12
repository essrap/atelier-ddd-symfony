# Atelier DDD Symfony

*[Version française](README.md)*

Companion repository for the video series **"DDD, CQRS, SOLID and design patterns with Symfony"** (videos in French, with an English audio track).

It builds **Tooloc**, an equipment rental back end between individuals and professionals: owners list equipment, renters book it for a time slot.

## Getting started (under 5 minutes)

Requirements: **Docker**, **Git** and **make** (on Windows: use WSL). PHP and Composer run in the container, nothing else to install.

```bash
git clone https://github.com/essrap/atelier-ddd-symfony.git
cd atelier-ddd-symfony
make up        # starts the application: http://localhost:8000/health
make qa        # runs every quality gate
```

`make help` lists every command. On the first run, `make qa` installs the dependencies of the project and of every example.

## Quality gates (`make qa`)

From the cheapest to the most expensive, locally and in the CI:

1. `composer validate --strict` and `composer audit`
2. PHP-CS-Fixer (`@Symfony`, `@PER-CS`) — `make cs-fix` fixes the style
3. PHPStan at the maximum level, without baseline
4. Deptrac: no context imports the code of another one (only the shared kernel is common), nor Symfony, nor Doctrine
5. PHPUnit
6. Infection (mutation testing) on the project domain (`src/*/Domain`) and on the examples, minimum score of 85 %

Architecture decisions live in [`projet/docs/adr/`](projet/docs/adr/), the ubiquitous language in [`projet/docs/glossary.md`](projet/docs/glossary.md), the contexts in [`projet/docs/contexts.md`](projet/docs/contexts.md), their relationships in [`projet/docs/context-map.md`](projet/docs/context-map.md).

## Layout

- `projet/`: the full Symfony application (Symfony 7.4 LTS, PHP 8.5, PostgreSQL 18), built episode by episode.
- `exemples/`: isolated code for principles that are easier to show outside the project. Each example has its own `composer.json`.
  - `ep01-mutation/`: a throwaway rule to show unit tests and mutation testing.
  - `ep01-casse/`: the episode 1 breakages, to replay yourself, and the challenge.
  - `ep02-casse/`: the episode 2 breakage (a folder name the glossary forbids).
  - `ep03-casse/`: the episode 3 breakage (a context that imports the code of another one).
  - `ep04-casse/`: the episode 4 breakage (a value object changed in place instead of a new one).
  - `ep05-casse/`: the episode 5 breakage (an entity that loses the guard of one of its rules).
  - `ep06-casse/`: the episode 6 breakage (an aggregate that no longer guards its rule).
  - `ep07-casse/`: the episode 7 breakage (an event recorded before the rule allows it).
  - `ep08-casse/`: the episode 8 breakage (an in-memory version that keeps what was never saved).
  - `ep09-anemique/`: an anemic model, objects without rules and one service holding them all, which another service bypasses.
  - `ep09-casse/`: the episode 9 breakage (an application service that publishes before saving).
  - `ep10-casse/`: the episode 10 breakage (the payment provider renames a refusal code: only the translation fails).
  - `ep11-casse/`: the episode 11 breakage (the reviews CRUD imports another context: Deptrac refuses).

Each episode matches an `ep-NN` tag: you can pick up the series at any episode (`git checkout ep-11`).

## Status at tag `ep-11`

The episode 1 quality foundation and the episode 2 split (seven contexts in [`projet/docs/contexts.md`](projet/docs/contexts.md), three of them coded: `Listing`, `Booking`, `Rental`), plus the context map: their relationships in [`projet/docs/context-map.md`](projet/docs/context-map.md), and one Deptrac layer per context that forbids any import from one context into another. Since episode 4, the first value objects: `Money` in the shared kernel (an amount in minor units and its currency) and `Slot` in `Booking` (a time slot), with mutation testing on the project domain. Since episode 5, the first entity: `Listing`, with its typed identifier `ListingId` in the shared kernel, a named constructor, business methods instead of setters (`publish`, `withdraw`, `changeDailyPrice`) and a business exception, `ListingRuleBroken`. Since episode 6, the first aggregate: `BookingCalendar`, the bookings of one listing, which refuses any booking that overlaps another one and knows the listing only by its `ListingId`. Since episode 7, the first domain events: `SlotBooked`, `BookingCancelled` and `ListingPublished`, immutable facts named in the past tense (`DomainEvent` interface in the shared kernel). The aggregate records them after checking its rules (`RecordsEvents` trait), and the caller collects them with `releaseEvents()` to publish them once the aggregate is saved. The caller gives the time: `book`, `cancel` and `publish` take a `$now` argument. Nobody listens to these events yet. Since episode 8, the first repositories: one per aggregate, `Listings` and `BookingCalendars`, domain interfaces that find (`get`) and save (`save`) a whole aggregate, and throw `ListingNotFound` or `BookingCalendarNotFound` when it does not exist. Their first version, in memory (`InMemoryListings`, `InMemoryBookingCalendars`, in an `Infrastructure` folder), behaves like a database: it keeps only what is saved, without the events. No database is plugged in. Since episode 9, each piece of logic has its place: a rule of an object in that object; a rule that belongs to no object in a domain service, `CommissionCalculator` (`Payment` context: 10 % of the payment, rounded to the nearest cent, at least 1.00 of the same currency, a payment of 1.00 or less is refused); the steps of a use case in a thin application service, in an `Application` folder: `BookSlot` gets the calendar, books, saves it, then publishes its events through `DomainEventPublisher` (contract in the shared kernel, in-memory version `InMemoryDomainEventPublisher`); `OpenBookingCalendar` opens the calendar of a new listing and leaves an existing one untouched. Nothing calls them outside their tests yet: no controller, no subscriber, and nobody listens to the published events. Since episode 10, an anticorruption layer between `Payment` and the payment provider: the domain states its need in its own words, `DepositHolder` (hold and release a deposit, in `Money`; refusal: `DepositRefused`, with a reason of ours), and a single class, `GatewayDepositHolder`, translates both ways to the provider (decimal amounts, lowercase currency, refusal codes); an unknown code becomes `Other`. The provider is a simulator, `projet/simulator/PaymentGateway/`, that only `Payment` may use (Deptrac). Since episode 11, a context written without DDD on purpose: `Review` (reviews after a rental), a generic subdomain, as a plain CRUD: a Doctrine entity mapped with attributes and setters, a Symfony controller (`POST /reviews`, `GET /reviews?rental=…`), input validation (rating from 1 to 5) and a migration. Doctrine arrives here, limited to `Review` (Deptrac still forbids it in the other contexts), with the PostgreSQL database of `compose.yaml`, which `make qa` starts for the tests (ADR 0008).

## License

[MIT](LICENSE)
