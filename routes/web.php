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

// Dashboard - accessible to all authenticated users
// ==============================================================================
// GROUP 1: OPERATIONAL READ (Lecture Seule - Tout le monde)
// Accessible à : Admin, Direction, Gestionnaire, Comptable
// ==============================================================================
Route::middleware(['auth', 'role:admin|direction|gestionnaire|comptable', 'throttle:moderate-stats'])->group(function () {
    // Dashboard & Stats
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Listes & Détails (Read Only)
    Route::get('/biens', [BienController::class, 'index'])->name('biens.index');
    Route::get('/biens/{bien}', [BienController::class, 'show'])->name('biens.show'); // Si existe

    Route::get('/proprietaires', [ProprietaireController::class, 'index'])->name('proprietaires.index');
    Route::get('/proprietaires/{proprietaire}', [ProprietaireController::class, 'show'])->name('proprietaires.show');
    Route::get('/proprietaires/{proprietaire}/bilan', [ProprietaireController::class, 'bilanPDF'])->name('proprietaires.bilan');

    Route::get('/locataires', [LocataireController::class, 'index'])->name('locataires.index');
    Route::get('/locataires/{locataire}', [LocataireController::class, 'show'])->name('locataires.show');
    Route::get('/locataires/{locataire}/documents', [DocumentController::class, 'getForLocataire'])->name('locataires.documents.index');

    Route::get('/contrats', [ContratController::class, 'index'])->name('contrats.index');
    Route::get('/contrats/{contrat}', [ContratController::class, 'show'])->name('contrats.show');
    Route::get('contrats/{contrat}/print', [ContratController::class, 'print'])->name('contrats.print');

    Route::get('/loyers', [LoyerController::class, 'index'])->name('loyers.index');
    Route::get('/loyers/{loyer}', [LoyerController::class, 'show'])->name('loyers.show');
    Route::get('/loyers/{loyer}/quittance', [LoyerController::class, 'exporterPDF'])->name('loyers.quittance');

    Route::get('/revisions', [RevisionLoyerController::class, 'index'])->name('revisions.index');

    // Rapports (Lecture)
    Route::get('/rapports/loyers', [RapportController::class, 'loyers'])->name('rapports.loyers');
    Route::get('/rapports/impayees', [RapportController::class, 'impayees'])->name('rapports.impayees');
    Route::get('/rapports/commissions', [RapportController::class, 'commissions'])->name('rapports.commissions');
    Route::get('/rapports/mensuel/{mois?}', [DashboardController::class, 'exporterRapportMensuel'])->name('rapports.mensuel');

    Route::get('/depenses', [\App\Http\Controllers\DepenseController::class, 'index'])->name('depenses.index'); // Si index existe

    // Paiements (Lecture seule pour tous)
    Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements.index');
    Route::get('/paiements/{paiement}', [PaiementController::class, 'show'])->name('paiements.show');

    // Stats API (Partagées)
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
    Route::post('/dashboard/proprietaires', [ProprietaireController::class, 'store'])->name('dashboard.proprietaires.store');
    Route::put('/dashboard/proprietaires/{proprietaire}', [ProprietaireController::class, 'update'])->name('dashboard.proprietaires.update');
    // Resource parts excluding index/show which are in Read Group
    Route::get('proprietaires/create', [ProprietaireController::class, 'create'])->name('proprietaires.create');
    Route::post('proprietaires', [ProprietaireController::class, 'store'])->name('proprietaires.store');
    Route::get('proprietaires/{proprietaire}/edit', [ProprietaireController::class, 'edit'])->name('proprietaires.edit');
    Route::put('proprietaires/{proprietaire}', [ProprietaireController::class, 'update'])->name('proprietaires.update');
    Route::delete('proprietaires/{proprietaire}', [ProprietaireController::class, 'destroy'])->name('proprietaires.destroy');

    // Biens (Create/Edit/Delete)
    Route::post('/dashboard/biens', [BienController::class, 'store'])->name('dashboard.biens.store');
    Route::put('/dashboard/biens/{bien}', [BienController::class, 'update'])->name('dashboard.biens.update');
    Route::delete('/dashboard/biens/{bien}', [BienController::class, 'destroy'])->name('dashboard.biens.delete');
    Route::delete('/dashboard/bien-images/{bienImage}', [BienController::class, 'deleteImage'])->name('dashboard.bien-images.delete');

    // Locataires (Write)
    Route::get('locataires/create', [LocataireController::class, 'create'])->name('locataires.create');
    Route::post('locataires', [LocataireController::class, 'store'])->name('locataires.store');
    Route::get('locataires/{locataire}/edit', [LocataireController::class, 'edit'])->name('locataires.edit');
    Route::put('locataires/{locataire}', [LocataireController::class, 'update'])->name('locataires.update');
    Route::delete('locataires/{locataire}', [LocataireController::class, 'destroy'])->name('locataires.destroy');

    // Documents Locataire (Write)
    Route::post('/locataires/{locataire}/documents', [DocumentController::class, 'storeForLocataire'])->name('locataires.documents.store');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Contrats (Write)
    Route::get('contrats/create', [ContratController::class, 'create'])->name('contrats.create');
    Route::post('contrats', [ContratController::class, 'store'])->name('contrats.store');
    Route::get('contrats/{contrat}/edit', [ContratController::class, 'edit'])->name('contrats.edit');
    Route::put('contrats/{contrat}', [ContratController::class, 'update'])->name('contrats.update');
    Route::delete('contrats/{contrat}', [ContratController::class, 'destroy'])->name('contrats.destroy');

    // Loyers (Generation & Write)
    // Note: LoyerController resource often implies viewing, but we put 'index'/'show' in Read group.
    // If resource() is used, we must be careful not to duplicate names or routes.
    // Ideally use explicit routes for clarity here or 'except' index/show.
    Route::get('loyers/create', [LoyerController::class, 'create'])->name('loyers.create');
    Route::post('loyers', [LoyerController::class, 'store'])->name('loyers.store');
    Route::get('loyers/{loyer}/edit', [LoyerController::class, 'edit'])->name('loyers.edit');
    Route::put('loyers/{loyer}', [LoyerController::class, 'update'])->name('loyers.update');
    Route::delete('loyers/{loyer}', [LoyerController::class, 'destroy'])->name('loyers.destroy');
    Route::post('/loyers/generer-mois', [LoyerController::class, 'genererMois'])->name('loyers.genererMois');

    // Révisions
    Route::post('revisions', [RevisionLoyerController::class, 'store'])->name('revisions.store');

    // Dépenses (Request/Creation only)
    // Gestionnaire can CREATE a request, but maybe not PAY it?
    // For now assuming full CRUD on depenses except maybe 'pay' status if that existed.
    Route::resource('depenses', \App\Http\Controllers\DepenseController::class)->only(['store', 'update', 'destroy']);
});

