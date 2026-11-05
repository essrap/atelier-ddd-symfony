# Épisode 8 — la cassure

*English below.*

Le correctif s'applique sur le tag `ep-08` (`git checkout ep-08`).

Le correctif change, dans `projet/src/Booking/Infrastructure/InMemoryBookingCalendars.php`, la méthode `get()` : elle rend le calendrier gardé lui-même au lieu d'une copie, « plus simple ». Une réservation faite sans `save` est alors gardée quand même. D'une requête à l'autre, une vraie base l'aurait perdue : la version mémoire ne se comporte plus comme elle. PHP-CS-Fixer et PHPStan restent verts : le typage est intact. Le code publié au tag `ep-08`, sans le correctif, reste vert.

```bash
git apply exemples/ep08-casse/01-s07-return-the-same-object.patch   # casser
make qa                                                              # lire l'erreur
```

Résultat attendu : `make qa` s'arrête sur PHPUnit, avec une erreur dans `InMemoryBookingCalendarsTest` :

- `testAChangeIsNotKeptUntilSaved`, avec `App\Booking\Domain\BookingRuleBroken: This slot overlaps another booking of the listing.`

Le test simule deux requêtes : la première réserve le créneau de Nora sans enregistrer le calendrier ; la seconde retrouve le calendrier, où Nora ne doit pas être, et réserve le même créneau pour Hugo. Avec le correctif, Nora est restée : la réservation de Hugo est refusée.

Pour réparer, retire le correctif (`git apply -R exemples/ep08-casse/01-s07-return-the-same-object.patch`) et relance `make qa`.

## English

The patch applies to the `ep-08` tag (`git checkout ep-08`).

The patch changes the `get()` method of `projet/src/Booking/Infrastructure/InMemoryBookingCalendars.php`: it returns the stored calendar itself instead of a copy, "it is simpler". A booking made without `save` is then kept anyway, while a real database would have lost it from one request to the next: the in-memory version no longer behaves like one. Apply it with `git apply`, run `make qa`: PHPUnit stops on one error in `InMemoryBookingCalendarsTest` (`testAChangeIsNotKeptUntilSaved`: `App\Booking\Domain\BookingRuleBroken: This slot overlaps another booking of the listing.`), while PHP-CS-Fixer and PHPStan stay green. Fix it by reverting the patch (`git apply -R`).
