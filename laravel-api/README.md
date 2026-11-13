# tp8
TP8 – Développement d’une API RESTful avec Laravel (Gestion des Produits et Catégories)



# Instructions d'installation

## 1. Prérequis
- PHP 8.4 ou supérieur
- Composer
- MySQL
- Laravel
- Git

## 2. Cloner le projet

```bash
git clone https://github.com/YASSBELAB/tp8.git
cd tp8/lab8-api
```

## 3. Installer les dépendances

```bash
composer install
```

## 4. Configurer l'environnement

Créer le fichier `.env` :

```bash
cp .env.example .env
```

Configurer les informations de la base de données dans `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lab8
DB_USERNAME=root
DB_PASSWORD=
```

Créer la base de données `lab8` dans MySQL ou phpMyAdmin.

## 5. Générer la clé de l'application

```bash
php artisan key:generate
```

## 6. Exécuter les migrations et seeders

```bash
php artisan migrate --seed
```

## 7. Démarrer le serveur Laravel

```bash
php artisan serve
```

L'API sera accessible à l'adresse :

http://127.0.0.1:8000

## 8. Tester l'API

Endpoint principal :

```text
http://127.0.0.1:8000/api/products
```

Les opérations disponibles sont :

- GET : afficher les produits.
- POST : créer un produit.
- GET /{id} : afficher un produit.
- PUT /{id} : modifier un produit.
- DELETE /{id} : supprimer un produit.

Les filtres disponibles comprennent la recherche par nom, le prix minimum, le prix maximum, le tri et la pagination.

## 9. Tester avec Postman

Utiliser Postman pour envoyer des requêtes HTTP à l'API avec des données JSON.

**Repository GitHub :**  
https://github.com/YASSBELAB/tp8





Une requête Invoke-RestMethod réussie récupérant la liste des produits au format JSON depuis l'API locale.

<img width="1103" height="318" alt="invoke product" src="https://github.com/user-attachments/assets/1f32c680-4544-4bb9-99db-a9eb6f959ac1" />


Requête triant les produits par prix décroissant (page 1, tri price en mode desc), affichant le MacBook Air en tête.


<img width="1210" height="223" alt="paginationproicedesc" src="https://github.com/user-attachments/assets/2a17a927-8229-49e7-829b-063bcdc244ec" />


Requête affichant les détails d'un produit spécifique par son identifiant (/api/products/1), retournant le "Dell Laptop".


<img width="1118" height="237" alt="displaybyid" src="https://github.com/user-attachments/assets/7850f214-21eb-4a56-97c8-56a531a25ef4" />


Requête POST envoyée via l'API pour créer un nouveau produit ("New Laptop"), confirmant sa création avec succès et affichant ses détails (ID 21).


<img width="1166" height="402" alt="create product" src="https://github.com/user-attachments/assets/4c8661ce-a9b5-4a75-86f6-7d862332c475" />



Requête PUT envoyée via l'API pour mettre à jour le produit existant (ID 1), confirmant la modification avec succès ("Produit modifie") et affichant ses nouvelles données.

<img width="1085" height="307" alt="updateproduct" src="https://github.com/user-attachments/assets/2a9d59d2-2137-42d9-8a4a-585042d80cfc" />


Requête DELETE envoyée via l'API pour supprimer le produit (ID 1), affichant le message de confirmation "Product deleted successfully".

<img width="1141" height="173" alt="deleteproduct" src="https://github.com/user-attachments/assets/a8a299ed-79a0-482a-9f03-75b8dd38d7a9" />


equête de filtrage des produits par nom (/api/products?name=laptop), affichant les résultats correspondants comme le "HP Laptop"


<img width="1152" height="189" alt="filterproduct name" src="https://github.com/user-attachments/assets/63021f56-a746-4d14-855f-f839b98230f1" />


Requête de filtrage des produits par prix minimum (/api/products?price_min=100), affichant les produits dont le prix est supérieur ou égal à 100 comme le "HP Laptop".


