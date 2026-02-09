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

// Dashboard - accessible to all authenticated users
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/dashboard/proprietaires', [ProprietaireController::class, 'store'])
    ->middleware(['auth', 'role:admin|gestionnaire'])->name('dashboard.proprietaires.store');
Route::put('/dashboard/proprietaires/{proprietaire}', [ProprietaireController::class, 'update'])
    ->middleware(['auth', 'role:admin|gestionnaire'])->name('dashboard.proprietaires.update');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes accessibles à Admin et Gestionnaire (Gestion du patrimoine)
Route::middleware(['auth', 'role:admin|direction|gestionnaire', 'throttle:global-mutations'])->group(function () {
    // Liste publique des biens (interface standard)
    Route::get('/biens', [BienController::class, 'index'])->name('biens.index');

    // Propriétaires
    Route::resource('proprietaires', ProprietaireController::class);
    Route::get('/proprietaires/{proprietaire}/bilan', [\App\Http\Controllers\ProprietaireController::class, 'bilanPDF'])->name('proprietaires.bilan');

    // Biens (Anciennement Immeubles/Logements)
    Route::post('/dashboard/biens', [BienController::class, 'store'])->name('dashboard.biens.store');
    Route::put('/dashboard/biens/{bien}', [BienController::class, 'update'])->name('dashboard.biens.update');
    Route::delete('/dashboard/biens/{bien}', [BienController::class, 'destroy'])->name('dashboard.biens.delete');
    Route::delete('/dashboard/bien-images/{bienImage}', [BienController::class, 'deleteImage'])->name('dashboard.bien-images.delete');

    // Locataires
    Route::resource('locataires', LocataireController::class);

    // Documents pour Locataires
    Route::post('/locataires/{locataire}/documents', [DocumentController::class, 'storeForLocataire'])->name('locataires.documents.store');
    Route::get('/locataires/{locataire}/documents', [DocumentController::class, 'getForLocataire'])->name('locataires.documents.index');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Contrats
    Route::get('contrats/{contrat}/print', [ContratController::class, 'print'])->name('contrats.print');
    Route::resource('contrats', ContratController::class);

    // Loyers
    Route::resource('loyers', LoyerController::class);
    Route::post('/loyers/generer-mois', [LoyerController::class, 'genererMois'])->name('loyers.genererMois');
    Route::get('/loyers/{loyer}/quittance', [LoyerController::class, 'exporterPDF'])->name('loyers.quittance');

    // Révisions de loyer
    Route::resource('revisions', RevisionLoyerController::class)->only(['index', 'store']);

    // Rapports
    Route::get('/rapports/loyers', [RapportController::class, 'loyers'])->name('rapports.loyers');
    Route::get('/rapports/impayees', [RapportController::class, 'impayees'])->name('rapports.impayees');
    Route::get('/rapports/commissions', [RapportController::class, 'commissions'])->name('rapports.commissions');
    Route::get('/rapports/mensuel/{mois?}', [DashboardController::class, 'exporterRapportMensuel'])->name('rapports.mensuel');

    // Dépenses
    Route::resource('depenses', \App\Http\Controllers\DepenseController::class)->only(['store', 'update', 'destroy']);
});

// Routes accessibles à Admin, Comptable et Gestionnaire (Gestion financière)
Route::middleware(['auth', 'role:admin|direction|comptable|gestionnaire', 'throttle:global-mutations'])->group(function () {
    // Paiements
    Route::resource('paiements', PaiementController::class)->only(['index', 'create', 'store', 'show']);
    Route::delete('/dashboard/paiements/{paiement}', [PaiementController::class, 'destroy'])->name('paiements.destroy');
});

// Routes accessibles à Admin seulement (Administration système)
Route::middleware(['auth', 'role:admin', 'throttle:global-mutations'])->group(function () {
    // Documents
    Route::resource('documents', DocumentController::class);
    // Gestion Utilisateurs
    Route::resource('users', \App\Http\Controllers\UserController::class)->only(['store', 'update', 'destroy']);
    // Exposer GET /users pour la section admin (tests)
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    // Gestion Rôles (nouvelle route)
    Route::get('/settings/roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('settings.roles');
    // Alias simple pour /roles utilisé par certains tests
    Route::get('/roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
    Route::post('/settings/roles/{role}/permissions', [\App\Http\Controllers\RoleController::class, 'updatePermissions'])->name('settings.roles.permissions');

    // Route Système pour Déploiement Mutualisé (Protégée par Token)
    Route::post('/system/migrate', [\App\Http\Controllers\SystemController::class, 'migrate'])
        ->middleware('throttle:strict-migration')
        ->name('system.migrate');
});

// Routes accessibles à Direction, Admin, Gestionnaire et Comptable (Rapports partagés)
Route::middleware(['auth', 'role:admin|direction|gestionnaire|comptable'])->group(function () {
    // Les routes de rapports sont déjà définies au-dessus pour admin/direction/gestionnaire.
    // Si on veut qu'elles soient aussi accessibles au comptable sans redéfinir, on les laisse dans le groupe du haut ou on les met ici.
});

// API Routes - Stats et Alertes (pour widgets AJAX)
Route::middleware(['auth', 'role:admin|direction|gestionnaire', 'throttle:moderate-stats'])->prefix('api')->group(function () {
    Route::get('/stats/kpis', function () {
        $service = new \App\Services\DashboardStatsService;

        return response()->json($service->getFinancialKPIs());
    })->name('api.stats.kpis');

    Route::get('/stats/parc', function () {
        $service = new \App\Services\DashboardStatsService;

        return response()->json($service->getParcStats());
    })->name('api.stats.parc');

    Route::get('/stats/charts', function () {
        $service = new \App\Services\DashboardStatsService;

        return response()->json($service->getChartData());
    })->name('api.stats.charts');

    Route::get('/alerts', function () {
        $service = new \App\Services\DashboardStatsService;

        return response()->json($service->getAlerts());
    })->name('api.alerts');
});

Route::get('/test-debug', function () {
    return 'ok';
});

require __DIR__.'/auth.php';
