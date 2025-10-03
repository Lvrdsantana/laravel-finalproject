#!/bin/bash

echo "🚀 Déploiement sur Railway..."

# Installer les dépendances
echo "📦 Installation des dépendances..."
composer install --optimize-autoloader --no-dev

# Créer la base de données SQLite si elle n'existe pas
echo "🗄️ Création de la base de données SQLite..."
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    chmod 664 database/database.sqlite
fi

# Générer la clé d'application si nécessaire
if [ ! -f .env ]; then
    echo "⚙️ Configuration de l'environnement..."
    cp .env.example .env
    php artisan key:generate
fi

# Mettre en cache les configurations
echo "🔧 Mise en cache des configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Exécuter les migrations
echo "🗄️ Exécution des migrations..."
php artisan migrate --force

echo "✅ Déploiement terminé!"

