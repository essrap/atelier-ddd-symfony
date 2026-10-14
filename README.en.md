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
4. Deptrac: the domain depends neither on Symfony nor on Doctrine
5. PHPUnit
6. Infection (mutation testing), minimum score of 85 %

Architecture decisions live in [`projet/docs/adr/`](projet/docs/adr/), the ubiquitous language in [`projet/docs/glossary.md`](projet/docs/glossary.md).

## Layout

- `projet/`: the full Symfony application (Symfony 7.4 LTS, PHP 8.5, PostgreSQL 18), built episode by episode.
- `exemples/`: isolated code for principles that are easier to show outside the project. Each example has its own `composer.json`.
  - `ep01-mutation/`: a throwaway rule to show unit tests and mutation testing.
  - `ep01-casse/`: the episode 1 breakages, to replay yourself, and the challenge.

Each episode matches an `ep-NN` tag: you can pick up the series at any episode (`git checkout ep-01`).

## Status at tag `ep-01`

The quality foundation is in place: Docker, Symfony, a `/health` route, and every quality gate, locally and in the CI. No bounded context is coded yet: that is what the next episodes are about.

## License

[MIT](LICENSE)
