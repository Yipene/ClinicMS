# Clinic MS

Logiciel de gestion médicale et pharmaceutique pour cliniques, hôpitaux et pharmacies.

## Stack technique

| Composant | Technologie |
|-----------|-------------|
| Backend | Laravel 12 (PHP 8.2+) |
| Base de données | **PostgreSQL** (`clinic`) ou SQLite |
| Frontend | Blade + Tailwind CSS 4 + Alpine.js |
| Auth & rôles | Laravel Breeze + Spatie Permission |
| PDF | DomPDF (`barryvdh/laravel-dompdf`) |

## Phases livrées

| Phase | Contenu |
|-------|---------|
| 1 | Auth, rôles, migrations métier, layout, dashboard KPI |
| 2 | Caisse (ventes, recettes, CSV/PDF), stock (produits, mouvements, BC, inventaire) |
| 3 | Pharmacie (ventes, annulations, retour stock) |
| 4 | Actes médicaux (patients, consultations, examens, hospitalisations, bloc…) |
| 5 | Administration, audit, API code-barres, exports, sauvegarde, tests |
| 6 | Documentation (`docs/`), script d’installation, déploiement |

## Modules

- **Caisse** — ventes, paiements, reçus PDF, recettes 24h/mensuelles, export CSV/PDF
- **Pharmacie** — ventes, annulations, approvisionnement
- **Stock** — catalogue, mouvements, BC/réception, inventaire, sortie interne, péremption, export CSV
- **Actes médicaux** — dossier patient (notes chiffrées), consultations, examens, hospitalisations, interventions, accouchements, bloc
- **Administration** — paramètres clinique, utilisateurs, fournisseurs, journal d’audit

## Installation

### Option rapide (Windows)

```powershell
.\scripts\install.ps1
```

### Manuel

#### 1. PostgreSQL

```sql
CREATE DATABASE clinic;
```

#### 2. Configuration `.env`

```env
APP_NAME="Clinic MS"
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=clinic
DB_USERNAME=postgres
DB_PASSWORD=votre_mot_de_passe
```

> Sans PostgreSQL : `DB_CONNECTION=sqlite` puis `touch database/database.sqlite`

#### 3. Lancer l'application

```bash
composer install
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

### Comptes de démonstration

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Administrateur | admin@clinic.local | password |
| Médecin | medecin@clinic.local | password |
| Caissier | caissier@clinic.local | password |
| Pharmacien | pharmacien@clinic.local | password |

## Documentation

- [Guide utilisateur](docs/GUIDE_UTILISATEUR.md)
- [Guide administrateur](docs/GUIDE_ADMIN.md)

## Commandes utiles

```bash
php artisan clinic:backup      # Sauvegarde BDD
php artisan test               # Tests PHPUnit
php artisan schedule:work      # Planificateur (sauvegarde 02:00)
```

## Personnalisation

Modifiez `APP_NAME` dans `.env` pour changer le nom affiché dans toute l'interface.
