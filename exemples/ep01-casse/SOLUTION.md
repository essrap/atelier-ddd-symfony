# Défi de l'épisode 1 — solution

*English below.*

`09-challenge` remplace `assertTrue(...)` par `assertNotNull(...)` dans le test de `MaxRentalDuration`.

- **PHPUnit reste vert** : un booléen n'est jamais `null`, donc le test passe toujours… et ne vérifie plus rien.
- **PHPStan passe au rouge** (avec l'extension `phpstan-phpunit`) : « `assertNotNull()` with bool will always evaluate to true ».
- Si PHPStan n'était pas là, **Infection** l'aurait vu aussi : des mutants survivraient.

La leçon : un test vert ne prouve pas qu'il teste quelque chose. C'est pour ça que les garde-fous se complètent.

## English

`09-challenge` replaces `assertTrue(...)` with `assertNotNull(...)`. PHPUnit stays green (a boolean is never `null`, so the test always passes and checks nothing), while PHPStan fails with the `phpstan-phpunit` extension. Without PHPStan, Infection would have caught it too. A green test does not prove that it tests anything.
