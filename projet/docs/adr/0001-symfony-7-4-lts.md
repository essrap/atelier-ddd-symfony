# 1. Use Symfony 7.4 LTS

- Status: accepted
- Date: 2026-10-14

## Context

Tooloc is a reference back end that students and developers may reuse for months, for example for a final-year project. When the project starts, Symfony 7.4 is the current long-term support release, Symfony 8.1 is maintained until January 2027 only, and the next LTS (8.4) is expected in November 2027.

## Decision

Build the project on **Symfony 7.4 LTS** with **PHP 8.5**. Development runs in the official `php:8.5.11-cli-alpine` image with PHP's built-in web server; a production image comes later in the series.

## Alternatives

- Symfony 8.1: newer, but its support ends while the series is still being published.
- Waiting for Symfony 8.2: not an LTS either, and it would delay the project.

## Consequences

- Bug fixes until November 2028 and security fixes until November 2029.
- A bonus episode will upgrade the project to Symfony 8.4 LTS once it is released, using the quality gates to do it safely.
