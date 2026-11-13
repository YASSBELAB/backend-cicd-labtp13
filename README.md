# Lab 13 : Backend CI/CD & DevSecOps Pipeline

Ce dépôt implémente une chaîne complète d'**Intégration Continue (CI)**, de **Livraison Continue (CD)** et de **DevSecOps** pour trois architectures d'API backend :
- **Spring Boot 4.x (Java 27 / Maven)** : `spring-api/`
- **Express.js (Node.js 20 / npm)** : `express-api/`
- **Laravel 11+ (PHP 8.2 / Composer / MySQL)** : `laravel-api/`

---

## 1. Architecture Générale des Pipelines

```
                     ┌────────────────┐
                     │ Push sur main  │
                     └───────┬────────┘
                             │
     ┌───────────────────────┼───────────────────────┐
     ▼                       ▼                       ▼
┌───────────────┐     ┌───────────────┐     ┌───────────────┐
│ Spring Boot CI│     │ Express.js CI │     │  Laravel CI   │
│ (spring-ci)   │     │ (express-ci)  │     │ (laravel-ci)  │
│  - Maven      │     │  - ESLint     │     │  - MySQL srv  │
│  - JUnit XML  │     │  - Mocha/Jest │     │  - PHPUnit    │
└───────┬───────┘     └───────┬───────┘     └───────┬───────┘
        │                     │                     │
        └─────────────────────┼─────────────────────┘
                              │ Succès (workflow_run)
                              ▼
                   ┌─────────────────────┐
                   │    Docker Build     │
                   │ (docker-build.yml)  │
                   │  - Buildx           │
                   │  - Push vers GHCR   │
                   └──────────┬──────────┘
                              │ Succès (workflow_run)
                              ▼
                   ┌─────────────────────┐
                   │       CD Demo       │
                   │    (cd-demo.yml)    │
                   │  - Déploiement Demo │
                   │  - Tests de fumée   │
                   └─────────────────────┘
```

---

## 2. Détail des Workflows GitHub Actions

### 2.1. Spring Boot CI (`.github/workflows/spring-ci.yml`)
- **Environnement** : Ubuntu Latest, JDK 27 (Eclipse Temurin).
- **Mise en cache** : Cache Maven sur `~/.m2` via `actions/cache@v4` indexé sur `pom.xml`.
- **Compilation** : `mvn -B package -DskipTests --file spring-api/pom.xml` en mode batch (`-B`).
- **Tests Unitaires & Rapports** : Exécution de `mvn -B test --file spring-api/pom.xml`.
- **Archivage des Artefacts** : Les rapports JUnit XML de Surefire (`spring-api/target/surefire-reports/`) sont archivés avec `if: always()` sous le nom `spring-test-results`.

### 2.2. Express.js CI (`.github/workflows/express-ci.yml`)
- **Environnement** : Node.js 20 LTS.
- **Mise en cache** : Cache npm automatique via `setup-node` indexé sur `express-api/package-lock.json`.
- **Dépendances** : Installation propre et déterministe via `npm ci --prefix express-api`.
- **Analyse Statique (DevSecOps)** : `npm run lint --prefix express-api` avec `continue-on-error: false` pour stopper le pipeline en cas de non-respect des règles de style.
- **Tests** : Exécution de la suite de tests via `npm test --prefix express-api`.

### 2.3. Laravel CI (`.github/workflows/laravel-ci.yml`)
- **Environnement** : PHP 8.2 avec couverture de code (`xdebug`).
- **Service Conteneurisé MySQL** : Démarrage d'un conteneur `mysql:8.0` dédié avec health-check (`mysqladmin ping`).
- **Gestion des Variables d'Environnement** : Injection dynamique au niveau du job (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
- **Mise en cache** : Cache Composer sur `laravel-api/vendor` indexé sur `composer.lock`.
- **Tests** : Exécution des tests automatisés avec `php artisan test`.

### 2.4. Construction et Publication Docker (`.github/workflows/docker-build.yml`)
- **Déclenchement Conditionnel** : Se déclenche automatiquement après la complétion réussie des 3 workflows CI précédents via `workflow_run`.
- **Docker Buildx** : Initialisation du builder multi-plateformes.
- **Publication sur GHCR** : Authentification auprès de GitHub Container Registry (`ghcr.io`) via `docker/login-action@v3` avec `secrets.GITHUB_TOKEN`.
- **Images générées** :
  - `ghcr.io/<owner>/spring-api:latest`
  - `ghcr.io/<owner>/express-api:latest`
  - `ghcr.io/<owner>/laravel-api:latest`

### 2.5. Livraison Continue vers la Démo (`.github/workflows/cd-demo.yml`)
- **Déclenchement** : Automatique après la publication des images Docker sur GHCR.
- **Tests de Fumée (Smoke Tests)** : Exécution de requêtes HTTP `curl -f` sur l'endpoint de santé (`/api/health`) pour valider la disponibilité immédiate du service après déploiement.

---

## 3. Bonnes Pratiques DevSecOps & Sécurité

1. **Principe du Moindre Privilège (`permissions`)** :
   - Tous les workflows CI et CD sont limités à `permissions: contents: read`.
   - Seul le workflow `docker-build.yml` possède le droit `packages: write` pour publier les conteneurs.
2. **Scan Automatisé des Dépendances (Dependabot)** :
   - Le fichier `.github/dependabot.yml` audite chaque semaine les vulnérabilités sur Maven, npm, Composer et GitHub Actions.
3. **Protection des Secrets** :
   - Aucun mot de passe ni clé en clair dans le code. Utilisation exclusive de variables d'environnement et de GitHub Secrets (`${{ secrets.* }}`).

---

## 4. Dockerfiles des Services

| Service | Image de base | Type de build | Port |
| :--- | :--- | :--- | :--- |
| `spring-api` | `eclipse-temurin:27-jre` | Runtime optimisé (Jar exécutable) | `8080` |
| `express-api` | `node:20-alpine` | Alpine léger avec `npm ci --only=production` | `3000` |
| `laravel-api` | `php:8.2-apache` | Apache + extensions `pdo_mysql` + Composer | `80` |
