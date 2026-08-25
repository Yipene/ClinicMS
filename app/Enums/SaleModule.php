<?php

namespace App\Enums;

enum SaleModule: string
{
    case Caisse = 'caisse';
    case Pharmacie = 'pharmacie';
    case Consultation = 'consultation';
    case Examen = 'examen';
    case Hospitalisation = 'hospitalisation';
    case Intervention = 'intervention';
    case Accouchement = 'accouchement';
    case Bloc = 'bloc';

    public function label(): string
    {
        return match ($this) {
            self::Caisse => 'Caisse',
            self::Pharmacie => 'Pharmacie',
            self::Consultation => 'Consultation',
            self::Examen => 'Examen',
            self::Hospitalisation => 'Hospitalisation',
            self::Intervention => 'Intervention chirurgicale',
            self::Accouchement => 'Accouchement',
            self::Bloc => 'Location de bloc',
        };
    }
}
