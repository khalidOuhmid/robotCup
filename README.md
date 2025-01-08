# SAE3.01_SiteRobot

# Le projet:
L'objectif est de développer une application full-stack permettant
d’organiser des compétitions de football … de robots !
Cette application couvre le processus : de l’inscription des
équipes à la publication des résultats de la compétition, en
passant par l’organisation des rencontres.

<hr>

# 1. Convention de codage:

### 1.1 Nomenclature et conventions associées:
- Le **PascalCase** (La première lettre de chaque mot sera en majuscule) est utilisé pour les classes, les interfaces et les noms des fichiers **PHP**.

- Le **camelCase** (La première lettre de chaque mot sera en majuscule sauf le premier mot qui sera lui en minuscule)est utilisé pour les fonctions, méthodes et variables.

### 1.2 Indentations:
- l'**indentation** doit être faite avec des **tabulations**.

### 1.3 Documentations & commentaires:
Un **bon commentaire** sur une **fonction compliquée** fait toujours plaisir pour un lecteur cherchant à **comprendre le fonctionnement**. **PHP** possède aussi des **PHPDocs**, commentaires de méthodes et fonctions permettant de résumer les entrées et sorties de celles-ci et ainsi savoir en un coup d'œil l'essentiel de son utilité.

### 1.4 PSR (PHP Standards Recommandation):
Règles spécifiques à **PHP**. Il s’agit de **conventions et standards de programmation** permettant de réunir les différentes méthodes de codage pouvant exister à travers les **framework** utilisés sous PHP, **Symfony** dans notre cas.



## 2. Installation et lancement du projet:

Le site de championnat de robots est réalisé avec les frameworks Symfony et TailwindCSS inclus en tant que composant dans le projet Symfony.

### 2.1 Installez les frameworks:

`Ces frameworks sont nécessaires pour que le site soit fonctionnel`

#### 2.1.1 Symfony:

 via `wegt` ou `curl`:

```bash
wget https://get.symfony.com/cli/installer -O - | bash

curl -sS https://get.symfony.com/cli/installer | bash
```

#### 2.1.2 TailwindCSS:

via `compser` et `php`:

```bash
composer require symfonycasts/tailwind-bundle
```
```bash
php bin/console tailwind:init
```


ou via `npm`:

```bash
npm install -D tailwindcss
```

```bash
npx tailwindcss init -p
```

### 2.2 Cloner le projet:


- Cloner le dépôt Git sur [Gitlab](git@gitlab-ce.iut.u-bordeaux.fr:cbenony/sae3.01_siterobot.git)

- Puis installer les composants manquants du projet symfony:

```bash
composer install
```

### 2.3 Lancer le site sur son navigateur:

Pour lancer le projet en local lancer la commande suivante:

```bash
./lancementAppli.sh
```
Cette commande lance deux terminaux distinct, un pour lancer tailwind et le deuxième pour lancer le serveur symfony.

Le **site sera accessible** sur l'addresse locale `127.0.0.1:8000` sur votre navigateur.

Le **port** est **indiqué après le lancement** de la commande précèdente.

## 3. Préparer la Base de Données :

### 3.1 Lancer le script de création dans phpMyAdmin
- Copier et executer le code du fichier script MLR1_ROBOT_CHAMPIONSHIP.SQL dans l'onglet SQL sur phpMyAdmin.net, dans votre base personelle.

### 3.2 Tester la base
#### 3.2.1 Installer les Frameworks
```bash
pip install pandas
pip install sqlalchemy
```
#### 3.2.2 Lancer les tests
- Lancer le fichier testDatabase.py 


### 3.3 Remplir la base 
- Copier et executer le code du fichier script script-remplissage.sql dans l'onglet SQL sur phpMyAdmin.net, dans votre base personelle.

<hr>

## Liste des membres:
- ABE Naoki
- BENONY Clément
- BEZIE Isadora
- DEBAILLEUL François
- LAUBAL Noah
- OUHMID Khalid