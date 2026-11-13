# tp3
## Instructions d'installation, de compilation et d'exécution

## 1. Prérequis

- Java JDK 27
- Maven
- XAMPP (MariaDB)
- Postman

## 2. Configuration de la base de données

Démarrer MySQL depuis XAMPP, puis créer la base de données dans phpMyAdmin :

```sql
CREATE DATABASE lab3_db;
```

Vérifier la configuration dans `src/main/resources/application.properties` :

```properties
spring.datasource.url=jdbc:mysql://localhost:3306/lab3_db
spring.datasource.username=root
spring.datasource.password=

spring.jpa.hibernate.ddl-auto=update
```

## 3. Compilation du projet (Build)

Ouvrir un terminal à la racine du projet et exécuter :

```powershell
.\mvnw.cmd clean
.\mvnw.cmd install
```

Ou avec Maven installé :

```bash
mvn clean install
```

## 4. Exécution du projet (Run)

Pour démarrer l'application :

```powershell
.\mvnw.cmd spring-boot:run
```

Ou :

```bash
mvn spring-boot:run
```

L'application sera accessible à l'adresse :

```text
http://localhost:8080
```

## 5. Exécution des tests (Tests)

Pour exécuter tous les tests :

```powershell
.\mvnw.cmd test
```

Ou :

```bash
mvn test
```

Pour exécuter les tests et générer le rapport :

```powershell
.\mvnw.cmd clean verify
```

Les rapports de tests sont générés dans :

```text
target/surefire-reports/
```

## 6. Tests avec Postman

Importer la collection :

```text
postman/Lab4-Auth.postman_collection.json
```

Tester les fonctionnalités suivantes :

- Inscription d'un utilisateur (`POST /api/auth/register`).
- Connexion et génération du JWT (`POST /api/auth/login`).
- Accès aux routes protégées avec un token valide.
- Refus d'accès sans token ou avec un token invalide.
- Vérification des autorisations USER, MANAGER et ADMIN.
- Rafraîchissement du JWT (`POST /api/auth/refresh`).
- Vérification des erreurs HTTP (400, 401, 403 et 409).

**Fin de la procédure.**



## Tests des endpoints avec Postman et cURL
« Test de l'endpoint POST /api/categories sous Postman, validant l'ajout d'une catégorie (201 Created). »
<img width="569" height="395" alt="14tp3screenpost categ" src="https://github.com/user-attachments/assets/b7193135-d094-4590-b7da-bbba1484f87c" />

« Test de l'endpoint POST /api/products sous Postman, validant l'ajout d'un produit avec le statut 201 Created. »
<img width="579" height="458" alt="posttp3postproductpage14" src="https://github.com/user-attachments/assets/63382a4f-cc33-4d8d-95fd-1a51068c3b7a" />

« Test de l'endpoint GET /api/products/1 sous Postman, validant la récupération d'un produit par son ID (200 OK). »
<img width="522" height="400" alt="getpage14 apiproduct byid" src="https://github.com/user-attachments/assets/644b5e14-b74e-4f44-8c9c-32412f551683" />

« Test de l'endpoint DELETE /api/products/1 sous Postman, validant la suppression d'un produit avec le statut 204 No Content. »
<img width="554" height="374" alt="deletebyidpage14" src="https://github.com/user-attachments/assets/af076c26-1a03-4aad-bed7-85c600937347" />

« Test de l'endpoint POST /api/products via PowerShell (Invoke-RestMethod), validant l'ajout d'un produit avec succès. »
<img width="584" height="224" alt="postpowersghell" src="https://github.com/user-attachments/assets/9e754a78-bca2-479b-8369-e079ca7ab719" />

« Vérification de l'enregistrement effectif du produit dans la base de données via une requête SQL SELECT * FROM products; (phpMyAdmin). »

<img width="542" height="368" alt="verificationdatabase" src="https://github.com/user-attachments/assets/b7076fe6-9576-4035-9ccc-c86f5c14047b" />

« Test de l'endpoint GET /api/products/2 sous Postman, validant la récupération réussie du produit créé (200 OK). »

<img width="589" height="428" alt="lastcheck" src="https://github.com/user-attachments/assets/a5139084-a110-49c9-80f9-83be868f9a23" />

« Test de l'endpoint POST /api/auth/register sous Postman, validant l'inscription d'un nouvel utilisateur avec succès (201 Created). »
<img width="568" height="389" alt="inscript appitp4" src="https://github.com/user-attachments/assets/3b2ead2a-c164-4b04-be21-756a425c001a" />

« Vérification de l'enregistrement de l'utilisateur dans la base de données via une requête SQL SELECT * FROM users; sur phpMyAdmin (rôle USER, mot de passe haché). »

