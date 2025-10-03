# Système de Gestion Scolaire Laravel

## 📚 À propos du projet

Ce système de gestion scolaire est une application web développée avec Laravel, conçue pour gérer efficacement les présences, les emplois du temps et la communication entre les différents acteurs de l'établissement scolaire.

## 🔑 Fonctionnalités principales

### 👥 Gestion des utilisateurs
- Multi-rôles : Étudiants, Enseignants, Coordinateurs, Parents
- Système d'authentification sécurisé
- Gestion des états des comptes (actif/inactif)
- Suivi de l'activité des utilisateurs

### 📅 Gestion des emplois du temps
- Création et modification des emplois du temps
- Historique des modifications
- Gestion des créneaux horaires
- Attribution des cours aux enseignants

### ✓ Gestion des présences
- Suivi des présences en temps réel
- Système de justification d'absences
- Calcul automatique des taux de présence
- Notifications automatiques pour les absences

### 📊 Système de notification
- Notifications pour les étudiants "droppés"
- Alertes de justification d'absence
- Notifications en temps réel pour les parents

## 🛠 Architecture technique

### Models
- User
- Students
- Teachers
- ParentModel
- Timetable
- TimeSlots
- TimetableHistory
- StudentPresence

### Notifications
- StudentDroppedNotification
- JustificationNotification

### Middleware
- RoleValidation (gestion des accès basée sur les rôles)

## 💻 Interface utilisateur
- Dashboard personnalisé pour chaque type d'utilisateur
- Interface responsive
- Design moderne avec CSS personnalisé
- Interactions dynamiques avec JavaScript

## 🔒 Sécurité
- Validation des rôles utilisateur
- Gestion des sessions
- Déconnexion automatique après inactivité
- Protection contre les accès non autorisés

## 📱 Vues principales
- Login
- Dashboard (personnalisé par rôle)
- Gestion des utilisateurs
- Emploi du temps des enseignants
- Interface parent
- Interface étudiant

## 🚀 Installation

1. Cloner le repository
```bash
git clone [url-du-repo]
```

2. Installer les dépendances
```bash
composer install
npm install
```

3. Configurer l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurer la base de données dans .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Lancer les migrations
```bash
php artisan migrate
```

6. Lancer le serveur
```bash
php artisan serve
```

## 📋 Prérequis
- PHP >= 8.0
- Composer
- MySQL
- Node.js & NPM

## 🚀 Déploiement sur Railway

### Prérequis
1. Compte Railway : [railway.app](https://railway.app)
2. GitHub repository avec votre projet

### Étapes de déploiement

1. **Connectez-vous à Railway**
   - Allez sur [railway.app](https://railway.app)
   - Connectez-vous avec GitHub

2. **Créez un nouveau projet**
   ```bash
   # Dans Railway Dashboard
   - Cliquez sur "New Project"
   - Sélectionnez "Deploy from GitHub repo"
   - Choisissez votre repository
   ```

3. **Configurez les variables d'environnement**
   ```bash
   # Dans Settings → Variables, ajoutez :
   APP_NAME=VotreNomApp
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://votre-app.up.railway.app
   DB_CONNECTION=sqlite
   
   # Note : SQLite utilise un fichier local, pas besoin de serveur MySQL
   ```

5. **Générez la clé d'application**
   ```bash
   # Localement, générez une clé :
   php artisan key:generate --show
   
   # Ajoutez-la dans Railway Variables :
   APP_KEY=base64:votre_clé_générée
   ```

6. **Déployez l'application**
   ```bash
   # Poussez votre code sur GitHub
   git add .
   git commit -m "Préparation déploiement Railway"
   git push origin main
   
   # Railway déploiera automatiquement
   ```

### Variables d'environnement importantes

| Variable | Description | Exemple |
|----------|-------------|---------|
| `APP_KEY` | Clé de chiffrement | `base64:...` |
| `APP_URL` | URL de votre app | `https://app.railway.app` |
| `DB_CONNECTION` | Type de BDD | `sqlite` |
| `SESSION_DRIVER` | Driver de session | `database` |
| `QUEUE_CONNECTION` | Type de queue | `database` |

### Commandes utiles

```bash
# Voir les logs
railway logs

# Exécuter des commandes
railway run php artisan migrate

# Ouvrir l'application
railway open
```

### Troubleshooting

**Erreur de migration ?**
```bash
railway run php artisan migrate:fresh --seed
```

**Problème de cache ?**
```bash
railway run php artisan config:clear
railway run php artisan cache:clear
```

**Vérifier les logs ?**
```bash
railway logs --follow
```

### Liens utiles
- [Documentation Railway](https://docs.railway.app/)
- [Railway Discord](https://discord.gg/railway)
- [Laravel Deployment](https://laravel.com/docs/deployment)

## 🤝 Contribution
Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou à proposer une pull request.

## 📝 License
Ce projet est sous licence MIT.
