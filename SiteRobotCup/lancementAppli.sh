#!/bin/bash

# Script pour lancer deux commandes dans des terminaux différents

# Lancer la commande tailwind:build --watch dans un nouveau terminal
gnome-terminal -- bash -c "php bin/console tailwind:build --watch; exec bash"

# Lancer la commande symfony serve dans un autre terminal
gnome-terminal -- bash -c "symfony serve; exec bash"
