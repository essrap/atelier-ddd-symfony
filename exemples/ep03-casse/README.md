# Épisode 3 — la cassure

*English below.*

Le correctif ajoute dans `projet/src/Rental/Domain/RentalStatus.php` une méthode `fromBooking()` : la location lit l'état de la réservation directement dans le contexte `Booking`. Le raccourci tient en une ligne, `use App\Booking\Domain\BookingStatus;`, et le code compile. Le dépôt, lui, reste toujours vert.

```bash
git apply exemples/ep03-casse/01-s07-rental-imports-booking.patch   # casser
make qa                                                              # lire l'échec
```

Résultat attendu : `make qa` s'arrête sur Deptrac, avec deux violations dans `RentalStatus.php` (lignes 22 et 24 : le type du paramètre et la comparaison) :

- `App\Rental\Domain\RentalStatus must not depend on App\Booking\Domain\BookingStatus` (couche `Booking`).

PHP-CS-Fixer, PHPStan et PHPUnit restent verts : seule la frontière entre contextes est enfreinte.

Pour réparer, retire le correctif (`git apply -R exemples/ep03-casse/01-s07-rental-imports-booking.patch`) et relance `make qa`. Le besoin ne disparaît pas pour autant : il est consigné dans `projet/docs/context-map.md` (« Rental learns that a booking is confirmed from a published fact »), et il sera codé avec les faits publiés de l'épisode 7.

## English

The patch adds a `fromBooking()` method to `projet/src/Rental/Domain/RentalStatus.php`: the rental reads the booking state straight from the `Booking` context. Apply it with `git apply`, run `make qa`: Deptrac fails twice (`RentalStatus must not depend on BookingStatus`, layer `Booking`), every other gate stays green. Fix it by reverting the patch (`git apply -R`). The need is recorded in `projet/docs/context-map.md` and built with the published facts of episode 7.