<img width="1110" height="159" alt="Screenshot 2026-11-10 235545" src="https://github.com/user-attachments/assets/82db5cbb-86fe-40bb-ac2c-37a8a4997ef4" />



Requête de filtrage des produits par prix maximum (/api/products?price_max=500), affichant les produits dont le prix est inférieur ou égal à 500 comme la "Wireless Mouse".


<img width="1142" height="166" alt="Screenshot 2026-11-10 235643" src="https://github.com/user-attachments/assets/82e582c5-61c0-4dd4-9b71-c66112a2c52b" />


Requête de pagination pour afficher la deuxième page des produits (/api/products?page=2), affichant la liste correspondante à partir du produit "Sneakers".


<img width="1097" height="169" alt="Screenshot 2026-11-10 235710" src="https://github.com/user-attachments/assets/0cec72ba-0402-4940-81e4-f0efe621cbd1" />



Test de la requête GET sur l'API des produits via Postman, retournant une réponse HTTP 200 OK avec la liste paginée des produits au format JSON.


<img width="705" height="620" alt="Screenshot 2026-11-10 235955" src="https://github.com/user-attachments/assets/c2a19b9d-38b3-4917-9a7c-f4b67d78cde4" />


est d'une requête GET sur l'API via Postman avec des paramètres de recherche (page=1&sort=price&direction=desc), retournant une réponse 200 OK avec le tri des produits par prix décroissant (affichant le MacBook Air en premier).


<img width="811" height="684" alt="Screenshot 2026-11-11 000024" src="https://github.com/user-attachments/assets/39b9ba05-6743-46a9-8cf2-44125e33da86" />


Test d'une requête POST sur l'API via Postman pour créer un nouveau produit (données JSON envoyées dans le corps), retournant un statut 201 Created avec les détails du produit créé (ID 22).


<img width="915" height="625" alt="Screenshot 2026-11-11 000112" src="https://github.com/user-attachments/assets/0d3cb956-ba32-4ac3-a50c-2c2589eb170b" />


Test d'une requête GET sur l'API via Postman pour récupérer un produit spécifique (/api/products/3), retournant un statut 200 OK avec les détails du "MacBook Air".

<img width="709" height="643" alt="Screenshot 2026-11-11 000152" src="https://github.com/user-attachments/assets/026b8175-3dfd-4447-9634-79389029f1db" />


Test d'une requête PUT sur l'API via Postman pour mettre à jour un produit existant (/api/products/3), retournant un statut 200 OK avec les nouvelles données modifiées du produit ("Produit modifie")


<img width="790" height="673" alt="Screenshot 2026-11-11 000244" src="https://github.com/user-attachments/assets/7a34b522-8e77-4311-9bc5-96f536354665" />


Test d'une requête GET de filtrage par nom (name=laptop) sur l'API via Postman, retournant un statut 200 OK avec les résultats correspondants (comme le "HP Laptop").


<img width="698" height="623" alt="Screenshot 2026-11-11 000321" src="https://github.com/user-attachments/assets/b8dff851-f0ad-4ef6-8c70-eaf8083ae0fc" />


Test d'une requête GET de filtrage par prix minimum (price_min=100) sur l'API via Postman, retournant un statut 200 OK avec les résultats correspondants (comme le "HP Laptop")


<img width="711" height="655" alt="Screenshot 2026-11-11 000355" src="https://github.com/user-attachments/assets/926be6c5-3033-4948-a0ff-b373a49519e8" />




Test d'une requête GET de filtrage par prix maximum (price_max=500) sur l'API via Postman, retournant un statut 200 OK avec les résultats correspondants (comme le "Produit modifie")


<img width="742" height="681" alt="Screenshot 2026-11-11 000419" src="https://github.com/user-attachments/assets/ddb615de-f39f-4122-a314-3c63582704b0" />



Test d'une requête GET de filtrage combiné par prix minimum et maximum (price_min=100&price_max=500) sur l'API via Postman, retournant un statut 200 OK avec les résultats correspondants (comme la "Wireless Mouse" au prix de 150).


