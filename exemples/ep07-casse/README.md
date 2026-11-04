# Épisode 7 — la cassure

*English below.*

Le correctif remonte, dans la méthode `book()` de `projet/src/Booking/Domain/BookingCalendar.php`, l'enregistrement de l'événement `SlotBooked` tout en haut, avant les vérifications, « pour ne pas l'oublier ». Le calendrier enregistre alors l'événement même quand il refuse la réservation : il garde un fait qui n'a pas eu lieu. Il suffit qu'un appelant attrape l'erreur et continue pour l'annoncer. PHP-CS-Fixer et PHPStan restent verts : le typage est intact. Le dépôt publié, sans le correctif, reste vert.

```bash
git apply exemples/ep07-casse/01-s07-record-before-checking.patch   # casser
make qa                                                             # lire l'échec
```

Résultat attendu : `make qa` s'arrête sur PHPUnit, avec un échec dans `BookingCalendarTest` :

- `testRecordsNoEventForARefusedBooking`, avec `Failed asserting that actual size 2 matches expected size 1.`

Le test réserve le créneau de Nora, puis tente celui de Hugo, qui le chevauche : la réservation est refusée, et le calendrier ne doit rendre qu'un seul `SlotBooked`, celui de Nora. Avec le correctif, il en rend deux.

Pour réparer, retire le correctif (`git apply -R exemples/ep07-casse/01-s07-record-before-checking.patch`) et relance `make qa`.

## English

The patch moves, in the `book()` method of `projet/src/Booking/Domain/BookingCalendar.php`, the recording of the `SlotBooked` event to the very top, before the checks, "so that we do not forget it". The calendar then records the event even when it refuses the booking: it keeps a fact that never happened. Apply it with `git apply`, run `make qa`: PHPUnit fails once in `BookingCalendarTest` (`testRecordsNoEventForARefusedBooking`: `Failed asserting that actual size 2 matches expected size 1.`), while PHP-CS-Fixer and PHPStan stay green. Fix it by reverting the patch (`git apply -R`).
