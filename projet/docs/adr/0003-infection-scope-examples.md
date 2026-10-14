# 3. Run mutation testing on the episode 1 example until the domain exists

- Status: accepted
- Date: 2026-10-14

## Context

The quality gates require a minimum mutation score on `src/*/Domain`. In episode 1 the project has no domain code yet, so Infection would have nothing to mutate.

## Decision

Until the first domain code arrives (episode 4), Infection runs on the isolated example `exemples/ep01-mutation/`, a throwaway rule (a rental lasts from 1 to 30 days) that is not part of the Tooloc model. From episode 4, Infection runs on `src/*/Domain` with the same 85 % threshold, and the example leaves the score.

## Alternatives

- Disabling Infection until episode 4: the gate would not be shown or tested.
- Writing temporary domain code in `src/`: it would anticipate the domain modelling episodes.

## Consequences

- `make qa` and the CI run Infection from episode 1.
- The 30-day rule never becomes a Tooloc business rule.
