# SAE3.01_SiteRobot

# Le projet:
L'objectif est de développer une application full-stack permettant
d’organiser des compétitions de football … de robots !
Cette application couvre le processus : de l’inscription des
équipes à la publication des résultats de la compétition, en
passant par l’organisation des rencontres.

<hr>

# Convention de codage:

### Nomenclature et conventions associées:
- Le **PascalCase** (La première lettre de chaque mot sera en majuscule) est utilisé pour les classes, les interfaces et les noms des fichiers **PHP**.

- Le **camelCase** (La première lettre de chaque mot sera en majuscule sauf le premier mot qui sera lui en minuscule)est utilisé pour les fonctions, méthodes et variables.

### Indentations:
- l'**indentation** doit être faite avec des **tabulations**.

### Documentations & commentaires:
Un **bon commentaire** sur une **fonction compliquée** fait toujours plaisir pour un lecteur cherchant à **comprendre le fonctionnement**. **PHP** possède aussi des **PHPDocs**, commentaires de méthodes et fonctions permettant de résumer les entrées et sorties de celles-ci et ainsi savoir en un coup d'œil l'essentiel de son utilité.

### PSR (PHP Standards Recommandation):
Règles spécifiques à **PHP**. Il s’agit de **conventions et standards de programmation** permettant de réunir les différentes méthodes de codage pouvant exister à travers les **framework** utilisés sous PHP, **Symfony** dans notre cas.

<hr>

## Liste des membres:
- ABE Naoki
- BENONY Clément
- BEZIE Isadora
- DEBAILLEUL François
- LAUBAL Noah
- OUHMID Khalid

## 3. Installation et lancement du projet:

> Le site de championnat de robots est réalisé avec les frameworks Symfony et TailwindCSS inclus en tant que composant dans le projet Symfony

### 3.1 Installez les frameworks

> Ces frameworks sont nécessaires pour que le site soit fonctionnel

#### 3.1.1 Symfony

 via `wegt` ou `curl`:

```bash
wget https://get.symfony.com/cli/installer -O - | bash

curl -sS https://get.symfony.com/cli/installer | bash
```

#### 3.1.2 TailwindCSS

via `npm`:

```bash
npm install -D tailwindcss
```

```bash
npx tailwindcss init -p
```

### 3.2 Cloner le projet


- Cloner le dépôt Git sur [Gitlab](git@gitlab-ce.iut.u-bordeaux.fr:cbenony/sae3.01_siterobot.git)

- Puis installer les composants manquants du projet symfony:

```bash
composer install
```

### 3.3 Lancer le site sur son navigateur

Pour lancer le projet en local avec Symfony lancer la commande suivante:

```bash
symfony server:start
```

ou 

```bash
symfony serve
```

Le site sera ainsi accessible sur l'addresse locale 127.0.0.1:[port] sur votre navigateur

> Le port est indiqué après le lancement de la commande précèdente
