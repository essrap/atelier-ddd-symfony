# 2. Enforce quality gates from day one

- Status: accepted
- Date: 2026-10-14

## Context

The domain rules of Tooloc (bookings, deposits, disputes) will change many times during the series. Each change must not silently break another part of the application.

## Decision

Every change must pass the same checks, locally with `make qa` and in the CI, from the cheapest to the most expensive:

1. `composer validate --strict` and `composer audit`
2. PHP-CS-Fixer (`@Symfony` and `@PER-CS` rules)
3. PHPStan at the maximum level with strict rules, without baseline
4. Deptrac: the domain depends neither on Symfony nor on Doctrine
5. PHPUnit
6. Infection (mutation testing), minimum MSI of 85 %

Database migrations and the OpenAPI contract are added to the pipeline when the project has entities and an API.

## Alternatives

- Adding the tools later, once the code exists: cheaper at first, but the first rules would then be written without a safety net.
- A PHPStan baseline: hides existing errors instead of fixing them.

## Consequences

- Setting up the project takes longer; every later change is safer.
- A failing check blocks the merge: nobody can skip it.
