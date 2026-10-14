# Atelier DDD Symfony

*[English version](README.en.md)*

Dépôt d'accompagnement de la série vidéo **« DDD, CQRS, SOLID et design patterns avec Symfony »**.

On y construit **Tooloc**, un back-end de location de matériel entre particuliers et professionnels : des loueurs publient du matériel, des locataires le réservent sur un créneau.

## Démarrer (moins de 5 minutes)

Prérequis : **Docker**, **Git** et **make** (sous Windows : utilise WSL). PHP et Composer tournent dans le conteneur, rien d'autre à installer.

```bash
git clone https://github.com/essrap/atelier-ddd-symfony.git
cd atelier-ddd-symfony
make up        # démarre l'application : http://localhost:8000/health
make qa        # lance tous les garde-fous
```

`make help` liste toutes les commandes. Au premier lancement, `make qa` installe les dépendances du projet et de chaque exemple.

## Les garde-fous (`make qa`)

Du moins coûteux au plus coûteux, en local comme dans la CI :

1. `composer validate --strict` et `composer audit`
2. PHP-CS-Fixer (`@Symfony`, `@PER-CS`) — `make cs-fix` corrige le style
3. PHPStan au niveau maximal, sans baseline
4. Deptrac : le domaine ne dépend ni de Symfony ni de Doctrine
5. PHPUnit
6. Infection (tests de mutation), score minimal de 85 %

Les décisions d'architecture sont dans [`projet/docs/adr/`](projet/docs/adr/), le vocabulaire métier dans [`projet/docs/glossary.md`](projet/docs/glossary.md).

## Organisation

- `projet/` : l'application Symfony complète (Symfony 7.4 LTS, PHP 8.5, PostgreSQL 18), construite épisode par épisode.
- `exemples/` : du code isolé pour les principes qui se montrent mieux hors du projet. Chaque exemple a son propre `composer.json`.
  - `ep01-mutation/` : une règle jetable pour montrer les tests et les tests de mutation.
  - `ep01-casse/` : les cassures de l'épisode 1, à rejouer soi-même, et le défi.

Chaque épisode correspond à un tag `ep-NN` : tu peux reprendre la série à n'importe quel épisode (`git checkout ep-01`).

## État au tag `ep-01`

Le socle qualité est en place : Docker, Symfony, une route `/health`, et tous les garde-fous, en local et en CI. Aucun contexte métier n'est encore codé : c'est l'objet des épisodes suivants.

## Licence

[MIT](LICENSE)
