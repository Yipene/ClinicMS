<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Caisse\CaisseReportController;
use App\Http\Controllers\Caisse\SaleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Medical\BlocController;
use App\Http\Controllers\Medical\ConsultationController;
use App\Http\Controllers\Medical\DeliveryController;
use App\Http\Controllers\Medical\ExamController;
use App\Http\Controllers\Medical\HospitalizationController;
use App\Http\Controllers\Medical\PatientController;
use App\Http\Controllers\Medical\SurgeryController;
use App\Http\Controllers\Pharmacie\PharmacyCancellationController;
use App\Http\Controllers\Pharmacie\PharmacySaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Stock\ExpiredStockController;
use App\Http\Controllers\Stock\InventoryController;
use App\Http\Controllers\Stock\ProductController;
use App\Http\Controllers\Stock\PurchaseOrderController;
use App\Http\Controllers\Stock\StockExportController;
use App\Http\Controllers\Stock\StockInternalController;
use App\Http\Controllers\Stock\StockMovementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::middleware('permission:caisse.manage')->prefix('caisse')->name('caisse.')->group(function () {
        Route::get('/ventes', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/ventes/nouvelle', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/ventes', [SaleController::class, 'store'])->name('sales.store');
        Route::get('/ventes/{sale}', [SaleController::class, 'show'])->name('sales.show');
        Route::get('/ventes/{sale}/pdf', [SaleController::class, 'pdf'])->name('sales.pdf');
    });

    Route::middleware('permission:caisse.reports')->prefix('caisse')->name('caisse.')->group(function () {
        Route::get('/recette-journaliere', [CaisseReportController::class, 'daily'])->name('daily');
        Route::get('/recette-journaliere/export', [CaisseReportController::class, 'exportDaily'])->name('daily.export');
        Route::get('/recette-journaliere/pdf', [CaisseReportController::class, 'dailyPdf'])->name('daily.pdf');
        Route::get('/recette-mensuelle', [CaisseReportController::class, 'monthly'])->name('monthly');
        Route::get('/recette-mensuelle/export', [CaisseReportController::class, 'exportMonthly'])->name('monthly.export');
        Route::get('/recette-mensuelle/pdf', [CaisseReportController::class, 'monthlyPdf'])->name('monthly.pdf');
    });

    Route::middleware('permission:stock.manage')->prefix('stock')->name('stock.')->group(function () {
        Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
        Route::get('/produits/nouveau', [ProductController::class, 'create'])->name('products.create');
        Route::post('/produits', [ProductController::class, 'store'])->name('products.store');
        Route::get('/produits/{product}/modifier', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/produits/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::get('/mouvements', [StockMovementController::class, 'index'])->name('movements.index');
        Route::get('/approvisionnements', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
        Route::get('/approvisionnements/nouveau', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('/approvisionnements', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
        Route::get('/approvisionnements/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
        Route::post('/approvisionnements/{purchaseOrder}/reception', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
        Route::get('/sortie-interne', [StockInternalController::class, 'create'])->name('internal.create');
        Route::post('/sortie-interne', [StockInternalController::class, 'store'])->name('internal.store');
        Route::get('/peremption', [ExpiredStockController::class, 'index'])->name('expired.index');
        Route::post('/peremption/{product}', [ExpiredStockController::class, 'destroy'])->name('expired.destroy');
        Route::get('/export', [StockExportController::class, 'export'])->name('export');
    });

    Route::middleware('permission:stock.inventory')->prefix('stock')->name('stock.')->group(function () {
        Route::get('/inventaire', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventaire/nouveau', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/inventaire', [InventoryController::class, 'store'])->name('inventory.store');
    });

    Route::middleware('permission:pharmacie.sell')->prefix('pharmacie')->name('pharmacie.')->group(function () {
        Route::get('/ventes', [PharmacySaleController::class, 'index'])->name('sales.index');
        Route::get('/ventes/nouvelle', [PharmacySaleController::class, 'create'])->name('sales.create');
        Route::post('/ventes', [PharmacySaleController::class, 'store'])->name('sales.store');
        Route::get('/ventes/{sale}', [PharmacySaleController::class, 'show'])->name('sales.show');
        Route::get('/api/produits', [PharmacySaleController::class, 'searchProducts'])->name('products.search');
    });

    Route::middleware('permission:pharmacie.cancel')->prefix('pharmacie')->name('pharmacie.')->group(function () {
        Route::get('/annulations', [PharmacyCancellationController::class, 'index'])->name('cancellations.index');
        Route::get('/annulations/nouvelle', [PharmacyCancellationController::class, 'create'])->name('cancellations.create');
        Route::post('/annulations', [PharmacyCancellationController::class, 'store'])->name('cancellations.store');
    });

    Route::get('/pharmacie/approvisionnement', fn () => redirect()->route('stock.purchase-orders.index'))
        ->middleware('permission:pharmacie.supply')
        ->name('pharmacie.supply.index');

    Route::middleware('permission:patients.manage')->group(function () {
        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/nouveau', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{patient}/modifier', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    });

    Route::middleware('permission:consultations.manage')->prefix('medical')->name('medical.')->group(function () {
        Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultations.index');
        Route::get('/consultations/nouvelle', [ConsultationController::class, 'create'])->name('consultations.create');
        Route::post('/consultations', [ConsultationController::class, 'store'])->name('consultations.store');
        Route::get('/consultations/{consultation}', [ConsultationController::class, 'show'])->name('consultations.show');
    });

    Route::middleware('permission:exams.manage')->prefix('medical')->name('medical.')->group(function () {
        Route::get('/examens', [ExamController::class, 'index'])->name('exams.index');
        Route::get('/examens/nouveau', [ExamController::class, 'create'])->name('exams.create');
        Route::post('/examens', [ExamController::class, 'store'])->name('exams.store');
        Route::get('/examens/{exam}/modifier', [ExamController::class, 'edit'])->name('exams.edit');
        Route::put('/examens/{exam}', [ExamController::class, 'update'])->name('exams.update');
    });

    Route::middleware('permission:hospitalizations.manage')->prefix('medical')->name('medical.')->group(function () {
        Route::get('/hospitalisations', [HospitalizationController::class, 'index'])->name('hospitalizations.index');
        Route::get('/hospitalisations/nouvelle', [HospitalizationController::class, 'create'])->name('hospitalizations.create');
        Route::post('/hospitalisations', [HospitalizationController::class, 'store'])->name('hospitalizations.store');
        Route::post('/hospitalisations/{hospitalization}/sortie', [HospitalizationController::class, 'discharge'])->name('hospitalizations.discharge');
    });

    Route::middleware('permission:surgeries.manage')->prefix('medical')->name('medical.')->group(function () {
        Route::get('/interventions', [SurgeryController::class, 'index'])->name('surgeries.index');
        Route::get('/interventions/nouvelle', [SurgeryController::class, 'create'])->name('surgeries.create');
        Route::post('/interventions', [SurgeryController::class, 'store'])->name('surgeries.store');
    });

    Route::middleware('permission:deliveries.manage')->prefix('medical')->name('medical.')->group(function () {
        Route::get('/accouchements', [DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/accouchements/nouveau', [DeliveryController::class, 'create'])->name('deliveries.create');
        Route::post('/accouchements', [DeliveryController::class, 'store'])->name('deliveries.store');
    });

    Route::middleware('permission:bloc.manage')->prefix('medical')->name('medical.')->group(function () {
        Route::get('/bloc', [BlocController::class, 'index'])->name('bloc.index');
        Route::get('/bloc/nouvelle', [BlocController::class, 'create'])->name('bloc.create');
        Route::post('/bloc', [BlocController::class, 'store'])->name('bloc.store');
    });

    Route::middleware('permission:users.manage')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/parametres', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/parametres', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('/utilisateurs', [UserController::class, 'index'])->name('users.index');
        Route::get('/utilisateurs/nouveau', [UserController::class, 'create'])->name('users.create');
        Route::post('/utilisateurs', [UserController::class, 'store'])->name('users.store');
        Route::get('/utilisateurs/{user}/modifier', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/utilisateurs/{user}', [UserController::class, 'update'])->name('users.update');
        Route::get('/fournisseurs', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/fournisseurs/nouveau', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/fournisseurs', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/fournisseurs/{supplier}/modifier', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/fournisseurs/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    });

    Route::middleware('permission:audit.view')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
    });

    Route::middleware(['auth', 'role_or_permission:caisse.manage|pharmacie.sell|stock.manage'])->prefix('api')->name('api.')->group(function () {
        Route::get('/products/lookup', [ProductApiController::class, 'lookup'])->name('products.lookup');
        Route::get('/products/barcode/{barcode}', [ProductApiController::class, 'byBarcode'])->name('products.barcode');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
