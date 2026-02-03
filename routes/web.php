<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProprietaireController;
use App\Http\Controllers\LocataireController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\LoyerController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Dashboard - accessible to all authenticated users
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/dashboard/proprietaires', [DashboardController::class, 'storeProprietaire'])
    ->middleware(['auth', 'role:admin,gestionnaire'])->name('dashboard.proprietaires.store');
Route::put('/dashboard/proprietaires/{proprietaire}', [DashboardController::class, 'updateProprietaire'])
    ->middleware(['auth', 'role:admin,gestionnaire'])->name('dashboard.proprietaires.update');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes accessibles à Admin et Gestionnaire
Route::middleware(['auth', 'role:admin,direction,gestionnaire'])->group(function () {
    // Propriétaires
    Route::resource('proprietaires', ProprietaireController::class);
    Route::get('/proprietaires/{proprietaire}/bilan', [\App\Http\Controllers\ProprietaireController::class, 'bilanPDF'])->name('proprietaires.bilan');
    
    // Biens (Anciennement Immeubles/Logements)
    Route::post('/dashboard/biens', [DashboardController::class, 'storeBien'])->name('dashboard.biens.store');
    Route::put('/dashboard/biens/{bien}', [DashboardController::class, 'updateBien'])->name('dashboard.biens.update');
    Route::delete('/dashboard/biens/{bien}', [DashboardController::class, 'deleteBien'])->name('dashboard.biens.delete');
    Route::delete('/dashboard/bien-images/{bienImage}', [DashboardController::class, 'deleteBienImage'])->name('dashboard.bien-images.delete');
    
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

    // Dépenses
    Route::resource('depenses', \App\Http\Controllers\DepenseController::class)->only(['store', 'update', 'destroy']);
});

// Routes accessibles à Admin et Comptable
Route::middleware(['auth', 'role:admin,direction,comptable'])->group(function () {
    // Paiements
    Route::resource('paiements', PaiementController::class)->only(['index', 'create', 'store', 'show']);
    Route::delete('/dashboard/paiements/{paiement}', [PaiementController::class, 'destroy'])->name('paiements.destroy');
});

// Routes accessibles à Admin seulement
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Documents
    Route::resource('documents', DocumentController::class);
    // Gestion Utilisateurs
    Route::resource('users', \App\Http\Controllers\UserController::class)->only(['store', 'update', 'destroy']);
});

// Routes accessibles à Direction, Admin et Gestionnaire
Route::middleware(['auth', 'role:admin,direction,gestionnaire'])->group(function () {
    Route::get('/rapports/loyers', function () { return view('rapports.loyers'); })->name('rapports.loyers');
    Route::get('/rapports/impayees', function () { return view('rapports.impayees'); })->name('rapports.impayees');
    Route::get('/rapports/commissions', function () { return view('rapports.commissions'); })->name('rapports.commissions');
    Route::get('/rapports/mensuel/{mois?}', [DashboardController::class, 'exporterRapportMensuel'])->name('rapports.mensuel');
});

require __DIR__.'/auth.php';