<img width="691" height="596" alt="Screenshot 2026-11-11 000502" src="https://github.com/user-attachments/assets/69df8490-a8b0-4e77-8629-0bbe1ca2ace4" />



Test d'une requête GET de pagination (page=2) sur l'API via Postman, retournant un statut 200 OK avec la liste paginée correspondante à partir du produit "Sneakers".


<img width="893" height="649" alt="Screenshot 2026-11-11 000534" src="https://github.com/user-attachments/assets/840e8ec8-0242-4efd-99bf-c5508c10984d" />




Test d'une requête DELETE sur l'API via Postman pour supprimer un produit spécifique (/api/products/13), retournant un statut 200 OK avec un message de confirmation de suppression ("Product deleted successfully").


<img width="767" height="513" alt="Screenshot 2026-11-11 000618" src="https://github.com/user-attachments/assets/affe5c1c-e031-4d85-9a9e-2a7e6a09e40b" />




Test d'une requête GET sur l'API des catégories (/api/categories) via Postman, retournant une réponse HTTP 200 OK avec la liste des catégories au format JSON, incluant les relations avec les produits associés (telle que la catégorie "Electronics" avec ses métadonnées et ses produits imbriqués)


<img width="717" height="617" alt="getallcat" src="https://github.com/user-attachments/assets/7067426c-59f6-4df1-a4b2-f7aa61ff24dd" />



Test d'une requête POST sur l'API via Postman pour créer une nouvelle catégorie (/api/categories) avec les données JSON envoyées dans le corps ({"name": "lolo"}), retournant un statut 201 Created ainsi qu'un message de confirmation et les détails de la catégorie créée (ID 4).

<img width="765" height="633" alt="postcat" src="https://github.com/user-attachments/assets/d34ad86a-2203-46b7-8c3d-e32fd6208747" />


Test d'une requête GET sur l'API pour récupérer une catégorie spécifique par son ID (/api/categories/1) via Postman, retournant un statut 200 OK avec les détails de la catégorie "Electronics" ainsi que ses produits associés

<img width="725" height="603" alt="getbyidcat" src="https://github.com/user-attachments/assets/de1ee92c-ced5-4c51-b0da-a58e047590d7" />



Test d'une requête PUT sur l'API via Postman pour mettre à jour une catégorie existante (/api/categories/1), avec les nouvelles données JSON envoyées dans le corps ({"name": "Computer Accessories"}), retournant un statut 200 OK ainsi qu'un message de confirmation ("Category updated successfully") et les détails actualisés de la catégorie.


<img width="726" height="629" alt="modifcat" src="https://github.com/user-attachments/assets/a143b833-d320-4b30-a033-f3234a299986" />



"Cet endpoint permet de supprimer une catégorie de la base de données (ex: Computer Accessories) en envoyant une requête DELETE avec l'ID correspondant."

<img width="703" height="532" alt="dletecat" src="https://github.com/user-attachments/assets/f1e7b303-ca7b-48d5-89b3-7e85f768dfd4" />


Cette requête permet de créer une nouvelle commande.


<img width="750" height="636" alt="createorder" src="https://github.com/user-attachments/assets/766e0412-bf1f-4152-92d4-dac4f391d70f" />


"Exemple de test Postman réussi avec message succes post script (statut 201) validant la création d'une commande via l'API."   


<img width="692" height="659" alt="avecmessagesuccess" src="https://github.com/user-attachments/assets/3c2fdec8-d492-4f84-9da5-ff7abd4aae37" />



"Exécution réussie des tests automatisés (via PHP Artisan) validant les relations du modèle Order.

<img width="573" height="185" alt="testpassed" src="https://github.com/user-attachments/assets/d3cec6bf-ba4c-458d-af33-9c3ca28f9db9" />


"Diagramme de classes UML illustrant l'architecture des données et les relations entre les modèles de l'API E-commerce."


<img width="433" height="532" alt="uml" src="https://github.com/user-attachments/assets/7dc778af-7295-4cec-a332-4033e2d53922" />

