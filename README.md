
# UniversiteDB – Système de Gestion Universitaire

## 📚 Présentation

Ce projet a pour objectif de concevoir un système complet de gestion universitaire à travers une base de données relationnelle sécurisée et une interface web. Il permet de gérer les cours, les enseignants, les séances, les notes et les élèves, tout en assurant un contrôle strict des rôles et des accès.

---

## 🧱 Fonctionnalités principales

- Gestion des cours, enseignants, séances, notes, inscriptions, exercices
- Interfaces web personnalisées pour :
  - **Secrétaire** : gestion administrative
  - **Enseignant** : création de séances, saisie de notes
  - **Élève** : inscription, dépôt d’exercices
- Système d’authentification sécurisé
- Prévention des injections SQL via PDO
- Optimisation des performances avec indexation et audit

---

## 🗃️ Structure de la base de données

La base `UniversiteDB` contient les tables suivantes :

- `ELEVE`, `ENSEIGNANT`, `COURS`, `SEANCE`, `NOTE`, `INSCRIPTION`, `EXERCICE`, `COURS_SEMESTRIEL`
- Relations définies avec des clés primaires/étrangères
- Utilisation de types ENUM pour normaliser les valeurs

---

## 🔐 Gestion des rôles et sécurité

Trois rôles SQL ont été définis :

- `Secretaire` : gestion des cours, enseignants, semestres
- `Enseignant` : gestion des séances, saisie de notes
- `Eleve` : inscriptions, rendus d'exercices

Chaque rôle se voit attribuer uniquement les privilèges nécessaires (principe du moindre privilège). Les utilisateurs sont authentifiés avec un mot de passe et un rôle par défaut.

---

## 🛠️ Technologies utilisées

- **Backend** : PHP 8.4.4 (avec PDO)
- **Frontend** : HTML5, Tailwind CSS
- **Base de données** : MySQL
- **Sécurité** : gestion des rôles SQL, requêtes préparées

---

## 💡 Sécurité des requêtes

Protection contre les attaques SQL via :

```php
$stmt = $pdo->prepare("SELECT * FROM ELEVE WHERE nom = :nom");
$stmt->bindParam(':nom', $nom);
$stmt->execute();
```

---

## 🔍 Cas d’usage

- *Marie Dubois* s’inscrit à un cours, dépose un devoir.
- *Dr. Hamid* crée une séance, attribue des notes.

---

## 📈 Optimisation

- Indexation stratégique sur les colonnes critiques
- Profiling via `EXPLAIN`, `SHOW PROFILE`
- Journalisation des événements avec `audit_log`

---

## 🚀 Perspectives d’évolution

- Intégration de l’authentification LDAP
- Passage à un backend Python (Django)
- Notifications automatiques
- Version mobile-friendly

---

## 🔗 Démo / Code source

👉 [GitHub Repository](https://github.com/Anasaouina/school_management/)

---

## 🧑‍💻 Auteur

**Anas Aouina**  
Master 1 CDSI – 2024/2025
