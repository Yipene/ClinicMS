<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ModulePlaceholderController extends Controller
{
    public function __invoke(string $module, ?string $section = null): View
    {
        $titles = [
            'caisse' => 'Module Caisse',
            'pharmacie' => 'Module Pharmacie',
            'stock' => 'Module Stock',
            'medical' => 'Module Actes Médicaux',
            'patients' => 'Gestion des patients',
        ];

        $title = $titles[$module] ?? 'Module';

        if ($section) {
            $title .= ' — '.ucfirst(str_replace(['.', '-', '_'], ' ', $section));
        }

        return view('modules.placeholder', [
            'title' => $title,
            'module' => $module,
            'section' => $section,
        ]);
    }
}