"Exemple de requête Postman rejetée (statut 401 Unauthorized) illustrant l'accès refusé à une route protégée sans authentification valide."

<img width="711" height="622" alt="Screenshot 2026-11-11 032625" src="https://github.com/user-attachments/assets/4779ed14-be42-4065-a6c1-f4938b61a0d8" />


Exemple de requête Postman réussie (statut 201) démontrant l'enregistrement d'un nouvel utilisateur et la génération d'un jeton d'accès (token)."

<img width="665" height="571" alt="Screenshot 2026-11-11 033421" src="https://github.com/user-attachments/assets/e26e7bd7-17b6-4448-813b-93abc73b999a" />



"Exemple de requête Postman réussie (statut 200 OK) démontrant l'authentification d'un utilisateur (login) et l'obtention d'un jeton d'accès (token)."



<img width="603" height="542" alt="login" src="https://github.com/user-attachments/assets/49db170e-e83d-420e-b601-e54b98e66e12" />


"Exemple de requête Postman réussie (statut 200 OK) illustrant l'accès aux informations de l'utilisateur connecté via une route protégée en utilisant un jeton Bearer (Bearer Token)."


<img width="713" height="632" alt="Screenshot 2026-11-11 034123" src="https://github.com/user-attachments/assets/e3f4e16c-ae4f-4095-98f9-0bf3004e9d90" />



"Exemple de requête Postman réussie (statut 200 OK) démontrant l'authentification d'un utilisateur (login) avec l'inclusion du paramètre 'device_name', et la récupération d'un jeton d'accès (token)."



<img width="696" height="569" alt="logindevice" src="https://github.com/user-attachments/assets/632f260d-a24f-40b9-9817-2e27c7fbe287" />



"Exemple de requête Postman réussie (statut 200 OK) illustrant la récupération des informations de l'utilisateur actuellement connecté via la route /api/auth/me en utilisant un jeton Bearer (Bearer Token)."


<img width="649" height="591" alt="Screenshot 2026-11-11 035219" src="https://github.com/user-attachments/assets/ada890bf-e0a7-42ea-a546-12c2c16de313" />


"Exemple de requête Postman réussie (statut 200 OK) illustrant la déconnexion d'un utilisateur (logout) via la route /api/auth/logout en utilisant un jeton Bearer."



<img width="624" height="569" alt="Screenshot 2026-11-11 040041" src="https://github.com/user-attachments/assets/7ed61763-5b30-4879-80b7-41c231401103" />



"Exemple de requête Postman rejetée (statut 401 Unauthorized) confirmant l'invalidation du jeton d'accès après la déconnexion, lors d'une tentative d'accès à la route protégée /api/auth/me."



<img width="665" height="569" alt="logedout" src="https://github.com/user-attachments/assets/39111ddb-9098-4528-a347-aaf7268e9ef0" />



"Exemple de requête Postman réussie (statut 200 OK) illustrant la récupération et l'affichage de la liste des produits via la route /api/products.


<img width="639" height="601" alt="admin see" src="https://github.com/user-attachments/assets/974b8c5a-a5cf-4e6d-acae-b771ac756892" />



"Exemple de requête Postman réussie (statut 200 OK) illustrant la récupération des détails d'un produit spécifique via la route /api/products/{id}."


<img width="647" height="635" alt="adminsee" src="https://github.com/user-attachments/assets/64767c2d-4048-4219-ae0b-74ce7c11479a" />


"Exemple de requête Postman rejetée (statut 401 Unauthorized) admin post without token illustrant l'accès refusé lors d'une tentative de création d'un produit (POST) via la route /api/products/ sans jeton d'authentification."


<img width="653" height="559" alt="afmin post without token" src="https://github.com/user-attachments/assets/3310e8ac-36f7-405b-b656-8635dfad5dce" />



"Exemple de requête Postman réussie (statut 201 Created) illustrant la création d'un nouveau produit via la route /api/products/ en utilisant un jeton d'authentification valide."



