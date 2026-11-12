# Épisode 11 — la cassure

*English below.*

Le correctif s'applique sur le tag `ep-11` (`git checkout ep-11`).

Il ajoute une règle aux avis : « on ne note qu'une location restituée ». Pour la vérifier, `ReviewController` importe `RentalStatus`, du contexte `Rental`. Les tests sont mis à jour : ils restent verts.

```bash
git apply exemples/ep11-casse/01-s06-review-reads-the-rental-status.patch   # la règle franchit la frontière
make qa                                                                     # lire l'échec
```

Résultat attendu : `make qa` s'arrête sur Deptrac, avec une seule violation :

- `App\Review\Controller\ReviewController must not depend on App\Rental\Domain\RentalStatus` (couche `Rental`).

Le CRUD a le droit d'utiliser Doctrine et Symfony, pas les autres contextes. Une règle qui a besoin d'un autre contexte n'est plus du CRUD : elle passera par un fait publié, `EquipmentReturned`.

Le correctif est volontairement naïf : le statut vient de la requête, donc du client. Une vraie règle lirait le statut du contexte `Rental`. Il ne sert qu'à montrer la frontière.

Pour réparer, retire le correctif :

```bash
git apply -R exemples/ep11-casse/01-s06-review-reads-the-rental-status.patch
make qa                                                                        # tout est vert
```

## English

The patch applies to the `ep-11` tag. It adds a rule to reviews, "only a returned rental can be reviewed", by importing `RentalStatus` from the `Rental` context into `ReviewController`; the tests are updated and stay green. `make qa` then stops on Deptrac with a single violation: `ReviewController must not depend on RentalStatus`. The patch is deliberately naive: the status comes from the request, so from the client; a real rule would read it from the `Rental` context. It only shows the boundary. The CRUD may use Doctrine and Symfony, not the other contexts. Remove the patch with `git apply -R` and everything is green again.