// ==============================================================================
// GROUP 3: FINANCIAL WRITE (Comptabilité)
// Accessible à : Admin, Comptable
// EXCLU : Direction, Gestionnaire (pour Paiements)
// ==============================================================================
Route::middleware(['auth', 'role:admin|comptable', 'throttle:global-mutations'])->group(function () {
    // Paiements (Create/Edit/Delete) - STRICTEMENT RÉSERVÉ FINANCES
    Route::get('paiements/create', [PaiementController::class, 'create'])->name('paiements.create');
    Route::post('paiements', [PaiementController::class, 'store'])->name('paiements.store');
    Route::get('paiements/{paiement}/edit', [PaiementController::class, 'edit'])->name('paiements.edit'); // Si existe
    // Route::put('paiements/{paiement}', [PaiementController::class, 'update'])->name('paiements.update'); // Si existe
    Route::delete('/dashboard/paiements/{paiement}', [PaiementController::class, 'destroy'])->name('paiements.destroy');
});

// ==============================================================================
// GROUP 4: ADMIN / SYSTEM
// Accessible à : Admin seulement
// ==============================================================================
Route::middleware(['auth', 'role:admin', 'throttle:global-mutations'])->group(function () {
    // Documents (Admin Global)
    Route::resource('documents', DocumentController::class)->except(['destroy', 'store', 'index']); // Adjust as needed

    // Gestion Utilisateurs
    Route::resource('users', \App\Http\Controllers\UserController::class);

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
