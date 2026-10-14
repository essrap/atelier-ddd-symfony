# Épisode 1 — les cassures

*English below.*

Chaque fichier `.patch` casse volontairement le code pour faire passer **un garde-fou** au rouge, comme dans la vidéo. Le dépôt, lui, reste toujours vert.

```bash
git apply exemples/ep01-casse/02-s06-json-encode-may-fail.patch   # casser
make qa                                                              # lire l'échec
git apply -R exemples/ep01-casse/02-s06-json-encode-may-fail.patch  # réparer
```

| Cassure | Séquence | Ce qui est cassé | Garde-fou au rouge dans `make qa` |
|---|---|---|---|
| `01-s05-status-code-as-string` | S05 | une chaîne passée comme code HTTP | PHPStan ; à l'exécution, `curl localhost:8000/health` renvoie 500 (`TypeError`, grâce à `strict_types`) |
| `02-s06-json-encode-may-fail` | S06 | `json_encode()` sans `JSON_THROW_ON_ERROR` | PHPStan (`string\|false` au lieu de `string`) |
| `03-s07-wrong-limit` | S07 | la limite passe de 30 à 29 jours | PHPUnit (exemple) |
| `04-s08-limits-not-tested` | S08 | les cas limites 1 et 30 retirés du test | Infection : les tests passent, mais des mutants survivent (MSI 75 % < 85 %) |
| `05-s09-domain-depends-on-symfony` | S09 | le domaine importe `Request` de Symfony | Deptrac : PHPStan reste vert (Symfony est installé, comme dans un vrai projet), seul Deptrac voit la frontière franchie |
| `06-s10-class-not-final` | S10 | une classe qui n'est plus `final` | PHP-CS-Fixer (`make cs-fix` corrige) |
| `07-s10-missing-license` | S10 | la licence retirée de `composer.json` | `composer validate --strict` |
| `08-s11-two-gates` | S11 | deux erreurs à la fois | PHP-CS-Fixer d'abord ; après `make cs-fix`, PHPStan. `make qa` s'arrête au premier rouge |
| `09-challenge` | S13 | ? | **Le défi** : devine quel garde-fou passe au rouge, puis vérifie. Réponse dans [SOLUTION.md](SOLUTION.md) |

## English

Each `.patch` file deliberately breaks the code so that **one quality gate** turns red, as in the video. Apply it with `git apply`, run `make qa`, then revert it with `git apply -R`. The table above lists, for each breakage, the gate that fails. The answer to the challenge (`09-challenge`) is in [SOLUTION.md](SOLUTION.md).
