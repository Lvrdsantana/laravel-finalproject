# 🚀 Guide de Déploiement Railway

## 📋 Checklist Pré-déploiement

- [ ] Code pushé sur GitHub
- [ ] Fichiers de configuration créés (Procfile, railway.json, nixpacks.toml)
- [ ] .env.example configuré
- [ ] Compte Railway créé

## 🔧 Configuration Locale

### 1. Générer la clé d'application

```bash
php artisan key:generate --show
```

Copiez la clé générée (commence par `base64:`)

### 2. Vérifier les migrations

```bash
php artisan migrate:status
```

### 3. Optimiser pour la production

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🌐 Déploiement sur Railway

### Étape 1 : Créer le projet

1. Allez sur [railway.app](https://railway.app)
2. Cliquez sur **"New Project"**
3. Sélectionnez **"Deploy from GitHub repo"**
4. Choisissez votre repository `laravel-finalproject`

### Étape 2 : Configurer les variables d'environnement

**Note :** Votre application utilise SQLite, donc pas besoin de service MySQL externe. La base de données sera créée automatiquement dans le dossier `database/`.

### Étape 3 : Variables d'environnement à ajouter

Dans **Settings → Variables**, ajoutez :

```env
APP_NAME="Laravel School Management"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:VOTRE_CLE_GENEREE
APP_URL=https://votre-app.up.railway.app

DB_CONNECTION=sqlite

SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database

LOG_CHANNEL=stack
LOG_LEVEL=error
```

### Étape 4 : Configuration du service

Dans **Settings → Service**, configurez :

**Build Command:**
```bash
composer install --optimize-autoloader --no-dev && touch database/database.sqlite && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

**Start Command:**
```bash
php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
```

### Étape 5 : Déployer

```bash
# Commit et push
git add .
git commit -m "Configure for Railway deployment"
git push origin main
```

Railway déploiera automatiquement votre application ! 🎉

## 🔍 Vérification Post-déploiement

### 1. Vérifier le statut du déploiement

```bash
railway logs
```

### 2. Exécuter les migrations

```bash
railway run php artisan migrate --force
```

### 3. Créer un utilisateur admin (optionnel)

```bash
railway run php artisan tinker
```

Puis dans tinker :
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@example.com';
$user->password = bcrypt('password');
$user->role = 'coordinators';
$user->save();
```

### 4. Ouvrir l'application

```bash
railway open
```

Ou visitez l'URL dans **Settings → Domains**

## 🐛 Dépannage

### Erreur "No application encryption key has been specified"

```bash
# Générez une nouvelle clé
php artisan key:generate --show

# Ajoutez-la dans Railway Variables
APP_KEY=base64:votre_nouvelle_cle
```

### Erreur de migration

```bash
# Effacez et recréez les tables
railway run php artisan migrate:fresh --force

# Ou seedez la base de données
railway run php artisan migrate:fresh --seed --force
```

### Problèmes de cache

```bash
railway run php artisan config:clear
railway run php artisan cache:clear
railway run php artisan view:clear
railway run php artisan route:clear
```

### Erreur 500

```bash
# Activez le mode debug temporairement
# Dans Railway Variables :
APP_DEBUG=true

# Vérifiez les logs
railway logs --follow

# N'oubliez pas de redésactiver après :
APP_DEBUG=false
```

### Permissions de stockage

```bash
railway run php artisan storage:link
railway run chmod -R 775 storage bootstrap/cache
```

## 📊 Monitoring

### Voir les logs en temps réel

```bash
railway logs --follow
```

### Vérifier l'état de la base de données

```bash
railway run php artisan db:show
railway run php artisan db:table users
```

### Statistiques de performance

```bash
railway run php artisan route:list
railway run php artisan optimize
```

## 🔄 Mise à jour de l'application

### Déploiement automatique

Railway redéploiera automatiquement à chaque push sur la branche `main`.

### Déploiement manuel

```bash
# Depuis le tableau de bord Railway
# Cliquez sur "Redeploy" dans Deployments
```

### Rollback

```bash
# Dans Deployments, cliquez sur l'ancien déploiement
# Puis cliquez sur "Redeploy"
```

## 🔐 Sécurité

### Variables sensibles

❌ **Ne committez JAMAIS :**
- `.env`
- Clés API
- Mots de passe
- Secrets

✅ **Utilisez toujours Railway Variables**

### SSL/HTTPS

Railway fournit automatiquement un certificat SSL pour votre domaine.

### Domaine personnalisé

1. Allez dans **Settings → Domains**
2. Cliquez sur **"Add Domain"**
3. Suivez les instructions pour configurer vos DNS

## 📞 Support

- **Documentation Railway** : [docs.railway.app](https://docs.railway.app/)
- **Discord Railway** : [discord.gg/railway](https://discord.gg/railway)
- **Laravel Deployment** : [laravel.com/docs/deployment](https://laravel.com/docs/deployment)

## ✅ Checklist Post-déploiement

- [ ] Application accessible via l'URL
- [ ] Base de données configurée et migrée
- [ ] Variables d'environnement correctes
- [ ] SSL actif
- [ ] Logs sans erreurs
- [ ] Login fonctionnel
- [ ] Utilisateur test créé
- [ ] Monitoring configuré

---

**Félicitations ! Votre application Laravel est maintenant déployée sur Railway ! 🎉**

