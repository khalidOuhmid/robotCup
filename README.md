# SAE3.01_SiteRobot


# The Project
The objective is to develop a full-stack application that organizes football... robot competitions!
This application covers the entire process: from team registration to publishing the competition results, including organizing the matches.


<hr>


# Table of Contents 


1. [Coding Convention](#1)
2. [Deployment Guide](#2)
3. [Database](#3)


<hr>


## 1. Coding Convention <a id="1"></a>


### 1.1 PSR (PHP Standards Recommendation)
Specific rules for **PHP**. These are **conventions and programming standards** aimed at unifying the different coding methods that may exist across various **frameworks** used in PHP, **Symfony version 7.2** in our case.


**Here are the PSR versions used in this application:**
- **psr/basic-coding-standard (PSR-1)**: Version 1.0.0 (basic coding standard, including file structure and class naming with CamelCase).
- **psr/coding-style-guide (PSR-2)**: Version 1.0.0 (coding style guide for consistent formatting of PHP code).
- **psr/log (PSR-3)**: Version 1.1.0 (logging interface).
- **psr/autoloading (PSR-4)**: Version 1.0.0 (autoloader standard for PHP classes based on namespaces).
- **psr/cache (PSR-6)**: Version 3.0.0 (interface for cache management).
- **psr/http-message (PSR-7**): Version 1.0.1 (interface for HTTP messages, including requests and responses).
- **psr/container (PSR-11)**: Version 1.1.0 (dependency container interface).
- **psr/http-factory (PSR-17)**: Version 1.0.0 (interfaces for creating HTTP objects such as Request, Response, Uri).


### 1.2 Verification of Convention Application


Install PHP_CodeSniffer, a tool that checks PSR conventions 
`composer require --dev squizlabs/php_codesniffer`


For example, a command to verify PSR-17: 
`vendor/bin/phpcs --standard=PSR17 ~/SaeRobocup/sae3.01_siterobot/SiteRobotcup` 

### 1.3 All versions
- Symfony version 7.2  
- Tailwindcss 3.4.17  
- Python 3.12.3  
- PhpMyAdmin 5.2.1deb1  

## 2. Installation and Launching the Project <a id="2"></a>


The robot championship website is built with the Symfony and TailwindCSS frameworks, included as components within the Symfony project.


### 2.1 Installing the Frameworks


**These frameworks are required for the site to function properly.**


#### 2.1.1 Symfony


via `wget` or `curl`:


```bash
wget https://get.symfony.com/cli/installer -O - | bash


curl -sS https://get.symfony.com/cli/installer | bash
```
#### 2.1.2 TailwindCSS


via `compser` and `php`:


``bash
composer require symfonycasts/tailwind-bundle
```
```bash
php bin/console tailwind:init
```



or via `npm`:


``bash
npm install -D tailwindcss
```


```bash
npx tailwindcss init -p
```


### 2.2 Cloning the project




- Clone the Git repository on [GitLab](git@gitlab-ce.iut.u-bordeaux.fr:cbenony/sae3.01_siterobot.git)



- Then install the missing components of the symfony project:


```bash
composer install
```


### 2.3 Launching the site on your browser


You need to run this command to link tailwind with the symfony application


``bash
./firstConnection.sh²
```


You need to run this command to link tailwind with the symfony application


```bash
./firstConnection.sh²
```


To launch the project locally, run the following command:


```bash
./launchAppli.sh
```
This command launches two separate terminals, one to launch Tailwind and the second to launch the symfony server.


The **site will be accessible** on the local address `127.0.0.1:8000` on your browser.


The **port** is **indicated after the launch** of the previous command.



## 3 Database <a id="3"></a>


### 3.1 Creating and connecting to the database


**The creation script:**
~/sae3.01_siterobot/BD/MLR1_ROBOT_CHAMPIONSHIP.SQL
Then import the file into your database management software.


**Connection in the .env file :**
~/sae3.01_siterobot/SiteRobotCup/.env 
Then add/change the line :
```
DATABASE_URL="mysql://etu_user:K6pggasB@info-titania/etu_user’
```
With your software identifiers


### 3.2 Creating an admin account
**Option 1:** add the following lines at the end of the fill script
```
INSERT INTO T_USER_USR (USR_TYPE, USR_MAIL, USR_PASS)
VALUES (‘ADMIN’, ‘admin@example.com’, ‘XXXXXXX’);
```
By replacing the email and the XXXXXXX (6 digits or more) with an email of your choice and a password of your choice.


**Option 2:** directly in your database management software (in our case PhpMyAdmin)
- Select the T_USER_USR table
- Insert as type : ADMIN
- Enter the email address of your admin account
- Enter your password (6 digits or more) 


**Login:**
Go to log in/login in the top right-hand corner and enter your email address as your username and the password you have chosen above.


<hr>

## Members list:
- ABE Naoki
- BENONY Clément
- BEZIE Isadora
- DEBAILLEUL François
- LAUBAL Noah
- OUHMID Khalid





