<?php

return [
    'navigation' => [
        [
            'label' => 'Tableau de bord',
            'route' => 'dashboard',
            'icon' => 'home',
            'permission' => 'dashboard.view',
        ],
        [
            'label' => 'Caisse',
            'icon' => 'banknotes',
            'permission' => 'caisse.manage',
            'children' => [
                ['label' => 'Ventes', 'route' => 'caisse.sales.index'],
                ['label' => 'Recette 24h', 'route' => 'caisse.daily', 'permission' => 'caisse.reports'],
                ['label' => 'Recette mensuelle', 'route' => 'caisse.monthly', 'permission' => 'caisse.reports'],
            ],
        ],
        [
            'label' => 'Pharmacie',
            'icon' => 'beaker',
            'permission' => 'pharmacie.sell',
            'children' => [
                ['label' => 'Vente', 'route' => 'pharmacie.sales.index'],
                ['label' => 'Approvisionnement', 'route' => 'pharmacie.supply.index'],
                ['label' => 'Annulations', 'route' => 'pharmacie.cancellations.index'],
            ],
        ],
        [
            'label' => 'Stock',
            'icon' => 'cube',
            'permission' => 'stock.manage',
            'children' => [
                ['label' => 'Produits', 'route' => 'stock.products.index'],
                ['label' => 'Mouvements', 'route' => 'stock.movements.index'],
                ['label' => 'Approvisionnements', 'route' => 'stock.purchase-orders.index'],
                ['label' => 'Inventaire', 'route' => 'stock.inventory.index', 'permission' => 'stock.inventory'],
                ['label' => 'Sortie interne', 'route' => 'stock.internal.create'],
                ['label' => 'Péremption', 'route' => 'stock.expired.index'],
                ['label' => 'Export stock', 'route' => 'stock.export'],
            ],
        ],
        [
            'label' => 'Administration',
            'icon' => 'cog',
            'permission' => 'users.manage',
            'children' => [
                ['label' => 'Paramètres clinique', 'route' => 'admin.settings.edit'],
                ['label' => 'Utilisateurs', 'route' => 'admin.users.index'],
                ['label' => 'Fournisseurs', 'route' => 'admin.suppliers.index'],
                ['label' => 'Journal d\'audit', 'route' => 'admin.audit.index', 'permission' => 'audit.view'],
            ],
        ],
        [
            'label' => 'Actes médicaux',
            'icon' => 'heart',
            'permission' => 'consultations.manage',
            'children' => [
                ['label' => 'Patients', 'route' => 'patients.index'],
                ['label' => 'Consultations', 'route' => 'medical.consultations.index'],
                ['label' => 'Examens', 'route' => 'medical.exams.index'],
                ['label' => 'Hospitalisations', 'route' => 'medical.hospitalizations.index'],
                ['label' => 'Interventions', 'route' => 'medical.surgeries.index'],
                ['label' => 'Accouchements', 'route' => 'medical.deliveries.index'],
                ['label' => 'Bloc opératoire', 'route' => 'medical.bloc.index'],
            ],
        ],
    ],
];
