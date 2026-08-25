# Guide utilisateur — Clinic MS

## Connexion

1. Ouvrez l’URL de l’application (ex. `http://127.0.0.1:8000`).
2. Connectez-vous avec l’e-mail et le mot de passe fournis par l’administrateur.

## Rôles

| Rôle | Modules principaux |
|------|-------------------|
| Administrateur | Tous + Administration |
| Médecin | Patients, consultations, examens, hospitalisations |
| Caissier | Caisse, recettes, patients |
| Pharmacien | Pharmacie, stock, inventaire |

## Caisse

- **Ventes** : créer une vente multi-lignes, choisir le mode de paiement, imprimer ou télécharger le reçu PDF.
- **Recette 24h / mensuelle** : consulter les totaux, exporter CSV ou PDF.

## Pharmacie

- Vente dédiée avec recherche produit.
- **Annulations** : annulation totale ou partielle avec retour stock automatique.

## Stock

- Gérer le catalogue (PA, PV, code-barres).
- **Approvisionnements** : bon de commande puis réception.
- **Inventaire** : comptage et ajustement.
- **Sortie interne** : consommation hors vente.
- **Péremption** : retrait des lots expirés ou proches de l’expiration.

## Actes médicaux

Enregistrer patients, consultations, examens, hospitalisations, interventions, accouchements et réservations de bloc. La facturation peut générer une vente en caisse selon les tarifs configurés.

## Code-barres (caisse / pharmacie / stock)

L’API interne permet de rechercher un produit :

- `GET /api/products/lookup?q=CODE`
- `GET /api/products/barcode/{code}`

(Requiert une session connectée avec les droits caisse, pharmacie ou stock.)
