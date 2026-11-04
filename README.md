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
4. Deptrac : aucun contexte n'importe le code d'un autre (seul le noyau partagé est commun), ni Symfony, ni Doctrine
5. PHPUnit
6. Infection (tests de mutation) sur le domaine du projet (`src/*/Domain`) et sur les exemples, score minimal de 85 %

Les décisions d'architecture sont dans [`projet/docs/adr/`](projet/docs/adr/), le vocabulaire métier dans [`projet/docs/glossary.md`](projet/docs/glossary.md), les contextes dans [`projet/docs/contexts.md`](projet/docs/contexts.md), leurs relations dans [`projet/docs/context-map.md`](projet/docs/context-map.md).

## Organisation

- `projet/` : l'application Symfony complète (Symfony 7.4 LTS, PHP 8.5, PostgreSQL 18), construite épisode par épisode.
- `exemples/` : du code isolé pour les principes qui se montrent mieux hors du projet. Chaque exemple a son propre `composer.json`.
  - `ep01-mutation/` : une règle jetable pour montrer les tests et les tests de mutation.
  - `ep01-casse/` : les cassures de l'épisode 1, à rejouer soi-même, et le défi.
  - `ep02-casse/` : la cassure de l'épisode 2 (un nom de dossier que le glossaire interdit).
  - `ep03-casse/` : la cassure de l'épisode 3 (un contexte qui importe le code d'un autre).
  - `ep04-casse/` : la cassure de l'épisode 4 (un objet valeur que l'on modifie au lieu d'en créer un nouveau).
  - `ep05-casse/` : la cassure de l'épisode 5 (une entité qui perd la garde d'une de ses règles).
  - `ep06-casse/` : la cassure de l'épisode 6 (un agrégat qui ne garde plus sa règle).
  - `ep07-casse/` : la cassure de l'épisode 7 (un événement enregistré avant que la règle ne l'autorise).

Chaque épisode correspond à un tag `ep-NN` : tu peux reprendre la série à n'importe quel épisode (`git checkout ep-07`).

## État au tag `ep-07`

Le socle qualité de l'épisode 1 et le découpage de l'épisode 2 (sept contextes dans [`projet/docs/contexts.md`](projet/docs/contexts.md), trois codés : `Listing`, `Booking`, `Rental`), plus la carte des contextes : leurs relations dans [`projet/docs/context-map.md`](projet/docs/context-map.md), et une couche Deptrac par contexte qui interdit tout import d'un contexte vers un autre. Depuis l'épisode 4, les premiers objets valeur : `Money` dans le noyau partagé (montant en centimes et devise) et `Slot` dans `Booking` (un créneau), avec les tests de mutation sur le domaine du projet. Depuis l'épisode 5, la première entité : `Listing` (une annonce), avec son identifiant typé `ListingId` dans le noyau partagé, un constructeur nommé, des méthodes métier à la place des setters (`publish`, `withdraw`, `changeDailyPrice`) et une exception métier, `ListingRuleBroken`. Depuis l'épisode 6, le premier agrégat : `BookingCalendar`, le calendrier de réservations d'une annonce, qui refuse toute réservation qui en chevauche une autre et ne connaît l'annonce que par son `ListingId`. Depuis l'épisode 7, les premiers événements de domaine : `SlotBooked`, `BookingCancelled` et `ListingPublished`, des faits immuables nommés au passé (interface `DomainEvent` dans le noyau partagé). L'agrégat les enregistre après avoir vérifié ses règles (trait `RecordsEvents`), et l'appelant les récupère avec `releaseEvents()` pour les publier une fois l'agrégat enregistré. L'heure est donnée par l'appelant : `book`, `cancel` et `publish` prennent un argument `$now`. Personne n'écoute encore ces événements.

## Licence

[MIT](LICENSE)