<img width="689" height="620" alt="adminpostwithtoke" src="https://github.com/user-attachments/assets/4c98d432-d0dd-4bb6-ac8e-756122e38b55" />


"Exemple de requête Postman réussie admin update (statut 200 OK) illustrant la mise à jour (PUT) des informations d'un produit existant via la route /api/products/{id} avec les autorisations appropriées."


<img width="708" height="638" alt="admincanupdate" src="https://github.com/user-attachments/assets/817a1d43-f3e6-4312-ad14-f5490aa4cb0f" />



"Exemple de requête Postman rejetée (statut 403 Forbidden) illustrant l'accès refusé pour un utilisateur standard tentant de créer un produit via la route /api/products (vérification des rôles et autorisations)."


<img width="601" height="604" alt="normalusercreate" src="https://github.com/user-attachments/assets/dda888ea-b64a-422a-93b8-7b07d569e6e7" />



"Exemple de requête Postman réussie (statut 200 OK) illustrant qu'un utilisateur authentifié est autorisé à consulter la liste (paginée) des produits via la route /api/products."



<img width="646" height="626" alt="cansee" src="https://github.com/user-attachments/assets/bfce429b-11df-4d70-87bc-3a01698b4662" />



"Exemple de requête Postman rejetée (statut 403 Forbidden) démontrant qu'un utilisateur standard n'a pas les autorisations nécessaires pour mettre à jour (PUT) un produit via la route /api/products/{id}."


<img width="695" height="647" alt="updatenormaluser" src="https://github.com/user-attachments/assets/409c7163-9337-420f-a35f-6ff1a9c73c52" />



"Exemple de requête Postman rejetée (statut 403 Forbidden) démontrant qu'un utilisateur standard n'a pas les autorisations nécessaires pour supprimer (DELETE) un produit via la route /api/products/{id}."



<img width="651" height="652" alt="normaldeleteerror" src="https://github.com/user-attachments/assets/a7ee4a1f-fbbf-4b5c-a14a-2d99f692831a" />


"Exemple de requête Postman réussie (statut 204 No Content) illustrant la suppression d'un produit (DELETE) via la route /api/products/{id} par un utilisateur disposant des privilèges d'administrateur."


<img width="644" height="535" alt="admindelete" src="https://github.com/user-attachments/assets/0d7e22e9-8c79-4e1e-ba15-3b7fe06232e9" />



"Exemple de requête Postman réussie (statut 200 OK) illustrant la récupération de la liste des commandes (et des détails clients associés) admin see all orders via la route /api/orders."


<img width="779" height="621" alt="adminseeorders" src="https://github.com/user-attachments/assets/7d64a6fb-0294-4516-9f8d-6dd3f64e3324" />



"Exemple de requête Postman réussie (statut 201 Created) illustrant la création d'une nouvelle commande (incluant les détails du client et les produits commandés)admin post order via la route /api/orders."


<img width="709" height="651" alt="adminpostorder" src="https://github.com/user-attachments/assets/ee79f317-12a5-49af-9a48-513df2b1de7e" />



"Exemple de requête Postman réussie (statut 200 OK)admin modifier order illustrant la mise à jour (PUT) d'une commande existante via la route /api/orders/{id} en utilisant un jeton d'authentification valide."


<img width="736" height="653" alt="adminmodifier order" src="https://github.com/user-attachments/assets/a8a5480c-f4e0-4891-a0f9-b01f33f2fc8c" />


"Exemple de requête Postman réussie (statut 204 No Content) illustrant la suppression d'une commande (DELETE) via la route /api/orders/{id} par un utilisateur disposant des privilèges d'administrateur."


<img width="771" height="581" alt="admin delete order" src="https://github.com/user-attachments/assets/0d161b3a-6225-4ed5-a5eb-68e393538177" />


"Exemple de requête Postman réussie (statut 201 Created) illustrant qu'un utilisateur standard est autorisé à créer une nouvelle commande via la route /api/orders."


