# Épisode 6 — la cassure

*English below.*

Le correctif retire de la méthode `book()` de `projet/src/Booking/Domain/BookingCalendar.php` la boucle qui refuse un créneau déjà pris, « parce qu'un service applicatif le vérifie déjà ». Le calendrier, racine de l'agrégat, ne garde plus la règle : deux réservations d'une même annonce peuvent alors se chevaucher. PHPStan reste vert : le typage est intact. Le dépôt publié, sans le correctif, reste vert.

```bash
git apply exemples/ep06-casse/01-s07-calendar-trusts-the-service.patch   # casser
make qa                                                                  # lire l'échec
```

Résultat attendu : `make qa` s'arrête sur PHPUnit, avec trois échecs dans `BookingCalendarTest` :

- `testRefusesABookingThatOverlapsAnother`
- `testAcceptsBackToBackBookings`
- `testACancelledBookingFreesItsSlot`

tous trois avec `Failed asserting that exception of type "App\Booking\Domain\BookingRuleBroken" is thrown.`

Pour réparer, retire le correctif (`git apply -R exemples/ep06-casse/01-s07-calendar-trusts-the-service.patch`) et relance `make qa`.

## English

The patch removes from the `book()` method of `projet/src/Booking/Domain/BookingCalendar.php` the loop that refuses a slot already taken, "because an application service already checks it". The calendar, root of the aggregate, no longer guards the rule. Apply it with `git apply`, run `make qa`: PHPUnit fails three times in `BookingCalendarTest` (`Failed asserting that exception of type "App\Booking\Domain\BookingRuleBroken" is thrown.`), while PHPStan stays green. Fix it by reverting the patch (`git apply -R`).
