# Épisode 10 — la cassure

*English below.*

Les correctifs s'appliquent sur le tag `ep-10` (`git checkout ep-10`).

Le premier correctif joue le prestataire qui sort une nouvelle version : dans `projet/simulator/PaymentGateway/Gateway.php`, le code de refus `insufficient_funds` devient `not_enough_funds`. Rien ne change chez nous.

```bash
git apply exemples/ep10-casse/01-s06-gateway-renames-a-code.patch   # le prestataire change
make qa                                                              # lire l'échec
```

Résultat attendu : `make qa` s'arrête sur PHPUnit, avec un seul échec, dans `GatewayDepositHolderTest` :

- `testInsufficientFundsBecomeOurReason`, avec `Failed asserting that two variables reference the same object.` : la raison attendue est `InsufficientFunds`, la traduction rend `Other`.

Aucun test du domaine n'échoue. Le nouveau code n'est jamais entré dans le domaine : inconnu de la traduction, il est devenu `Other`.

Le second correctif est la réparation : une ligne de la traduction, `GatewayDepositHolder`.

```bash
git apply exemples/ep10-casse/02-s06-translate-the-new-code.patch   # traduire le nouveau code
make qa                                                             # tout est vert
```

Pour revenir au tag, retire les deux correctifs (`git apply -R`, dans l'ordre inverse).

## English

The patches apply to the `ep-10` tag. The first one plays the provider releasing a new version: the refusal code `insufficient_funds` becomes `not_enough_funds`. `make qa` then stops on PHPUnit with a single failure, `GatewayDepositHolderTest::testInsufficientFundsBecomeOurReason` (`Other` instead of `InsufficientFunds`); no domain test fails. The second patch is the fix: one line of the translation. Remove both with `git apply -R`, in reverse order.