<img width="880" height="206" alt="databaseusertp4" src="https://github.com/user-attachments/assets/510bb139-91a7-4236-9e0f-58c8bfe8b478" />

« Test du cas d'erreur de l'endpoint POST /api/auth/register sous Postman, validant la gestion des doublons avec le statut 409 Conflict (« Username already exists »). »

<img width="577" height="424" alt="useraleready existtp4" src="https://github.com/user-attachments/assets/ecb33952-6f38-4f16-8bb9-1babaac521de" />

« Test de l'endpoint POST /api/auth/login sous Postman, validant l'authentification réussie d'un utilisateur et la génération du jeton JWT (200 OK). »

<img width="538" height="423" alt="loginok" src="https://github.com/user-attachments/assets/83733503-790e-4bc4-bc5c-ba7e92d99d6b" />

« Test de l'endpoint GET /api/products avec authentification sécurisée par jeton Bearer (JWT) sous Postman, validant la récupération de la liste des produits avec le statut 200 OK. »   


<img width="538" height="443" alt="testtoken" src="https://github.com/user-attachments/assets/bc0697ff-3b34-4835-974a-8cb96a2188da" />

« Test du cas d'accès refusé de l'endpoint POST /api/products sous Postman, validant la restriction des rôles avec le statut 403 Forbidden pour un utilisateur non autorisé normal user. »

<img width="549" height="370" alt="userforbiddentocreateproduct" src="https://github.com/user-attachments/assets/6a96b435-cf4e-4145-8834-f0377c96d5e0" />

<img width="584" height="446" alt="loginasadminafterupdate userinmysqlasadmin" src="https://github.com/user-attachments/assets/995e2fbf-2597-4a87-93c6-d87b17115442" />

« Test de l'endpoint POST /api/products avec un token JWT administrateur sous Postman, validant la création réussie d'un nouveau produit (« Gaming Laptop ») avec le statut 201 Created. »

<img width="575" height="441" alt="createasadminjeton" src="https://github.com/user-attachments/assets/0f49be15-c21a-452d-9691-4ec7d8e2e4d6" />

« Test du cas d'accès non authentifié de l'endpoint POST /api/products sans jeton, validant le rejet de la requête avec le statut 401 Unauthorized. »

<img width="560" height="412" alt="notoken401" src="https://github.com/user-attachments/assets/b3ebc87a-63d3-4f2e-9cdb-d51343cc33c6" />

« Test de l'endpoint PUT /api/products/2 sous Postman . validant le rejet de la modification avec le statut 401 Unauthorized par normal user. »

<img width="554" height="412" alt="normal usermodif" src="https://github.com/user-attachments/assets/1c4265fe-78e2-4410-b5e2-a16f4a031b1a" />

« Test de l'endpoint DELETE /api/products/2 sous Postman , validant le rejet de la suppression avec le statut 401 Unauthorized par normal user. »

<img width="514" height="407" alt="normaluserdelete" src="https://github.com/user-attachments/assets/7c022a90-ead3-46ec-afb3-b428173a1d83" />


« Test de l'endpoint PUT /api/products/2 sous Postman avec un jeton JWT administrateur, validant la modification réussie d'un produit (200 OK). »


<img width="549" height="456" alt="adminmodif" src="https://github.com/user-attachments/assets/ab902bfb-b01a-4ba3-a503-f69a80664222" />


« Test de l'endpoint DELETE /api/products/2 sous Postman avec un jeton JWT administrateur, validant la suppression réussie d'un produit avec le statut 204 No Content. »

<img width="525" height="425" alt="deleteadmin" src="https://github.com/user-attachments/assets/8743e49f-0ca5-4e9d-bf5b-b4df92428a2a" />

« Test de l'endpoint DELETE /api/categories/2 sous Postman, validant la suppression réussie d'une catégorie avec le statut 204 No Content. »

<img width="559" height="479" alt="dmindeletecategorie" src="https://github.com/user-attachments/assets/97a81558-9aee-44c3-aad3-eed55bf492d3" />


« Test de l'endpoint PUT /api/categories/1 sous Postman, validant la mise à jour réussie d'une catégorie avec le statut 200 OK. »

<img width="568" height="422" alt="adminupdatevcat" src="https://github.com/user-attachments/assets/7f3ab276-c902-4daa-a87f-6767e198835f" />


« Test de l'endpoint DELETE /api/categories/1 sous Postman avec un jeton d'utilisateur normal usere, validant le rejet de la suppression avec le statut 401 Unauthorized. »

<img width="532" height="386" alt="normaluserdeletecat" src="https://github.com/user-attachments/assets/840056b2-6dbe-4ca0-a4f3-d0b0be0b6859" />

« Test de l'endpoint PUT /api/categories/1 sous Postman par normal user, validant le rejet de la modification avec le statut 401 Unauthorized. »


