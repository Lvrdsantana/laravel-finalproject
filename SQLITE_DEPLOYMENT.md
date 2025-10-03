# 🗄️ Déploiement Laravel avec SQLite sur Railway

## ✅ Avantages de SQLite pour Railway

- ✅ Pas besoin de service MySQL externe (économie de ressources)
- ✅ Configuration simplifiée
- ✅ Base de données incluse dans le code
- ✅ Parfait pour les petits projets
- ✅ Déploiement plus rapide

## ⚠️ Important pour SQLite

### 1. Persistance des données

Sur Railway, le système de fichiers est **éphémère**. Cela signifie que :
- Les données SQLite seront **perdues** lors d'un redéploiement
- Pour une solution permanente, envisagez un volume persistent

### 2. Solution : Utiliser Railway Volumes

Pour persister votre base de données SQLite :

1. **Créer un volume dans Railway**
   ```bash
   # Dans le dashboard Railway
   - Allez dans votre service
   - Settings → Volumes
   - Cliquez sur "New Volume"
   - Mount Path: /app/database
   ```

2. **Modifier le chemin de la base de données**
   
   Dans votre `.env` sur Railway :
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=/app/database/database.sqlite
   ```

## 🚀 Guide de déploiement simplifié

### Étape 1 : Préparer localement

```bash
# Vérifier que votre base SQLite existe
touch database/database.sqlite

# Tester les migrations
php artisan migrate

# Générer la clé d'application
php artisan key:generate --show
```

### Étape 2 : Pousser sur GitHub

```bash
git add .
git commit -m "Configure for Railway deployment with SQLite"
git push origin main
```

### Étape 3 : Déployer sur Railway

1. Allez sur [railway.app](https://railway.app)
2. **New Project** → **Deploy from GitHub repo**
3. Sélectionnez votre repository

### Étape 4 : Variables d'environnement

Dans **Settings → Variables** :

```env
APP_KEY=base64:VOTRE_CLE_ICI
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=sqlite
```

### Étape 5 : Ajouter un Volume (Recommandé)

1. **Settings → Volumes**
2. **New Volume**
3. **Mount Path:** `/app/database`

Railway créera automatiquement le dossier et vos données seront persistées.

## 📝 Configuration du fichier database.php

Vérifiez que votre `config/database.php` contient :

```php
'sqlite' => [
    'driver' => 'sqlite',
    'url' => env('DB_URL'),
    'database' => env('DB_DATABASE', database_path('database.sqlite')),
    'prefix' => '',
    'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
],
```

## 🔧 Commandes utiles

### Vérifier la base de données

```bash
railway run php artisan db:show
```

### Migrer la base de données

```bash
railway run php artisan migrate --force
```

### Reset complet (⚠️ Supprime toutes les données)

```bash
railway run php artisan migrate:fresh --force
```

### Seeder la base de données

```bash
railway run php artisan db:seed --force
```

## 🐛 Troubleshooting SQLite

### Erreur : "database is locked"

```bash
# Augmentez le timeout
# Dans config/database.php :
'sqlite' => [
    // ...
    'timeout' => 15,
],
```

### Erreur : "unable to open database file"

```bash
# Vérifiez que le fichier existe
railway run ls -la database/

# Créez-le si nécessaire
railway run touch database/database.sqlite
```

### Permissions manquantes

```bash
railway run chmod 664 database/database.sqlite
railway run chmod 775 database/
```

## 🔄 Migration de SQLite vers MySQL

Si votre projet grandit et vous avez besoin de MySQL :

1. **Ajoutez MySQL à Railway**
   ```bash
   # Dans Railway Dashboard
   + New → Database → MySQL
   ```

2. **Exportez vos données SQLite**
   ```bash
   php artisan db:seed --class=DataExportSeeder
   ```

3. **Changez la connexion**
   ```env
   DB_CONNECTION=mysql
   ```

4. **Migrez les données**
   ```bash
   railway run php artisan migrate:fresh --seed --force
   ```

## 📊 Monitoring

### Taille de la base de données

```bash
railway run du -h database/database.sqlite
```

### Nombre d'enregistrements

```bash
railway run php artisan tinker
>>> DB::table('users')->count();
>>> DB::table('students')->count();
```

## 🎯 Recommandations

### Pour le développement ✅
- SQLite est parfait
- Rapide et simple

### Pour la production avec peu d'utilisateurs ✅
- SQLite fonctionne bien
- Utilisez un volume Railway pour la persistance

### Pour la production avec beaucoup d'utilisateurs ⚠️
- Envisagez MySQL ou PostgreSQL
- Meilleures performances en concurrence
- Plus de fonctionnalités

## 📋 Checklist de déploiement SQLite

- [ ] Fichier `database/database.sqlite` créé
- [ ] `.gitignore` configuré (SQLite n'est pas committé)
- [ ] Variables d'environnement configurées
- [ ] Volume Railway créé pour la persistance
- [ ] Migrations testées localement
- [ ] APP_KEY générée
- [ ] Code pushé sur GitHub
- [ ] Déploiement Railway réussi
- [ ] Migrations exécutées sur Railway
- [ ] Utilisateur test créé

---

**Votre application avec SQLite est prête pour Railway ! 🎉**

