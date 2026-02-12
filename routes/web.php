<?php

use App\Http\Controllers\BienController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LocataireController;
use App\Http\Controllers\LoyerController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProprietaireController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\RevisionLoyerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    $profileRoute = 'profile';
    Route::get($profileRoute, [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch($profileRoute, [ProfileController::class, 'update'])->name('profile.update');
    Route::delete($profileRoute, [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Dashboard: accessible à tous les utilisateurs authentifiés
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ==============================================================================
// GROUP 1: OPERATIONAL READ
// Accessible à : Admin, Direction, Gestionnaire, Comptable
// ==============================================================================
Route::middleware(['auth', 'role:admin|direction|gestionnaire|comptable', 'throttle:moderate-stats'])->group(function () {
    Route::get('/biens', [BienController::class, 'index'])
        ->middleware('permission:biens.view')
        ->name('biens.index');

    Route::get('/proprietaires/{proprietaire}/bilan', [ProprietaireController::class, 'bilanPDF'])
        ->middleware('permission:proprietaires.bilan')
        ->name('proprietaires.bilan');

    Route::get('/locataires/{locataire}/documents', [DocumentController::class, 'getForLocataire'])
        ->middleware('permission:documents.view')
        ->name('locataires.documents.index');

    Route::get('contrats/{contrat}/print', [ContratController::class, 'print'])
        ->middleware('permission:contrats.print')
        ->name('contrats.print');

    Route::resource('loyers', LoyerController::class)
        ->only(['index', 'show'])
        ->middleware('permission:loyers.view');

    Route::get('/loyers/{loyer}/quittance', [LoyerController::class, 'exporterPDF'])
        ->middleware('permission:loyers.quittance')
        ->name('loyers.quittance');

    Route::resource('revisions', RevisionLoyerController::class)
        ->only(['index'])
        ->middleware('permission:loyers.view');

    Route::get('/rapports/loyers', [RapportController::class, 'loyers'])
        ->middleware('permission:rapports.view')
        ->name('rapports.loyers');
    Route::get('/rapports/impayees', [RapportController::class, 'impayees'])
        ->middleware('permission:rapports.view')
        ->name('rapports.impayees');
    Route::get('/rapports/commissions', [RapportController::class, 'commissions'])
        ->middleware('permission:rapports.view')
        ->name('rapports.commissions');
    Route::get('/rapports/mensuel/{mois?}', [DashboardController::class, 'exporterRapportMensuel'])
        ->middleware('permission:rapports.mensuel')
        ->name('rapports.mensuel');

    Route::resource('paiements', PaiementController::class)
        ->only(['index', 'show'])
        ->middleware('permission:paiements.view');

    // API Stats & Alerts (lecture dashboard)
    Route::prefix('api')->group(function () {
        Route::get('/stats/kpis', function () {
            return response()->json((new \App\Services\DashboardStatsService)->getFinancialKPIs());
        })->name('api.stats.kpis');

        Route::get('/stats/parc', function () {
            return response()->json((new \App\Services\DashboardStatsService)->getParcStats());
        })->name('api.stats.parc');

        Route::get('/stats/charts', function () {
            return response()->json((new \App\Services\DashboardStatsService)->getChartData());
        })->name('api.stats.charts');

        Route::get('/alerts', function () {
            return response()->json((new \App\Services\DashboardStatsService)->getAlerts());
        })->name('api.alerts');
    });
});

// ==============================================================================
// GROUP 2: OPERATIONAL WRITE (Gestion du Patrimoine)
// Accessible à : Admin, Gestionnaire
// EXCLU : Direction, Comptable
// ==============================================================================
Route::middleware(['auth', 'role:admin|gestionnaire', 'throttle:global-mutations'])->group(function () {
    // Propriétaires (Create/Edit/Delete)
    Route::post('/dashboard/proprietaires', [ProprietaireController::class, 'store'])
        ->middleware('permission:proprietaires.create')
        ->name('dashboard.proprietaires.store');
    Route::put('/dashboard/proprietaires/{proprietaire}', [ProprietaireController::class, 'update'])
        ->middleware('permission:proprietaires.edit')
        ->name('dashboard.proprietaires.update');
    // Resource parts excluding index/show which are in Read Group
    Route::post('proprietaires', [ProprietaireController::class, 'store'])
        ->middleware('permission:proprietaires.create')
        ->name('proprietaires.store');
    Route::put('proprietaires/{proprietaire}', [ProprietaireController::class, 'update'])
        ->middleware('permission:proprietaires.edit')
        ->name('proprietaires.update');
    Route::delete('proprietaires/{proprietaire}', [ProprietaireController::class, 'destroy'])
        ->middleware('permission:proprietaires.delete')
        ->name('proprietaires.destroy');

    // Biens (Create/Edit/Delete)
    Route::post('/dashboard/biens', [BienController::class, 'store'])
        ->middleware('permission:biens.create')
        ->name('dashboard.biens.store');
    Route::put('/dashboard/biens/{bien}', [BienController::class, 'update'])
        ->middleware('permission:biens.edit')
        ->name('dashboard.biens.update');
    Route::delete('/dashboard/biens/{bien}', [BienController::class, 'destroy'])
        ->middleware('permission:biens.delete')
        ->name('dashboard.biens.delete');
    Route::delete('/dashboard/bien-images/{bienImage}', [BienController::class, 'deleteImage'])
        ->middleware('permission:biens.edit')
        ->name('dashboard.bien-images.delete');

    // Locataires (Write)
    Route::post('locataires', [LocataireController::class, 'store'])
        ->middleware('permission:locataires.create')
        ->name('locataires.store');
    Route::put('locataires/{locataire}', [LocataireController::class, 'update'])
        ->middleware('permission:locataires.edit')
        ->name('locataires.update');
    Route::delete('locataires/{locataire}', [LocataireController::class, 'destroy'])
        ->middleware('permission:locataires.delete')
        ->name('locataires.destroy');

    // Documents Locataire (Write)
    Route::post('/locataires/{locataire}/documents', [DocumentController::class, 'storeForLocataire'])
        ->middleware('permission:documents.upload')
        ->name('locataires.documents.store');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])
        ->middleware('permission:documents.delete')
        ->name('documents.destroy');

    // Contrats (Write)
    Route::post('contrats', [ContratController::class, 'store'])
        ->middleware('permission:contrats.create')
        ->name('contrats.store');
    Route::put('contrats/{contrat}', [ContratController::class, 'update'])
        ->middleware('permission:contrats.edit')
        ->name('contrats.update');
    Route::delete('contrats/{contrat}', [ContratController::class, 'destroy'])
        ->middleware('permission:contrats.delete')
        ->name('contrats.destroy');

    // Loyers (Generation & Write)
    // Note: LoyerController resource often implies viewing, but we put 'index'/'show' in Read group.
    // If resource() is used, we must be careful not to duplicate names or routes.
    // Ideally use explicit routes for clarity here or 'except' index/show.
    Route::resource('loyers', LoyerController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('permission:loyers.generate');
    Route::post('/loyers/generer-mois', [LoyerController::class, 'genererMois'])
        ->middleware('permission:loyers.generate')
        ->name('loyers.genererMois');

    // Révisions
    Route::post('revisions', [RevisionLoyerController::class, 'store'])
        ->middleware('permission:loyers.generate')
        ->name('revisions.store');

    // Dépenses (Write)
    Route::post('depenses', [\App\Http\Controllers\DepenseController::class, 'store'])
        ->middleware('permission:depenses.create')
        ->name('depenses.store');
    Route::put('depenses/{depense}', [\App\Http\Controllers\DepenseController::class, 'update'])
        ->middleware('permission:depenses.edit')
        ->name('depenses.update');
    Route::delete('depenses/{depense}', [\App\Http\Controllers\DepenseController::class, 'destroy'])
        ->middleware('permission:depenses.delete')
        ->name('depenses.destroy');
});

// ==============================================================================
// GROUP 3: FINANCIAL WRITE (Comptabilité)
// Accessible à : Admin, Comptable
// EXCLU : Direction, Gestionnaire (pour Paiements)
// ==============================================================================
Route::middleware(['auth', 'role:admin|comptable', 'throttle:global-mutations'])->group(function () {
    // Paiements (Create/Edit/Delete) - STRICTEMENT RÉSERVÉ FINANCES
    Route::post('paiements', [PaiementController::class, 'store'])
        ->middleware('permission:paiements.create')
        ->name('paiements.store');
    Route::delete('/dashboard/paiements/{paiement}', [PaiementController::class, 'destroy'])
        ->middleware('permission:paiements.delete')
        ->name('paiements.destroy');
});

// ==============================================================================
// GROUP 4: ADMIN / SYSTEM
// Accessible à : Admin seulement
// ==============================================================================
Route::middleware(['auth', 'role:admin', 'throttle:global-mutations'])->group(function () {
    // Documents (Admin Global)
    Route::resource('documents', DocumentController::class)
        ->only(['index', 'show'])
        ->middleware('permission:documents.view');

    // Gestion Utilisateurs
    Route::resource('users', \App\Http\Controllers\UserController::class)->only(['index', 'store', 'update', 'destroy']);

    // Gestion Rôles
    Route::get('/settings/roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('settings.roles');
    Route::get('/roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
    Route::post('/settings/roles/{role}/permissions', [\App\Http\Controllers\RoleController::class, 'updatePermissions'])->name('settings.roles.permissions');

    // Système
    Route::post('/system/migrate', [\App\Http\Controllers\SystemController::class, 'migrate'])
        ->middleware('throttle:strict-migration')
        ->name('system.migrate');
});

require __DIR__.'/auth.php';