<img width="564" height="437" alt="normaluserupdatecat" src="https://github.com/user-attachments/assets/b0380890-0bc6-4672-97b1-bdc925e90a0e" />

« Test de l'endpoint GET /api/orders sous Postman avec un jeton JWT administrateur, validant la récupération réussie de la liste des commandes avec le statut 200 OK. »

<img width="572" height="497" alt="adminseeorders" src="https://github.com/user-attachments/assets/f8c11516-cf6e-4fdd-a3a3-f98e8276a8ab" />

« Test de l'endpoint GET /api/orders/1 sous Postman avec un jeton JWT administrateur, validant la récupération réussie des détails d'une commande spécifique avec le statut 200 OK.get order by id . »


<img width="604" height="469" alt="adminseeorderbyid" src="https://github.com/user-attachments/assets/14e814b5-3c15-49df-be39-627ebc6dc093" />

« Test de l'endpoint DELETE /api/orders/1 sous Postman avec un jeton JWT administrateur, validant la suppression réussie d'une commande avec le statut 204 No Content. »

<img width="562" height="434" alt="admindeleteorder" src="https://github.com/user-attachments/assets/15f3788c-844a-4f8e-bd8d-76fdcff631d2" />

« Test de l'endpoint POST /api/orders sous Postmanadmin creates order, validant le rejet de la création d'une commande avec le statut 401 Unauthorized. »

<img width="554" height="424" alt="adminforbiddencreateorder" src="https://github.com/user-attachments/assets/32e212ae-205b-4393-8704-c07d17205194" />

« Test de l'endpoint GET /api/orders sous Postman avec un jeton d'utilisateur standard (USER), validant la récupération réussie de la liste des commandes propres à l'utilisateur avec le statut 200 OK. »

<img width="532" height="487" alt="userseehisorders" src="https://github.com/user-attachments/assets/e2ecb63f-9b07-410d-af5f-7d0cda7ed533" />

« Test de l'endpoint GET /api/orders/1 sous Postman normal user cant see another customer order, validant le rejet de la requête avec le statut 401 Unauthorized. »

<img width="532" height="418" alt="usercantseeanorderofanother user" src="https://github.com/user-attachments/assets/a5f4df80-65c8-42fb-b1b1-f016eae94fbb" />

« Test de l'endpoint GET /api/orders/2 sous Postman avec un jeton d'utilisateur standard (USER), validant la récupération réussie des détails d'une commande autorisée avec le statut 200 OK,see one command by id . »

<img width="541" height="467" alt="userseeorderbyid" src="https://github.com/user-attachments/assets/86c06c65-351b-485b-8a9a-9d0e55d7eefb" />

« reconfig of 401 and 403 Test de l'endpoint GET /api/orders/2 sous Postman avec un jeton d'utilisateur standard essayant d'accéder à la commande d'un autre utilisateur, validant le refus d'accès avec le statut 403 Forbidden et le message Access denied: insufficient permissions. »

<img width="539" height="414" alt="403whentryto seeaanotheruserorder" src="https://github.com/user-attachments/assets/875b38d7-d886-4c64-b656-fe57a3bf10da" />

« Test de l'endpoint GET /api/orders/5 sous Postman avec un jeton d'authentification invalide, confirmant le rejet de la requête avec le code de statut 401 Unauthorized. »

<img width="561" height="446" alt="401invalidtoken" src="https://github.com/user-attachments/assets/254d770b-e654-4647-ad5e-bbaf1f748fad" />

« Test de l'endpoint POST /api/products sous Postman avec un jeton d'authentification administrateur valide, validant la création réussie d'un nouveau produit (admintest Product) avec le code de statut 201 Created. »

<img width="563" height="468" alt="admintest401403 try postproductwithtoken" src="https://github.com/user-attachments/assets/2f7507f5-2fef-4128-a0c3-9bfa1a52f503" />

« Test de l'endpoint POST /api/products sous Postman avec un jeton JWT invalide admin , validant le rejet de la requête avec le statut 401 Unauthorized. »

<img width="524" height="413" alt="admininvalidtoken" src="https://github.com/user-attachments/assets/4b665640-61b6-4946-880d-c2e67f8d70fc" />


« Test de l'endpoint POST /api/products sous Postman avec un jeton d'utilisateur standard (USER), validant le refus d'accès aux actions administrateur avec le statut 403 Forbidden et le message Access denied: insufficient permissions. »   

<img width="557" height="428" alt="userpostdemnied403product" src="https://github.com/user-attachments/assets/de0ae1ce-b9db-4b75-a7b1-0ab002b844fd" />


