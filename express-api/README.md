# TP6 - API REST Node.js, Express et MySQL

# installation instructions
# Product REST API – LAB 6 & LAB 7

API REST développée avec **Node.js, Express.js et MySQL**.

Ce projet regroupe les fonctionnalités du LAB 6 (gestion des produits et catégories) et du LAB 7 (authentification JWT et sécurisation des routes).

## 1. Technologies utilisées

- Node.js
- Express.js
- MySQL / MariaDB
- mysql2
- JSON Web Token (JWT)
- bcrypt
- express-validator
- dotenv
- nodemon

## 2. Prérequis

Avant de commencer, installer :

- [Node.js](https://nodejs.org/)
- [Git](https://git-scm.com/)
- [XAMPP](https://www.apachefriends.org/) ou un serveur MySQL
- [Postman](https://www.postman.com/) pour tester les endpoints

Vérifier les installations :

```bash
node --version
npm --version
git --version
```

## 3. Cloner le projet

```bash
git clone https://github.com/YASSBELAB/tp6.git
```

Accéder au dossier du projet :

```bash
cd tp6/node-product-api
```

## 4. Installer les dépendances

```bash
npm install
```

Si nodemon n'est pas installé :

```bash
npm install --save-dev nodemon
```

## 5. Configurer la base de données MySQL

1. Démarrer Apache et MySQL depuis XAMPP.
2. Ouvrir phpMyAdmin : http://localhost/phpmyadmin
3. Sélectionner l'onglet SQL.
4. Exécuter les scripts SQL du dossier `sql/` :

- `create_database.sql`
- `seed_data.sql`

La base de données utilisée est `product_api`.

## 6. Configurer les variables d'environnement

Créer un fichier `.env` à la racine du projet :

```env
PORT=3000

DB_HOST=localhost
DB_PORT=3306
DB_USER=lab_user
DB_PASSWORD=your_database_password
DB_NAME=product_api

JWT_SECRET=your_access_token_secret
JWT_REFRESH_SECRET=your_refresh_token_secret

JWT_EXPIRES_IN=1h
JWT_REFRESH_EXPIRES_IN=7d

CORS_ORIGIN=http://localhost:5173
```

Remplacer les valeurs par les paramètres de votre environnement.

Les secrets JWT doivent être différents et suffisamment longs.

**Important :** Ne jamais partager ou pousser le fichier `.env` sur GitHub.

## 7. Lancer le serveur

Mode développement :

```bash
npm run dev
```

Si le script `dev` n'existe pas dans `package.json` :

```bash
npx nodemon src/server.js
```

Le serveur sera accessible à :

http://localhost:3000

## 8. Authentification et sécurité

Les principales routes d'authentification sont :

| Méthode | Endpoint | Description |
|---|---|---|
| POST | `/api/auth/register` | Inscription |
| POST | `/api/auth/login` | Connexion |
| POST | `/api/auth/refresh` | Renouvellement du token |
| GET | `/api/auth/me` | Consulter son profil |
| DELETE | `/api/auth/me` | Supprimer son compte |

L'API utilise :

- bcrypt pour le hachage des mots de passe.
- JWT pour l'authentification.
- Refresh tokens pour renouveler les access tokens.
- Middleware `authenticate` pour protéger les routes.
- Middleware `authorize` pour gérer les rôles USER et ADMIN.
- Journalisation des tentatives de connexion échouées.

## 9. Tester l'API

Utiliser Postman avec l'adresse :

```text
http://localhost:3000
```

Pour les routes protégées, ajouter dans Authorization :

```text
Type: Bearer Token
Token: YOUR_ACCESS_TOKEN
```

## 10. Structure du projet

```text
node-product-api/
├── sql/
│   ├── create_database.sql
│   └── seed_data.sql
├── src/
│   ├── config/
│   │   └── db.js
│   ├── controllers/
│   ├── middlewares/
│   ├── repositories/
│   ├── routes/
│   ├── services/
│   ├── utils/
│   └── server.js
├── .env
├── .gitignore
├── package.json
├── package-lock.json
└── README.md
```

## 11. Dépôt GitHub

Repository : https://github.com/YASSBELAB/tp6

Pour envoyer les modifications :

```bash
git add .
git commit -m "Update project documentation"
git push origin main
```

Migration de l'API REST TP5 vers une base de données MySQL avec une architecture en couches.


GET /api/products/search : Permet de rechercher et de récupérer la liste de tous les produits disponibles.

<img width="681" height="631" alt="searchallproducts" src="https://github.com/user-attachments/assets/4ae42d9d-87ce-48be-addb-1b65ce70338a" />

GET /api/products/search?minPrice={valeur} : Permet de filtrer les résultats pour récupérer uniquement les produits dont le prix est supérieur ou égal au montant minimum spécifié.


<img width="824" height="626" alt="filterby minum price" src="https://github.com/user-attachments/assets/bd7bd56d-f7df-4293-bfa8-7da2a584a004" />


GET /api/products/search?maxPrice={valeur} : Permet de filtrer les résultats pour récupérer uniquement les produits dont le prix est inférieur ou égal au montant maximum spécifié.

<img width="822" height="648" alt="searchbymaxprice" src="https://github.com/user-attachments/assets/b5881fc4-236a-46f6-873f-e1758aff4eea" />

GET /api/products/search?minPrice={valeur}&maxPrice={valeur} : Permet de filtrer les résultats pour récupérer uniquement les produits dont le prix est compris entre les montants minimum et maximum spécifiés.

<img width="862" height="639" alt="filterbyrange" src="https://github.com/user-attachments/assets/143e9be4-9237-47db-8d0f-294763eb03cb" />


GET /api/products/search?categoryId={id} : Permet de filtrer les résultats pour récupérer uniquement les produits appartenant à la catégorie spécifiée par son identifiant.


<img width="733" height="620" alt="filterbycatego" src="https://github.com/user-attachments/assets/c7ed652a-804c-43ee-8e81-af114acf137b" />

GET /api/products/search?limit={valeur}&offset={valeur} : Permet de paginer les résultats en spécifiant le nombre de produits à retourner (limit) et le point de départ (offset)

<img width="819" height="644" alt="pagination" src="https://github.com/user-attachments/assets/e8e3fc48-de9e-4c56-a4f9-e5ecf51b1c52" />


GET /api/products/search?limit={valeur}&offset={valeur} : Permet de récupérer une page de résultats spécifique, en sautant les premiers résultats (ici offset=2) et en limitant le nombre d'éléments retournés (ici limit=2).


<img width="750" height="625" alt="skipfierst 2" src="https://github.com/user-attachments/assets/ca22e049-07cb-4342-9ae8-937ff9bc01ee" />


GET /api/products/search?sort=name : Permet de trier la liste des résultats par ordre alphabétique selon le nom des produits.


<img width="763" height="641" alt="sortbyalphabetname" src="https://github.com/user-attachments/assets/7f2b34d9-f224-43a8-aba4-feab173f63bc" />

GET /api/products/search?sort=price : Permet de trier la liste des résultats par ordre croissant en fonction du prix des produits.

<img width="752" height="619" alt="sortbyprice" src="https://github.com/user-attachments/assets/e3896016-3dfe-4f24-8c92-e205c95b80f1" />



GET /api/products/search?name={valeur}&minPrice={valeur}&maxPrice={valeur}&categoryId={valeur}&limit={valeur}&offset={valeur}&sort={valeur} : Permet de combiner plusieurs critères pour effectuer une recherche avancée incluant des filtres (nom, prix, catégorie), de la pagination et du tri dans une seule et même requête.



<img width="824" height="633" alt="combined filter" src="https://github.com/user-attachments/assets/b4b1c13e-13c5-4db1-8446-e993b7a02d80" />



gET /api/products/search?name={valeur} : Permet de rechercher des produits par leur nom, en renvoyant ceux dont le nom correspond ou contient la chaîne de caractères spécifiée.

<img width="777" height="608" alt="searchbyname" src="https://github.com/user-attachments/assets/fd4c6fc2-96d5-4b9e-99bb-d9e0f9ac84e1" />

POST /api/products/with-category : Permet de créer simultanément un nouveau produit et sa catégorie associée en spécifiant le nom de la catégorie dans le corps de la requête.

<img width="759" height="643" alt="createwithcateg" src="https://github.com/user-attachments/assets/51f4b0fa-2780-45c9-8e58-13062320b39c" />



POST /api/products : Permet de créer un nouveau produit en fournissant ses informations et l'identifiant d'une catégorie existante.   

<img width="696" height="595" alt="createproduct" src="https://github.com/user-attachments/assets/b6009134-4d57-4aa9-9e8e-ab615b6ab1fd" />


PUT /api/products/{id} : Permet de modifier les informations d'un produit existant en spécifiant son identifiant dans l'URL et les nouvelles données dans le corps de la requête.

<img width="780" height="558" alt="modifierproduct" src="https://github.com/user-attachments/assets/a600c15e-59ed-43b8-a3bc-7d40f41e608e" />


DELETE /api/products/{id} : Permet de supprimer un produit spécifique en utilisant son identifiant dans l'URL (renvoie un statut 204 No Content en cas de succès).

<img width="696" height="536" alt="deleteprodct" src="https://github.com/user-attachments/assets/dd6f297f-99b6-430c-ad02-bcc807c7acf7" />

GET /api/products/{id} (Produit introuvable) : Renvoie une erreur 404 Not Found accompagnée d'un message clair si l'identifiant fourni ne correspond à aucun produit existant dans la base de données.

<img width="718" height="561" alt="productnotfound" src="https://github.com/user-attachments/assets/b3d193a0-353f-4104-8306-6ebd3cdae39d" />





POST /api/products (Erreur de validation) : Renvoie une erreur 400 Bad Request contenant le détail des champs invalides si les données fournies ne respectent pas les règles de validation, comme par exemple un nom de produit trop court.

<img width="860" height="625" alt="badrequestpost" src="https://github.com/user-attachments/assets/f06d999b-e7c5-4ba8-8666-497b83a261ed" />

POST /api/auth/register : Permet de créer un nouveau compte utilisateur en fournissant un nom d'utilisateur, une adresse e-mail et un mot de passe (renvoie un statut 201 Created en cas de succès).   

<img width="804" height="635" alt="register" src="https://github.com/user-attachments/assets/155baf7c-5423-4b43-8f4a-4f2832f5072d" />


POST /api/auth/login : Permet à un utilisateur de s'authentifier avec son e-mail et son mot de passe. En cas de succès, l'endpoint renvoie un token d'authentification (JWT) et le rôle de l'utilisateur

<img width="695" height="581" alt="loginavectoken" src="https://github.com/user-attachments/assets/bd5514e7-34a0-4801-9e58-3911e5fd6adc" />

GET /api/auth/me : Permet de récupérer les informations du profil de l'utilisateur actuellement authentifié grâce à son token d'accès (Bearer Token).

<img width="725" height="637" alt="Screenshot 2026-11-10 194133" src="https://github.com/user-attachments/assets/002ef3f9-f5fa-4bc3-a499-37e7d232619a" />

POST /api/products (Permissions insuffisantes) : Renvoie une erreur 403 Forbidden si un utilisateur authentifié tente de créer un produit sans posséder les droits d'administration requis (rôle "ADMIN")


<img width="762" height="595" alt="normalusercantaddproductevenwithtoken" src="https://github.com/user-attachments/assets/66792ed6-29e3-410d-81c7-4390a6b12cb7" />



POST /api/products (Création avec rôle Admin) : Démontre la création réussie d'un produit (statut 201 Created) lorsqu'une requête POST est effectuée avec un token d'authentification valide appartenant à un utilisateur disposant des privilèges d'administrateur.

<img width="726" height="636" alt="admin can create" src="https://github.com/user-attachments/assets/b97b87cb-50a9-4079-bb7c-bd2d1a61b755" />

POST /api/auth/login (via PowerShell) : Illustre la démarche pour s'authentifier et récupérer un token d'accès (JWT) en ligne de commande à l'aide de la commande Invoke-RestMethod


<img width="1267" height="485" alt="jetonloginpowershell" src="https://github.com/user-attachments/assets/93684ba9-bda5-4dfa-b987-51beb04f583b" />


GET /api/auth/me (via PowerShell) : Illustre l'utilisation d'un token d'accès (Bearer Token) en ligne de commande pour récupérer le profil de l'utilisateur authentifié et vérifier ses droits, comme ici le rôle "ADMIN".

<img width="1248" height="352" alt="addporducttokenviashell" src="https://github.com/user-attachments/assets/d5e72fc4-c029-48a2-b690-92c6dcee039d" />


Diagramme de séquence de l'authentification JWT : Ce diagramme illustre le flux de l'authentification "sans état" (stateless), détaillant les interactions entre le client, le serveur Express et la base de données MySQL lors de la connexion (génération du token) et lors des requêtes protégées (vérification du token).


<img width="349" height="481" alt="uml" src="https://github.com/user-attachments/assets/eb3ed183-87df-4358-88c2-790390801543" />



POST /api/auth/login (avec Refresh Token) : Permet à un utilisateur de s'authentifier et de récupérer à la fois un token d'accès principal, un token de rafraîchissement (refreshToken) pour prolonger la session, et son rôle.

<img width="790" height="585" alt="2token" src="https://github.com/user-attachments/assets/89b7e62d-499c-4439-a292-27cce369d293" />


POST /api/auth/refresh : Permet de générer un nouveau token d'accès (JWT) en fournissant un refreshToken valide dans le corps de la requête, sans avoir à s'authentifier de nouveau avec ses identifiants.

<img width="831" height="625" alt="refresh" src="https://github.com/user-attachments/assets/f108c042-6772-4c57-9a9a-cf2013c591e7" />


GET /api/auth/me (après rafraîchissement) : Démontre qu'une requête effectuée avec le nouveau token d'accès (obtenu via le Refresh Token) permet d'accéder aux informations du profil, validant ainsi le bon fonctionnement du mécanisme de renouvellement de session.   

<img width="732" height="631" alt="refreshtoken working" src="https://github.com/user-attachments/assets/1938d2ac-5d38-4055-a7f3-d78874238ec5" />


DELETE /api/auth/me : Permet à un utilisateur authentifié de supprimer son propre compte (renvoie un message de confirmation en cas de succès).


<img width="784" height="615" alt="deleteacc" src="https://github.com/user-attachments/assets/a55750a7-2329-4e73-9119-5d596549ffc0" />



POST /api/auth/login (Identifiants invalides) : Renvoie une erreur 401 Unauthorized accompagnée du message "Invalid credentials" lorsqu'une tentative de connexion échoue en raison d'un mot de passe ou d'une adresse e-mail incorrect.   


<img width="747" height="598" alt="logininvalidpass" src="https://github.com/user-attachments/assets/08546f9e-887f-47b9-8d0f-288eb245f601" />


Journalisation des tentatives de connexion (Base de données) : Montre que le système enregistre les historiques de connexion dans la base de données, en stockant des informations de sécurité telles que l'adresse e-mail, l'adresse IP de l'utilisateur (ici ::1 pour localhost), l'horodatage de la tentative (attempted_at) et son statut final (ici "FAILED" suite à une erreur d'authentification).


<img width="723" height="213" alt="logingfailed in database" src="https://github.com/user-attachments/assets/704b4674-eb6f-4404-8761-500b79417406" />




