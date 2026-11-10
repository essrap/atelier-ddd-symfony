# Épisode 9 — la cassure

*English below.*

Le correctif s'applique sur le tag `ep-09` (`git checkout ep-09`).

Le correctif change, dans `projet/src/Booking/Application/BookSlot.php`, l'ordre des étapes : la ligne `publish` remonte avant `save`, « tant qu'à faire, on prévient tout de suite ». PHP-CS-Fixer, PHPStan et Deptrac restent verts : le typage et les dépendances sont intacts. Le code publié au tag `ep-09`, sans le correctif, reste vert.

```bash
git apply exemples/ep09-casse/01-s06-publish-before-save.patch   # casser
make qa                                                          # lire l'échec
```

Résultat attendu : `make qa` s'arrête sur PHPUnit, avec un échec dans `BookSlotTest` :

- `testNothingIsPublishedWhenTheSaveFails`, avec `Failed asserting that two arrays are identical.` : la liste attendue est vide, la liste publiée contient un `SlotBooked`.

Le test remplace le dépôt par `BookingCalendarsWithStorageDown`, dont `save` lève `StorageIsDown` (la base est tombée). Malik réserve la remorque d'Inès : l'enregistrement échoue, donc rien ne doit être publié. Avec le correctif, `SlotBooked` est déjà parti. Le test attrape l'exception (`try` / `self::fail` / `catch`) au lieu d'utiliser `expectException`, qui arrêterait le test à l'exception et ne vérifierait jamais la publication.

Pour réparer, retire le correctif (`git apply -R exemples/ep09-casse/01-s06-publish-before-save.patch`) et relance `make qa`.

## English

The patch applies to the `ep-09` tag (`git checkout ep-09`).

The patch changes the order of the steps in `projet/src/Booking/Application/BookSlot.php`: the `publish` line moves up before `save`, "let's tell everyone right away". Apply it with `git apply`, run `make qa`: PHPUnit stops on one failure in `BookSlotTest` (`testNothingIsPublishedWhenTheSaveFails`: `Failed asserting that two arrays are identical.`, the expected list is empty, the published one holds a `SlotBooked`), while PHP-CS-Fixer, PHPStan and Deptrac stay green. The test replaces the repository with `BookingCalendarsWithStorageDown`, whose `save` throws `StorageIsDown`; it catches the exception (`try` / `self::fail` / `catch`) rather than using `expectException`, which would stop the test at the exception and never check what was published. Fix it by reverting the patch (`git apply -R`).
