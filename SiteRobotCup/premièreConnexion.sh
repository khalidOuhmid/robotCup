#!/bin/bash

# Assurez-vous que le script est exécuté dans le bon répertoire de votre projet Symfony
echo "Installation de SymfonyCasts Tailwind Bundle..."
composer require symfonycasts/tailwind-bundle

# Initialisation de Tailwind avec Symfony
echo "Initialisation de Tailwind..."
php bin/console tailwind:init

echo "Processus terminé."
