# Épisode 5 — la cassure

*English below.*

Le correctif retire, « pour aller plus vite », la garde de la méthode `publish()` de `projet/src/Listing/Domain/Listing.php` : seule une annonce en brouillon pouvait être publiée. Sans elle, une annonce retirée peut être republiée. Le code compile et PHPStan reste vert : le typage est intact, seule la règle métier tombe. Le dépôt, lui, reste toujours vert.

```bash
git apply exemples/ep05-casse/01-s06-publish-without-guard.patch   # casser
make qa                                                            # lire l'échec
```

Résultat attendu : `make qa` s'arrête sur PHPUnit, avec deux échecs dans `ListingTest` :

- `testCannotPublishTwice`
- `testCannotRepublishAWithdrawnListing`

tous deux avec `Failed asserting that exception of type "App\Listing\Domain\ListingRuleBroken" is thrown.`

`public private(set)` n'y pouvait rien : la modification vient de l'annonce elle-même. Seuls les tests l'attrapent.

Pour réparer, retire le correctif (`git apply -R exemples/ep05-casse/01-s06-publish-without-guard.patch`) et relance `make qa`.

## English

The patch removes, "to go faster", the guard of the `publish()` method in `projet/src/Listing/Domain/Listing.php`: only a draft could be published. Without it, a withdrawn listing can be published again. It compiles and PHPStan stays green. Apply it with `git apply`, run `make qa`: PHPUnit fails twice (`testCannotPublishTwice`, `testCannotRepublishAWithdrawnListing`: `Failed asserting that exception of type "App\Listing\Domain\ListingRuleBroken" is thrown.`). `public private(set)` cannot help here: the change comes from the listing itself, and only the tests catch it. Fix it by reverting the patch (`git apply -R`).
