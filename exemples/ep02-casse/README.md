# Épisode 2 — la cassure

*English below.*

Le correctif ajoute `src/Reservation/Domain/Reservation.php` : un collègue crée un dossier avec le mot que le glossaire demande d'éviter. Le dépôt, lui, reste toujours vert.

```bash
git apply exemples/ep02-casse/01-s06-reservation-forbidden.patch   # casser
make qa                                                             # lire l'échec
```

Résultat attendu : `make qa` s'arrête sur PHPUnit, avec deux échecs de `ContextNamingTest` :

- `src/Reservation is not a declared context` : le dossier n'est pas déclaré dans `projet/docs/contexts.md` ;
- `Reservation is forbidden, use Booking (glossary)` : le glossaire (colonne « Avoid ») interdit ce mot.

Pour réparer, soit tu retires le correctif (`git apply -R exemples/ep02-casse/01-s06-reservation-forbidden.patch`), soit tu fais la correction métier : déplace le fichier dans `projet/src/Booking/Domain/`, renomme la classe en `Booking`, puis relance `make qa`.

## English

The patch adds `src/Reservation/Domain/Reservation.php`: a folder named with a word the glossary asks to avoid. Apply it with `git apply`, run `make qa`: PHPUnit fails twice in `ContextNamingTest` (undeclared context folder, forbidden word). Fix it by reverting the patch (`git apply -R`), or by moving the file to `projet/src/Booking/Domain/` and renaming the class `Booking`.
