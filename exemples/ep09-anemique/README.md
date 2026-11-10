# Épisode 9 — un modèle anémique

*English below.*

Un exemple isolé, volontairement mal rangé : ce code ne fait pas partie de Tooloc.

- `before/BookingCalendar.php` ne porte que des données : `getBookings()` et `setBookings()`, aucune règle.
- `before/BookingService.php` porte tout dans `book()` : le chevauchement, la commission de la plateforme (10 %, au moins 1 €), l'enregistrement et l'envoi des nouvelles (une liste de messages, `$sent`).
- `before/ManualBookingService.php` ajoute une réservation saisie à la main par `setBookings()`, sans vérifier le chevauchement, et recalcule la commission à sa façon (`intdiv($price, 10)` : ni arrondi, ni plancher).

Le test `testAnotherServiceCanBypassTheOverlapRule` (`tests/ManualBookingServiceTest.php`) est vert : c'est le constat. Malik réserve la remorque par `BookingService` ; une seconde réservation qui chevauche la sienne passe par `ManualBookingService` ; le calendrier en contient deux. La règle vit dans un service, n'importe quel autre service la contourne.

Il n'y a pas de dossier `after/` : la version riche, c'est le projet lui-même.

- La règle du chevauchement vit dans l'objet qui la protège : `BookingCalendar` (`projet/src/Booking/Domain/`, épisode 6).
- La commission, qui n'appartient à aucun objet, vit dans un service de domaine : `CommissionCalculator` (`projet/src/Payment/Domain/`).
- L'enchaînement des étapes vit dans un service applicatif mince : `BookSlot` (`projet/src/Booking/Application/`) retrouve le calendrier, le fait agir, l'enregistre, puis publie ses événements. `OpenBookingCalendar` ouvre le calendrier d'une annonce neuve et ne fait rien s'il existe déjà : il essaie `get` et n'enregistre un calendrier vide que sur `BookingCalendarNotFound`, sans méthode `has()` sur le dépôt (ADR 0006).

`make qa-examples` passe cet exemple aux mêmes garde-fous que le projet : PHPStan au niveau maximal, Deptrac, PHPUnit et Infection (85 % au moins). Les garde-fous restent verts : aucun outil ne voit où vivent les règles, c'est à toi de les ranger.

## English

An isolated example, badly organised on purpose: this code is not part of Tooloc.

- `before/BookingCalendar.php` only carries data: `getBookings()` and `setBookings()`, no rule.
- `before/BookingService.php` does everything in `book()`: the overlap check, the platform commission (10 %, at least 1 €), saving, and sending the news (a list of messages, `$sent`).
- `before/ManualBookingService.php` adds a booking typed in by hand through `setBookings()`, without checking the overlap, and computes the commission its own way (`intdiv($price, 10)`: no rounding, no minimum).

The test `testAnotherServiceCanBypassTheOverlapRule` is green, and that is the point: Malik books the trailer through `BookingService`, an overlapping booking goes through `ManualBookingService`, and the calendar holds both. When the rule lives in a service, any other service can bypass it.

There is no `after/` folder: the rich version is the project itself. The overlap rule lives in `BookingCalendar` (episode 6), the commission in the `CommissionCalculator` domain service, and the steps of the use case in the thin `BookSlot` application service, which gets the calendar, makes it act, saves it, then publishes its events. `OpenBookingCalendar` opens the calendar of a new listing and does nothing if it already exists: it tries `get` and saves an empty calendar only on `BookingCalendarNotFound`, with no `has()` method on the repository (ADR 0006).

`make qa-examples` runs the same quality gates as the project on this example. They stay green: no tool sees where the rules live.