« Diagramme de séquence UML représentant le processus d'authentification par JWT (Login Sequence), détaillant les interactions entre le Client, l'AuthController, l'AuthenticationManager, le UserDetailsService et le JwtUtil pour la génération du jeton d'accès (statut 200 OK). »   

<img width="575" height="364" alt="uml1" src="https://github.com/user-attachments/assets/2d2a14fa-1bf6-4aed-a9bd-3876fd2d8737" />


« Diagramme de séquence UML représentant la chaîne de filtres de sécurité Spring Security (JWT Filter Chain), détaillant le traitement d'une requête HTTP par le JwtAuthenticationFilter, la validation du token via JwtUtil, la gestion du contexte via le SecurityContextHolder et l'acheminement vers le DispatcherServlet et le Controller. »

<img width="562" height="345" alt="uml2" src="https://github.com/user-attachments/assets/c2989a8b-c9b2-4fff-85a0-aa1a6cec39f5" />

« Test de l'endpoint POST /api/products sous Postman avec un jeton d'authentification gestionnaire, validant la création réussie d'un nouveau produit (« Manager Test Product ») avec le code de statut 201 Created. »

<img width="551" height="440" alt="managercreateproduit" src="https://github.com/user-attachments/assets/417b4606-aa6b-4a71-a60b-b6f5fe9629cd" />

« Test de l'endpoint GET /api/orders sous Postman avec un jeton d'authentification  gestionnaire, validant la récupération réussie de la liste complète des commandes de la plateforme avec le code de statut 200 OK. »

<img width="577" height="472" alt="managerreadcorders" src="https://github.com/user-attachments/assets/a4bba334-9113-494d-b1c1-6a3599b1e5e2" />

« Test de l'endpoint DELETE /api/products/3 sous Postman avec un jeton de rôle gestionnaire (Manager), validant le refus d'accès pour les actions de suppression strictes par le statut 403 Forbidden et le message Access denied: insufficient permissions. »

<img width="660" height="446" alt="managercantdeleteproduct" src="https://github.com/user-attachments/assets/f040d661-9f36-4675-b287-932ecd5e4ff8" />


« Test de l'endpoint POST /api/auth/refresh sous Postman avec un jeton d'authentification valide, validant le renouvellement réussi du JWT avec le code de statut 200 OK et la génération d'un nouveau jeton d'accès au format JSON. »

<img width="587" height="440" alt="rafraichirtoken" src="https://github.com/user-attachments/assets/db61965f-7d73-4333-97c5-0488fa8be93b" />





















## Exercice 3 – Gestion de la révocation des JWT

### 1. Problématique

Un JWT est un token autonome : une fois généré, il reste valide jusqu'à son expiration, même si l'utilisateur souhaite se déconnecter ou si le token est compromis.

Pour réduire ce risque, plusieurs stratégies peuvent être utilisées.

### 2. Stratégies proposées

#### A. Liste noire en base de données (Blacklist)

- Lorsqu'un utilisateur se déconnecte ou signale un token compromis, son identifiant unique (`jti`) est ajouté à une table `revoked_tokens`.
- À chaque requête, le filtre JWT vérifie si le token figure dans cette liste.
- Si le token est révoqué, l'accès est refusé avec une réponse HTTP 401.
- Les entrées peuvent être supprimées automatiquement après l'expiration des tokens.

**Avantage :** permet de révoquer immédiatement un token précis.

**Inconvénient :** nécessite une vérification en base de données à chaque authentification.

#### B. Durée de vie courte

- Réduire la durée de validité des access tokens (par exemple, 10 à 15 minutes).
- Utiliser un mécanisme de rafraîchissement pour obtenir un nouveau token.
- En cas de vol, le token compromis ne sera utilisable que pendant une courte période.

**Avantage :** limite les conséquences d'un vol de token.

**Inconvénient :** ne permet pas de révoquer immédiatement un token encore valide.

#### C. Rotation du secret JWT

- Si la clé secrète est compromise, générer une nouvelle clé sécurisée.
- Les tokens signés avec l'ancienne clé deviennent invalides.
- Tous les utilisateurs doivent alors se reconnecter pour obtenir de nouveaux tokens.

**Avantage :** permet d'invalider tous les tokens signés avec l'ancienne clé.

**Inconvénient :** oblige tous les utilisateurs à se reconnecter.

### 3. Solution recommandée

Pour cette application Spring Boot, une combinaison de ces stratégies est recommandée :

1. Utiliser des access tokens de courte durée.
2. Mettre en place une liste noire pour révoquer individuellement les tokens.
3. Effectuer une rotation du secret en cas de compromission de la clé.

### 4. Conclusion

La combinaison de ces mécanismes améliore la sécurité de l'authentification JWT. Elle permet de limiter la durée d'utilisation d'un token volé, de révoquer un token individuellement et d'invalider tous les tokens en cas de compromission du secret.

