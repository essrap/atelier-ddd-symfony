# 8. Review is a plain CRUD

- Status: accepted
- Date: 2026-11-12

## Context

After a rental, the renter leaves a review: a rating from one to five stars and an optional comment. Episode 2 classed `Review` as a generic subdomain: it does not set Tooloc apart from its competitors, its rules hardly ever change, and the business has no words of its own for it. Writing it like the core (typed identifier, value object for the rating, aggregate, repository interface, events) would cost about six files to protect almost nothing.

Three questions sort a need: is it what sets us apart? Are there rules that change often? Does the business talk about it with its own words? Three "no" for reviews.

## Decision

- `Review` is a plain CRUD: a Doctrine entity mapped with attributes, getters and setters, a Doctrine repository, a Symfony controller (`POST /reviews`, `GET /reviews?rental=…`), one migration.
- Input is checked at the edge with Symfony Validator (`Range` 1 to 5, `Length` 500, `NotBlank` and `Length` 64 for the rental reference): there is no invariant to protect, only input to refuse (`422`).
- No `Domain`, `Application` or `Infrastructure` folders inside `src/Review/`: a CRUD has no layers to separate.
- `Review` is not `final`: Doctrine entities are excluded from the `final_class` rule, so the ORM can build lazy proxies.
- Doctrine arrives in the project here, limited to `Review`. The core keeps its in-memory repositories until episode 18, where it gets an XML mapping.
- Deptrac allows Doctrine and Symfony in `Review`, and, for now, in no other context: `Identity`, conformist to Symfony Security, will be the second. `Review` may not use the other contexts: the rental it refers to is a plain reference string.
- Tests are functional, against the PostgreSQL service of `compose.yaml`: `make qa` starts it and migrates the test database first, then checks that the Doctrine mapping matches the schema. Mutation testing still covers `src/*/Domain` only (ADR 0003): `Review` has no domain.

## Alternatives

- The same patterns as the core: six files for four stars, and nothing they would protect.
- A third-party reviews service: an integration and an anticorruption layer for a feature this simple.
- SQLite for the tests: simpler, but another engine than production.

## Consequences

- The CRUD does not guarantee business rules: a rental can be reviewed twice, or before the equipment is returned.
- When such a rule arrives, `Review` moves to the core patterns, in this context only. A rule that needs another context (only a returned rental can be reviewed) is not CRUD any more: it will come through a published fact, `EquipmentReturned`, once messages are wired. `exemples/ep11-casse/` shows Deptrac refusing the shortcut.
- The CI needs the database service: `make qa` starts it.
