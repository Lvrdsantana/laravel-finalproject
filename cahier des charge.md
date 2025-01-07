# Cahier des Charges - Application Mobile de Gestion des Présences

## 1. Présentation du Projet
- **Objectif** : Développer une version mobile de l'application de gestion des présences
- **Technologie** : Flutter (Frontend) + SQLite (Base de données locale)
- **Public cible** : Étudiants, Enseignants, Coordinateurs

## 2. Architecture Technique

### 2.1 Base de données
- Utilisation de SQLite locale (même structure que Laravel)
- Tables principales :
  - users
  - students
  - teachers
  - coordinators
  - timetables (avec soft delete)
  - attendances
  - attendance_grades
  - courses
  - classes
  - justifications

### 2.2 Structure du Projet Flutter
```
lib/
├── main.dart
├── config/
│   ├── routes.dart
│   └── theme.dart
├── models/
│   ├── user.dart
│   ├── student.dart
│   ├── teacher.dart
│   ├── timetable.dart
│   └── attendance.dart
├── services/
│   ├── database_helper.dart
│   ├── auth_service.dart
│   └── attendance_service.dart
├── screens/
│   ├── auth/
│   │   ├── login_screen.dart
│   │   └── splash_screen.dart
│   ├── student/
│   │   ├── dashboard_screen.dart
│   │   └── attendance_history_screen.dart
│   ├── teacher/
│   │   ├── mark_attendance_screen.dart
│   │   └── class_list_screen.dart
│   └── coordinator/
│       ├── stats_screen.dart
│       └── manage_timetable_screen.dart
└── widgets/
    ├── attendance_card.dart
    ├── timetable_widget.dart
    └── stats_chart.dart
```

## 3. Étapes de Développement

### 3.1 Configuration Initiale
1. Créer un nouveau projet Flutter
```bash
flutter create presence_app
cd presence_app
```

2. Ajouter les dépendances dans pubspec.yaml
```yaml
dependencies:
  flutter:
    sdk: flutter
  sqflite: ^2.3.0
  path_provider: ^2.1.1
  provider: ^6.1.1
  shared_preferences: ^2.2.2
  fl_chart: ^0.65.0
  intl: ^0.18.1
```

### 3.2 Base de données
1. Créer le DatabaseHelper
2. Implémenter les migrations SQLite
3. Créer les modèles Dart avec fromMap/toMap

### 3.3 Authentification
1. Système de login local
2. Gestion des sessions
3. Routage basé sur les rôles

### 3.4 Fonctionnalités Étudiants
1. Dashboard avec statistiques
2. Visualisation emploi du temps
3. Historique des présences
4. Système de justification

### 3.5 Fonctionnalités Enseignants
1. Liste des cours
2. Marquage des présences
3. Modification des présences
4. Statistiques par classe

### 3.6 Fonctionnalités Coordinateurs
1. Gestion des emplois du temps
2. Suivi des statistiques
3. Gestion des alertes

## 4. Synchronisation des Données

### 4.1 Structure SQLite
```sql
-- Exemple de création de table
CREATE TABLE timetables (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    class_id INTEGER,
    teacher_id INTEGER,
    day_id INTEGER,
    time_slot_id INTEGER,
    color TEXT,
    course_id INTEGER,
    deleted_at TEXT
);
```

### 4.2 Gestion Offline
1. Stockage local des données
2. File d'attente des modifications
3. Gestion des conflits

## 5. Tests et Validation

### 5.1 Tests Unitaires
- Models
- Services
- Logique métier

### 5.2 Tests d'Intégration
- Flux de navigation
- Gestion des données
- Synchronisation

### 5.3 Tests UI
- Responsive design
- Thèmes (clair/sombre)
- Accessibilité

## 6. Déploiement

### 6.1 Préparation
1. Configuration de signing
2. Optimisation des assets
3. Configuration des environnements

### 6.2 Publication
1. Tests sur devices réels
2. Génération des builds
3. Déploiement sur stores

## 7. Maintenance

### 7.1 Monitoring
- Crashlytics
- Analytics
- Logs d'erreurs

### 7.2 Mises à jour
- Correctifs
- Nouvelles fonctionnalités
- Optimisations

## 8. Sécurité

### 8.1 Données Sensibles
- Chiffrement SQLite
- Secure Storage pour credentials
- Protection contre injection SQL

### 8.2 Authentification
- Gestion des tokens
- Validation des entrées
- Timeout de session

## 9. Performance

### 9.1 Optimisations
- Lazy loading
- Mise en cache
- Compression des données

### 9.2 Objectifs
- Démarrage < 2s
- Réponse UI < 16ms
- Taille app < 50MB
