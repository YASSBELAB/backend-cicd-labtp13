# Rapports de tests – TP13

Rapports générés localement le 13 novembre 2026 à partir des trois API du projet.

| Application | Framework | Résultat | Fichier |
|---|---|---:|---|
| Spring API | JUnit 5 via Maven Surefire | 7 tests réussis, 0 échec, 0 erreur | `spring-junit/` |
| Express API | Jest | 5 tests réussis, 0 échec | `jest/jest-results.json` |
| Laravel API | PHPUnit | 41 tests réussis, 126 assertions | `phpunit/phpunit.xml` |

## Fichiers inclus

- `spring-junit/TEST-com.example.lab3.controller.ProductControllerTest.xml`
- `spring-junit/TEST-com.example.lab3.Lab3JpaApplicationTests.xml`
- `jest/jest-results.json`
- `phpunit/phpunit.xml`

## Regénérer les rapports

Depuis la racine du dépôt :

```sh
mvn -B clean test --file spring-api/pom.xml
```

Pour Express, démarrer MySQL, configurer les variables de connexion, puis lancer depuis `express-api` :

```sh
node src/test-db-init.js
npm ci
npm test
```

Pour Laravel, installer Composer puis lancer depuis `laravel-api` :

```sh
composer install
vendor/bin/phpunit --log-junit reports/phpunit.xml
```

Le workflow Spring archive déjà ses rapports Surefire. Les workflows Express et Laravel ont été configurés pour téléverser leurs rapports en artefacts GitHub Actions.
