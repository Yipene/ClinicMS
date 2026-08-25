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
        if ($fee <= 0) {
            return null;
        }

        return $this->saleService->create(
            ['patient_id' => $patient->id, 'module' => $module, 'discount' => 0],
            [[
                'description' => $description,
                'quantity' => 1,
                'unit_price' => $fee,
            ]],
            [['method' => 'cash', 'amount' => $fee]],
        );
    }
}
