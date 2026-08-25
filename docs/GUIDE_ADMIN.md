# Guide administrateur — Clinic MS

## Paramètres clinique

Menu **Administration → Paramètres clinique** : nom, adresse, téléphones, devise, seuil d’alerte recette journalière.

## Utilisateurs

Créer ou modifier des comptes, assigner un rôle (administrateur, médecin, caissier, pharmacien), activer/désactiver l’accès.

## Fournisseurs

Référentiel utilisé pour les bons d’approvisionnement (délai de livraison en jours).

## Journal d’audit

Trace les actions sensibles : ventes, annulations, patients, paramètres, stock interne, etc. Filtrer par action ou date.

## Sauvegarde base de données

```bash
php artisan clinic:backup
```

Les fichiers sont enregistrés dans `storage/app/backups/`.

- **PostgreSQL** : nécessite `pg_dump` dans le PATH et `DB_*` correctement renseignés dans `.env`.
- **SQLite** : copie du fichier `database/database.sqlite`.

Une sauvegarde automatique est planifiée chaque jour à 02:00 si le planificateur Laravel tourne :

```bash
php artisan schedule:work
```

En production, ajoutez une entrée cron : `* * * * * php /chemin/artisan schedule:run`.

## Sécurité

- Notes patients : chiffrées en base (`encrypted`).
- Ne commitez jamais le fichier `.env`.
- Changez les mots de passe de démonstration après la mise en production.

## Déploiement rapide

Voir `scripts/install.ps1` (Windows) ou la section Installation du `README.md`.
