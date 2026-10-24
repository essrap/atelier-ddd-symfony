# Épisode 4 — la cassure

*English below.*

Le correctif réécrit la méthode `add()` de `projet/src/Shared/Domain/Money.php` « au réflexe » : au lieu de renvoyer un nouvel objet, elle modifie le montant de l'objet courant (`$this->minorAmount += …`) et le renvoie. Le prix et la caution qui partagent ce `Money` changeraient tous les deux. Le dépôt, lui, reste toujours vert.

```bash
git apply exemples/ep04-casse/01-s05-mutable-money.patch   # casser
make qa                                                    # lire l'échec
```

Résultat attendu : `make qa` s'arrête sur PHPStan, avec une erreur dans `Money.php` (ligne 31) :

- `Readonly property App\Shared\Domain\Money::$minorAmount is assigned outside of the constructor.` (`property.readOnlyAssignNotInConstructor`)

PHPStan l'attrape avant toute exécution. Sans lui, PHP refuserait aussi l'écriture, mais seulement à l'exécution, avec une `Error`.

Pour réparer, retire le correctif (`git apply -R exemples/ep04-casse/01-s05-mutable-money.patch`) et relance `make qa`.

## English

The patch rewrites the `add()` method of `projet/src/Shared/Domain/Money.php` "by reflex": instead of returning a new object, it changes the amount of the current one (`$this->minorAmount += …`) and returns it. Apply it with `git apply`, run `make qa`: PHPStan fails before anything runs (`Readonly property App\Shared\Domain\Money::$minorAmount is assigned outside of the constructor.`). PHP would refuse the write too, but only at runtime, with an `Error`. Fix it by reverting the patch (`git apply -R`).
