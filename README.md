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
  - `ep08-casse/` : la cassure de l'épisode 8 (une version mémoire qui garde ce qui n'a pas été enregistré).
  - `ep09-anemique/` : un modèle anémique, des objets sans règles et un service qui les porte toutes, qu'un autre service contourne.
  - `ep09-casse/` : la cassure de l'épisode 9 (un service applicatif qui publie avant d'enregistrer).
  - `ep10-casse/` : la cassure de l'épisode 10 (le prestataire de paiement renomme un code de refus : seule la traduction échoue).
  - `ep11-casse/` : la cassure de l'épisode 11 (le CRUD des avis importe un autre contexte : Deptrac refuse).

Chaque épisode correspond à un tag `ep-NN` : tu peux reprendre la série à n'importe quel épisode (`git checkout ep-11`).

## État au tag `ep-11`

Le socle qualité de l'épisode 1 et le découpage de l'épisode 2 (sept contextes dans [`projet/docs/contexts.md`](projet/docs/contexts.md), trois codés : `Listing`, `Booking`, `Rental`), plus la carte des contextes : leurs relations dans [`projet/docs/context-map.md`](projet/docs/context-map.md), et une couche Deptrac par contexte qui interdit tout import d'un contexte vers un autre. Depuis l'épisode 4, les premiers objets valeur : `Money` dans le noyau partagé (montant en centimes et devise) et `Slot` dans `Booking` (un créneau), avec les tests de mutation sur le domaine du projet. Depuis l'épisode 5, la première entité : `Listing` (une annonce), avec son identifiant typé `ListingId` dans le noyau partagé, un constructeur nommé, des méthodes métier à la place des setters (`publish`, `withdraw`, `changeDailyPrice`) et une exception métier, `ListingRuleBroken`. Depuis l'épisode 6, le premier agrégat : `BookingCalendar`, le calendrier de réservations d'une annonce, qui refuse toute réservation qui en chevauche une autre et ne connaît l'annonce que par son `ListingId`. Depuis l'épisode 7, les premiers événements de domaine : `SlotBooked`, `BookingCancelled` et `ListingPublished`, des faits immuables nommés au passé (interface `DomainEvent` dans le noyau partagé). L'agrégat les enregistre après avoir vérifié ses règles (trait `RecordsEvents`), et l'appelant les récupère avec `releaseEvents()` pour les publier une fois l'agrégat enregistré. L'heure est donnée par l'appelant : `book`, `cancel` et `publish` prennent un argument `$now`. Personne n'écoute encore ces événements. Depuis l'épisode 8, les premiers dépôts : un par agrégat, `Listings` et `BookingCalendars`, des interfaces du domaine qui retrouvent (`get`) et enregistrent (`save`) un agrégat entier, et lèvent `ListingNotFound` ou `BookingCalendarNotFound` s'il n'existe pas. Leur première version, en mémoire (`InMemoryListings`, `InMemoryBookingCalendars`, dans un dossier `Infrastructure`), se comporte comme une base : elle ne garde que ce qui est enregistré, sans les événements. Aucune base de données n'est branchée. Depuis l'épisode 9, chaque logique a sa place : la règle d'un objet dans cet objet ; la règle qui n'appartient à aucun objet dans un service de domaine, `CommissionCalculator` (contexte `Payment` : 10 % du paiement, arrondi au centime le plus proche, au moins 1,00 de la même devise, un paiement de 1,00 ou moins est refusé) ; l'enchaînement d'un cas d'usage dans un service applicatif mince, dans un dossier `Application` : `BookSlot` retrouve le calendrier, réserve, l'enregistre, puis publie ses événements par `DomainEventPublisher` (contrat dans le noyau partagé, version mémoire `InMemoryDomainEventPublisher`) ; `OpenBookingCalendar` ouvre le calendrier d'une annonce neuve et ne touche pas à un calendrier existant. Rien ne les appelle encore hors de leurs tests : ni contrôleur, ni abonné, et personne n'écoute les événements publiés. Depuis l'épisode 10, une couche anticorruption entre `Payment` et le prestataire de paiement : le domaine dit ce dont il a besoin dans ses mots, `DepositHolder` (bloquer et libérer une caution, en `Money` ; refus : `DepositRefused`, avec une raison à nous), et une seule classe, `GatewayDepositHolder`, traduit dans les deux sens vers le prestataire (montants en décimales, devise en minuscules, codes de refus) ; un code inconnu devient `Other`. Le prestataire est un simulateur, `projet/simulator/PaymentGateway/`, que seul `Payment` peut utiliser (Deptrac). Depuis l'épisode 11, un contexte écrit volontairement sans DDD : `Review` (les avis après une location), sous-domaine générique, en simple CRUD : une entité Doctrine à attributs avec setters, un contrôleur Symfony (`POST /reviews`, `GET /reviews?rental=…`), une validation à l'entrée (note de 1 à 5) et une migration. Doctrine arrive ici, limité à `Review` (Deptrac l'interdit toujours aux autres contextes), avec la base PostgreSQL de `compose.yaml`, que `make qa` démarre pour les tests (ADR 0008).

## Licence

[MIT](LICENSE)
