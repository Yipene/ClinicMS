<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Sale;

class MedicalBillingService
{
    public function __construct(
        protected SaleService $saleService,
    ) {}

    public function bill(Patient $patient, string $module, string $description, float $fee): ?Sale
    {
        return null;
    }
}
