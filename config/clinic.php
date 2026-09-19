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
                ['label' => 'Recette 24h', 'route' => 'caisse.daily', 'permission' => 'caisse.reports'],
                ['label' => 'Recette mensuelle', 'route' => 'caisse.monthly', 'permission' => 'caisse.reports'],
            ],
        ],
        [
    'label' => 'Pharmacie',
    'route' => 'pharmacie.index',
    'icon' => 'beaker',
    'permission' => 'pharmacie.sell',
],
        [
            'label' => 'Stock',
            'icon' => 'cube',
            'permission' => 'stock.manage',
            'children' => [
                ['label' => 'Produits', 'route' => 'stock.products.index', 'permission' => 'stock.manage'],
                ['label' => 'Mouvements', 'route' => 'stock.movements.index', 'permission' => 'stock.manage'],
                ['label' => 'Approvisionnements', 'route' => 'stock.purchase-orders.index', 'permission' => 'stock.manage'],
                ['label' => 'Inventaire', 'route' => 'stock.inventory.index', 'permission' => 'stock.inventory'],
                ['label' => 'Sortie interne', 'route' => 'stock.internal.create', 'permission' => 'stock.manage'],
                ['label' => 'Péremption', 'route' => 'stock.expired.index', 'permission' => 'stock.manage'],
                ['label' => 'Export stock', 'route' => 'stock.export', 'permission' => 'stock.manage'],
            ],
        ],
        [
            'label' => 'Administration',
            'icon' => 'cog',
            'permission' => 'users.manage',
            'children' => [
                ['label' => 'Paramètres clinique', 'route' => 'admin.settings.edit', 'permission' => 'users.manage'],
                ['label' => 'Utilisateurs', 'route' => 'admin.users.index', 'permission' => 'users.manage'],
                ['label' => 'Fournisseurs', 'route' => 'admin.suppliers.index', 'permission' => 'users.manage'],
                ['label' => 'Journal d\'audit', 'route' => 'admin.audit.index', 'permission' => 'audit.view'],
            ],
        ],
        [
            'label' => 'Actes médicaux',
            'icon' => 'heart',
            'permission' => 'consultations.manage',
            'children' => [
                ['label' => 'Patients', 'route' => 'patients.index', 'permission' => 'patients.manage'],
                ['label' => 'Consultations', 'route' => 'medical.consultations.index', 'permission' => 'consultations.manage'],
                ['label' => 'Examens', 'route' => 'medical.exams.index', 'permission' => 'exams.manage'],
                ['label' => 'Hospitalisations', 'route' => 'medical.hospitalizations.index', 'permission' => 'hospitalizations.manage'],
                ['label' => 'Interventions', 'route' => 'medical.surgeries.index', 'permission' => 'surgeries.manage'],
                ['label' => 'Accouchements', 'route' => 'medical.deliveries.index', 'permission' => 'deliveries.manage'],
                ['label' => 'Bloc opératoire', 'route' => 'medical.bloc.index', 'permission' => 'bloc.manage'],
            ],
        ],
    ],
];
