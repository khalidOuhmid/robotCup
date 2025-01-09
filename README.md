# SAE3.01_SiteRobot

# Le projet
L'objectif est de développer une application full-stack permettant
d’organiser des compétitions de football … de robots !
Cette application couvre le processus : de l’inscription des
équipes à la publication des résultats de la compétition, en
passant par l’organisation des rencontres.

<hr>

## 1. Convention de codage  

### 1.1 PSR (PHP Standards Recommendation)
Règles spécifiques à **PHP**. Il s’agit de **conventions et standards de programmation** permettant de réunir les différentes méthodes de codage pouvant exister à travers les **frameworks** utilisés sous PHP, **Symfony version 5.10.4.** dans notre cas.  

**Voici les versions des PSR utilisées dans cette application:**

- psr/cache (PSR-16) : Version 3.0.0 (utilisé pour la gestion du cache).
-  psr/clock : Version 1.0.0 (interface pour la gestion du temps).
- psr/container (PSR-11) : Version 2.0.2 (interface du container).
- psr/event-dispatcher (PSR-14) : Version 1.0.0 (pour la gestion des événements).
- psr/link (PSR-13) : Version 2.0.1 (interface pour la gestion des liens HTTP).
- psr/log (PSR-3) : Version 3.0.2 (interface de journalisation).

### 1.2 Vérification de l'application de la convention

Installer PHP_CodeSniffer, outil qui vérifie les conventions PSR  
`composer require --dev squizlabs/php_codesniffer` 

Et par exemple une commande qui vérifie  le PSR-16 :  
`vendor/bin/phpcs --standard=PSR16 ~/SaeRobocup/sae3.01_siterobot/SiteRobotcup` 






## 2. Installation et lancement du projet

Le site de championnat de robots est réalisé avec les frameworks Symfony et TailwindCSS inclus en tant que composant dans le projet Symfony.

### 2.1 Installation des frameworks

`Ces frameworks sont nécessaires pour que le site soit fonctionnel`

#### 2.1.1 Symfony

 via `wegt` ou `curl`:

```bash
wget https://get.symfony.com/cli/installer -O - | bash

curl -sS https://get.symfony.com/cli/installer | bash
```

#### 3.1.2 TailwindCSS

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

### 2.2 Cloner le projet


- Cloner le dépôt Git sur [GitLab](git@gitlab-ce.iut.u-bordeaux.fr:cbenony/sae3.01_siterobot.git)

- Puis installer les composants manquants du projet symfony:

```bash
composer install
```

### 3.3 Lancer le site sur son navigateur

Pour lancer le projet en local lancer la commande suivante:

```bash
./lancementAppli.sh
```
Cette commande lance deux terminaux distinct, un pour lancer Tailwind et le deuxième pour lancer le serveur symfony.

Le **site sera accessible** sur l'adresse locale `127.0.0.1:8000` sur votre navigateur.

Le **port** est **indiqué après le lancement** de la commande précédente.

<hr>

## Liste des membres:
- ABE Naoki
- BENONY Clément
- BEZIE Isadora
- DEBAILLEUL François
- LAUBAL Noah
- OUHMID Khalid