<img width="636" height="641" alt="normal user cretae order" src="https://github.com/user-attachments/assets/38dda0c0-11dd-4c4d-9757-6180cb9dba99" />



"Exemple de requête Postman rejetée (statut 403 Forbidden) démontrant qu'un utilisateur standard n'a pas les autorisations nécessaires pour mettre à jour (PUT) une commande via la route /api/orders/{id}.


<img width="746" height="626" alt="normalusercantupdateorder" src="https://github.com/user-attachments/assets/629b6674-7e1a-45d6-8871-6a67cd4a6aa0" />



"Exemple de requête Postman rejetée (statut 403 Forbidden) démontrant qu'un utilisateur standard n'a pas les autorisations nécessaires pour supprimer (DELETE) une commande via la route /api/orders/{id}."


<img width="694" height="631" alt="normalusercantdelete order" src="https://github.com/user-attachments/assets/c9986d65-806a-41fb-bc30-858f08cb4d78" />


"Exemple de requête Postman réussie (statut 200 OK) démontrant qu'un utilisateur standard est autorisé à consulter les détails de sa propre commande via la route /api/orders/{id}."


<img width="737" height="640" alt="canseehisownorder" src="https://github.com/user-attachments/assets/a1dca69a-dc65-4a30-9f00-1ba40700d682" />


"Exemple de requête Postman rejetée (statut 403 Forbidden) démontrant qu'un utilisateur standard n'a pas l'autorisation de consulter les détails d'une commande appartenant à un autre utilisateur via la route /api/orders/{id}."


<img width="704" height="625" alt="cantsee another orderofsomeone" src="https://github.com/user-attachments/assets/12bb47e7-de28-48e9-86c6-b6b95208cbf3" />


"Exemple de requête Postman réussie (statut 200 OK) illustrant qu'un utilisateur standard ne récupère que la liste de ses propres commandes via la route /api/orders/."


<img width="697" height="579" alt="normaluser see in this query just his order" src="https://github.com/user-attachments/assets/ee2e6532-9b2f-4382-a9c5-deba97c2074b" />



"Exemple de requête Postman rejetée (statut 403 Forbidden) démontrant qu'un utilisateur standard, même en étant authentifié avec un jeton, n'a pas les autorisations nécessaires pour créer (POST) un produit via la route /api/products/."

<img width="738" height="635" alt="normaluseeven withtoken cancreateproducts" src="https://github.com/user-attachments/assets/5d56865e-d6d3-4261-9bec-3618f4554fa5" />


"Exemple de requête Postman rejetée (statut 403 Forbidden) démontrant qu'un utilisateur standard n'a pas les autorisations nécessaires pour mettre à jour (PUT) les informations d'un produit via la route /api/products/{id}."


<img width="752" height="653" alt="normalusercantupdateproducts" src="https://github.com/user-attachments/assets/a8a42ab2-8fde-4d6d-9488-e204220d268f" />


"Exemple de requête Postman rejetée (statut 403 Forbidden) démontrant qu'un utilisateur standard n'a pas les autorisations nécessaires pour supprimer (DELETE) un produit via la route /api/products/{id}.

<img width="705" height="677" alt="normalusecantdelete products" src="https://github.com/user-attachments/assets/7278b5cb-682d-4f1e-9f9d-40c702d11af2" />


"Exemple de requête Postman réussie (statut 200 OK) illustrant que la consultation de la liste des produits via la route /api/products est publique et ne nécessite ni jeton ni authentification préalable."

<img width="759" height="638" alt="anyone can see products without token or login" src="https://github.com/user-attachments/assets/6f4d42ff-08ad-414b-9aed-1decc996e310" />


"Exemple de requête Postman réussie (statut 200 OK) illustrant la suppression d'un produit (DELETE) via la route /api/products/{id} par un administrateur authentifié."


<img width="673" height="543" alt="admkncandelete pproduct withoutorder" src="https://github.com/user-attachments/assets/a1387bad-2fa7-4510-a8d8-b5bf43be3dda" />

