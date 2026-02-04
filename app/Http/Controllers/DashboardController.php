<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bien;
use App\Models\BienImage;
use App\Models\Loyer;
use App\Models\Paiement;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Proprietaire;
use App\Models\User;
use App\Models\ActivityLog;
use App\Helpers\ActivityLogger;
use App\Services\DashboardStatsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        Carbon::setLocale('fr');
        $user = auth()->user();
        $moisActuel = Carbon::now()->format('Y-m');
        
        // 1. Récupération des stats Ontario Group (Optimisée avec Sous-requêtes)
        // 1. Récupération des stats Ontario Group (Optimisée selon le rôle)
        // Les sous-requêtes financières sont lourdes, on ne les lance que pour Admin/Direction/Gestionnaire
        $proprietaires = collect([]);
        
        if (in_array($user->role, ['admin', 'gestionnaire', 'direction'])) {
            $proprietaires = Proprietaire::withCount(['biens as logements_count'])
                ->addSelect([
                    'loyers_factures_mois' => Loyer::selectRaw('sum(montant)')
                        ->whereColumn('loyers.contrat_id', 'contrats.id')
                        ->where('loyers.mois', $moisActuel)
                        ->where('loyers.statut', '!=', 'annulé')
                        ->join('contrats', 'loyers.contrat_id', '=', 'contrats.id')
                        ->join('biens', 'contrats.bien_id', '=', 'biens.id')
                        ->whereColumn('biens.proprietaire_id', 'proprietaires.id'),

                    'loyers_encaisses_mois' => Loyer::selectRaw('sum(montant)')
                        ->whereColumn('loyers.contrat_id', 'contrats.id')
                        ->where('loyers.mois', $moisActuel)
                        ->where('loyers.statut', 'payé')
                        ->join('contrats', 'loyers.contrat_id', '=', 'contrats.id')
                        ->join('biens', 'contrats.bien_id', '=', 'biens.id')
                        ->whereColumn('biens.proprietaire_id', 'proprietaires.id'),

                    'total_impayes' => Loyer::selectRaw('SUM(loyers.montant - COALESCE((SELECT SUM(montant) FROM paiements WHERE paiements.loyer_id = loyers.id), 0))')
                        ->whereIn('loyers.statut', ['en_retard', 'émis', 'emis', 'partiellement_payé'])
                        ->join('contrats', 'loyers.contrat_id', '=', 'contrats.id')
                        ->join('biens', 'contrats.bien_id', '=', 'biens.id')
                        ->whereColumn('biens.proprietaire_id', 'proprietaires.id'),

                    'total_depenses' => \App\Models\Depense::selectRaw('sum(montant)')
                        ->join('biens', 'depenses.bien_id', '=', 'biens.id')
                        ->whereColumn('biens.proprietaire_id', 'proprietaires.id'),

                    'total_encaisse_global' => Paiement::selectRaw('sum(paiements.montant)')
                        ->join('loyers', 'paiements.loyer_id', '=', 'loyers.id')
                        ->join('contrats', 'loyers.contrat_id', '=', 'contrats.id')
                        ->join('biens', 'contrats.bien_id', '=', 'biens.id')
                        ->whereColumn('biens.proprietaire_id', 'proprietaires.id'),

                    'depenses_mois' => \App\Models\Depense::selectRaw('sum(montant)')
                        ->where('date_depense', 'like', $moisActuel . '%')
                        ->join('biens', 'depenses.bien_id', '=', 'biens.id')
                        ->whereColumn('biens.proprietaire_id', 'proprietaires.id'),
                ])
                ->get();
        } else {
            // Version légère pour Comptable (juste la liste pour info)
            $proprietaires = Proprietaire::withCount(['biens as logements_count'])->limit(50)->get();
        }

        // Données communes optimisées (Sélection des colonnes nécessaires)
        $commonData = [
            'biens_list' => Bien::with(['contrats:id,bien_id,statut,locataire_id,date_debut', 'contrats.locataire:id,nom,telephone', 'images', 'imagePrincipale'])
                                ->latest()
                                ->limit(50)
                                ->get(),
            'depenses_list' => \App\Models\Depense::with('bien')->latest()->get(),
            'categories_depenses' => ['maintenance', 'travaux', 'taxe', 'assurance', 'autre'],
            'locataires_list' => Locataire::with(['contrats:id,locataire_id,bien_id,loyer_montant,statut', 'contrats.bien:id,nom', 'contrats.loyers:id,contrat_id,montant,statut'])
                                ->withCount('contrats')
                                ->latest()
                                ->limit(50)
                                ->get(),
            'contrats_list' => Contrat::with(['bien:id,nom', 'locataire:id,nom'])
                                ->latest()
                                ->limit(50)
                                ->get(),
            'loyers_list' => Loyer::withMontantPaye()
                                 ->with(['contrat:id,locataire_id,bien_id', 'contrat.locataire:id,nom', 'contrat.bien:id,nom'])
                                 ->orderBy('id', 'desc')
                                 ->limit(200)
                                 ->get(),
            'paiements_list' => Paiement::with(['loyer:id,contrat_id,mois', 'loyer.contrat:id,locataire_id', 'loyer.contrat.locataire:id,nom'])
                                     ->latest()
                                     ->limit(50)
                                     ->get(),
            'proprietaires_list' => $proprietaires,
        ];

        switch ($user->role) {
            case 'admin':
                $roleData = $this->getAdminData();
                break;
            case 'gestionnaire':
                $roleData = $this->getGestionnaireData();
                break;
            case 'comptable':
                $roleData = $this->getComptableData();
                break;
            case 'direction':
                $roleData = $this->getDirectionData();
                break;
            default:
                abort(403, 'Rôle non autorisé');
        }
        
        $data = array_merge($commonData, $roleData);
        
        return view('dashboard.index', compact('data'));
    }

    /**
     * Store Propriétaire via Iframe target (Single Page Pattern)
     */
    public function storeProprietaire(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|unique:proprietaires,email',
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $prop = Proprietaire::create($request->only(['nom', 'prenom', 'email', 'telephone', 'adresse']));
            return response()->json([
                'success' => true,
                'message' => 'Propriétaire créé avec succès !',
                'data' => $prop
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Update Propriétaire via Iframe target (Single Page Pattern)
     */
    public function updateProprietaire(Request $request, Proprietaire $proprietaire)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|unique:proprietaires,email,' . $proprietaire->id,
            'telephone' => 'nullable|string|max:50',
            'adresse' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $proprietaire->update($request->only(['nom', 'prenom', 'email', 'telephone', 'adresse']));
            return response()->json([
                'success' => true,
                'message' => 'Propriétaire mis à jour !',
                'data' => $proprietaire
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Store Bien via Iframe target
     */
    public function storeBien(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string',
            'loyer_mensuel' => 'required|numeric',
            'type' => 'required|in:appartement,villa,studio,bureau,magasin,entrepot,autre',
            'nombre_pieces' => 'nullable|integer',
            'meuble' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Ontario Group est le seul propriétaire
            $proprietaire = Proprietaire::firstOrCreate(
                ['nom' => 'Ontario Group'],
                [
                    'prenom' => 'S.A.',
                    'email' => 'contact@ontariogroup.net', 
                    'telephone' => '33 822 32 67',
                    'adresse' => '5 Félix Faure x Colbert, Dakar Plateau'
                ]
            );

            $data = $request->except(['images', 'image']); // On garde 'image' par sécurité si envoyé par erreur
            $data['proprietaire_id'] = $proprietaire->id;

            $bien = Bien::create($data);
            
            // Gestion de plusieurs images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    $path = $imageFile->store('biens/' . $bien->id, 'public');
                    BienImage::create([
                        'bien_id' => $bien->id,
                        'chemin' => $path,
                        'nom_original' => $imageFile->getClientOriginalName(),
                        'principale' => ($index === 0),
                        'ordre' => $index + 1,
                    ]);
                }
            } elseif ($request->hasFile('image')) {
                // Compatibilité avec l'ancien champ unique si nécessaire
                $imageFile = $request->file('image');
                $path = $imageFile->store('biens/' . $bien->id, 'public');
                BienImage::create([
                    'bien_id' => $bien->id,
                    'chemin' => $path,
                    'nom_original' => $imageFile->getClientOriginalName(),
                    'principale' => true,
                    'ordre' => 1,
                ]);
            }

            ActivityLogger::log('Création Bien', 'Ajout du bien : ' . $bien->nom, 'success', $bien);
            
            return response()->json([
                'success' => true,
                'message' => 'Bien ajouté avec succès !',
                'data' => $bien
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Bien via Iframe target
     */
    public function updateBien(Request $request, Bien $bien)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string',
            'loyer_mensuel' => 'required|numeric',
            'type' => 'required|in:appartement,villa,studio,bureau,magasin,entrepot,autre',
            'nombre_pieces' => 'nullable|integer',
            'meuble' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldLoyer = $bien->loyer_mensuel;
            $newLoyer = $request->loyer_mensuel; // Capture explicite de la nouvelle valeur
            
            $bien->update($request->except(['images', 'image']));
            
            // Si le loyer a changé (comparaison robuste)
            if ((float)$oldLoyer != (float)$newLoyer) {
                // ... (reste du code de synchro inchangé)
                $contrats = Contrat::where('bien_id', $bien->id)
                       ->whereIn('statut', ['actif', 'en_attente'])
                       ->get();

                foreach ($contrats as $c) {
                    $c->update(['loyer_montant' => $newLoyer]);
                    Loyer::where('contrat_id', $c->id)
                         ->whereIn('statut', ['émis', 'en_retard'])
                         ->update(['montant' => $newLoyer]);
                }
                ActivityLogger::log('Synchro Bien-Global', 'Mise à jour automatique Contrats & Loyers (Tout impayé) : ' . $bien->nom, 'info');
            }
            
            // Si de nouvelles images sont uploadées, on peut soit ajouter, soit remplacer.
            // Vu le design actuel, remplacer semble être l'intention si on uploade à nouveau.
            if ($request->hasFile('images')) {
                // Nettoyer les anciennes images si on veut un remplacement total
                foreach ($bien->images as $oldImage) {
                    if (Storage::disk('public')->exists($oldImage->chemin)) {
                        Storage::disk('public')->delete($oldImage->chemin);
                    }
                    $oldImage->delete();
                }

                foreach ($request->file('images') as $index => $imageFile) {
                    $path = $imageFile->store('biens/' . $bien->id, 'public');
                    BienImage::create([
                        'bien_id' => $bien->id,
                        'chemin' => $path,
                        'nom_original' => $imageFile->getClientOriginalName(),
                        'principale' => ($index === 0),
                        'ordre' => $index + 1,
                    ]);
                }
            } elseif ($request->hasFile('image')) {
                // Ancien comportement pour image unique
                foreach ($bien->images as $oldImage) {
                    if (Storage::disk('public')->exists($oldImage->chemin)) {
                        Storage::disk('public')->delete($oldImage->chemin);
                    }
                    $oldImage->delete();
                }
                $imageFile = $request->file('image');
                $path = $imageFile->store('biens/' . $bien->id, 'public');
                BienImage::create([
                    'bien_id' => $bien->id,
                    'chemin' => $path,
                    'nom_original' => $imageFile->getClientOriginalName(),
                    'principale' => true,
                    'ordre' => 1,
                ]);
            }
            
            ActivityLogger::log('Modification Bien', 'Mise à jour du bien : ' . $bien->nom, 'info', $bien);

            return response()->json([
                'success' => true,
                'message' => 'Bien mis à jour (et contrats synchronisés) !',
                'data' => $bien
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete Bien via Iframe target
     */
    public function deleteBien(Bien $bien)
    {
        try {
            $nom = $bien->nom;
            $bien->delete();
            ActivityLogger::log('Suppression Bien', 'Suppression du bien : ' . $nom, 'warning');
            return response()->json([
                'success' => true,
                'message' => 'Bien supprimé avec succès !'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer ce bien car il est lié à des contrats actifs.'
            ], 422); // Note: 422 ou 403, mais 422 pour "Unprocessable"
        }
    }

    /**
     * Delete Bien Image via AJAX
     */
    public function deleteBienImage(BienImage $bienImage)
    {
        try {
            // Supprimer le fichier du storage
            if (Storage::disk('public')->exists($bienImage->chemin)) {
                Storage::disk('public')->delete($bienImage->chemin);
            }
            
            // Si c'était l'image principale, définir la prochaine image comme principale
            if ($bienImage->principale) {
                $nextImage = BienImage::where('bien_id', $bienImage->bien_id)
                    ->where('id', '!=', $bienImage->id)
                    ->orderBy('ordre')
                    ->first();
                if ($nextImage) {
                    $nextImage->update(['principale' => true]);
                }
            }
            
            $bienImage->delete();
            
            return response()->json(['success' => true, 'message' => 'Image supprimée']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    private function getAdminData()
    {
        $moisActuel = Carbon::now()->format('Y-m');
        $gestionnaireData = $this->getGestionnaireData();
        
        return array_merge($gestionnaireData, [
            'role' => 'admin',
            'users_list' => User::all(),
            'logs_list' => ActivityLog::with('user')->latest()->limit(50)->get(),
        ]);
    }

    private function getGestionnaireData()
    {
        $totalBiens = Bien::count();
        $biensLibres = Bien::where('statut', 'libre')->count();
        $contratsActifs = Contrat::where('statut', 'actif')->count();
        $moisActuel = Carbon::now()->format('Y-m');
        $loyersEmisMois = Loyer::where('mois', $moisActuel)->where('statut', '!=', 'annulé')->sum('montant');
        $loyersPayesMois = Loyer::where('mois', $moisActuel)->where('statut', 'payé')->sum('montant');
        $loyersEnRetard = Loyer::whereIn('statut', ['en_retard', 'émis', 'emis', 'partiellement_payé'])
            ->sum(DB::raw('montant - (SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.loyer_id = loyers.id)'));
        
        return [
            'role' => 'gestionnaire',
            'kpis' => [
                'total_logements' => $totalBiens,
                'logements_libres' => $biensLibres,
                'contrats_actifs' => $contratsActifs,
                'loyers_emis_mois' => $loyersEmisMois,
                'loyers_payes_mois' => $loyersPayesMois,
                'total_en_retard' => $loyersEnRetard,
            ],
            'derniers_contrats' => Contrat::with(['bien', 'locataire'])->where('statut', 'actif')->latest()->limit(5)->get(),
        ];
    }

    private function getComptableData()
    {
        $moisActuel = Carbon::now()->format('Y-m');
        $loyersEmis = Loyer::where('mois', $moisActuel)->where('statut', '!=', 'annulé')->sum('montant');
        $loyersPayes = Loyer::where('mois', $moisActuel)->where('statut', 'payé')->sum('montant');
        $totalImpaye = Loyer::whereIn('statut', ['en_retard', 'émis', 'emis', 'partiellement_payé'])
            ->sum(DB::raw('montant - (SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.loyer_id = loyers.id)'));
        
        return [
            'role' => 'comptable',
            'kpis' => [
                'loyers_emis' => $loyersEmis,
                'loyers_payes' => $loyersPayes,
                'total_impaye' => $totalImpaye,
                'taux_recouvrement' => $loyersEmis > 0 ? round(($loyersPayes / $loyersEmis) * 100) : 0,
            ],
            'loyers_en_attente' => Loyer::with(['contrat.bien', 'contrat.locataire'])->whereIn('statut', ['émis', 'en_retard'])->latest()->limit(10)->get(),
            'derniers_paiements' => Paiement::with(['loyer.contrat.locataire'])->latest('date_paiement')->limit(10)->get(),
        ];
    }

    private function getDirectionData()
    {
        $moisActuel = Carbon::now()->format('Y-m');
        $totalBiens = Bien::count();
        $biensOccupesCount = Bien::where('statut', 'occupé')->count();
        $tauxOccupation = $totalBiens > 0 ? round(($biensOccupesCount / $totalBiens) * 100) : 0;
        
        $loyersEmisMois = Loyer::where('mois', $moisActuel)->where('statut', '!=', 'annulé')->sum('montant');
        $loyersPayesMois = Loyer::where('mois', $moisActuel)->where('statut', 'payé')->sum('montant');
        $tauxCollecte = $loyersEmisMois > 0 ? round(($loyersPayesMois / $loyersEmisMois) * 100) : 0;
        
        $impayesTotal = Loyer::whereIn('statut', ['en_retard', 'émis', 'emis', 'partiellement_payé'])
            ->sum(DB::raw('montant - (SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.loyer_id = loyers.id)'));
        $valeurPortefeuille = Bien::sum('loyer_mensuel');
        
        $commissionMensuelle = round($loyersPayesMois * 0.10);
        
        // Répartition par type de bien
        $repartitionType = DB::table('biens')
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();

        // Revenus des 6 derniers mois pour le graphique
        $revenusPar6Mois = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i)->translatedFormat('M Y');
            $moisRaw = Carbon::now()->subMonths($i)->format('Y-m');
            $revenusPar6Mois[] = [
                'mois' => $m,
                'montant' => Loyer::where('mois', $moisRaw)->where('statut', 'payé')->sum('montant')
            ];
        }

        return [
            'role' => 'direction',
            'kpis' => [
                'total_logements' => $totalBiens,
                'biens_occupes' => $biensOccupesCount,
                'taux_occupation' => $tauxOccupation,
                'revenu_mensuel' => $loyersPayesMois,
                'taux_collecte' => $tauxCollecte,
                'commission_mensuelle' => $commissionMensuelle,
                'impayes' => $impayesTotal,
                'valeur_portefeuille' => $valeurPortefeuille,
                'loyer_moyen' => $totalBiens > 0 ? round($valeurPortefeuille / $totalBiens) : 0,
                'projection_annuelle' => $commissionMensuelle * 12,
            ],
            'repartition_type' => $repartitionType,
            'revenus_par_mois' => $revenusPar6Mois,
            'derniers_paiements' => Paiement::with(['loyer.contrat.locataire'])->latest()->limit(5)->get(),
            'contrats_expiration' => Contrat::with(['bien', 'locataire'])
                ->where('statut', 'actif')
                ->whereNotNull('date_fin')
                ->where('date_fin', '<=', Carbon::now()->addDays(60))
                ->orderBy('date_fin', 'asc')
                ->get(),
        ];
    }

    /**
     * Exporter le rapport mensuel en PDF
     */
    public function exporterRapportMensuel($mois = null)
    {
        $mois = $mois ?? request('mois') ?? Carbon::now()->format('Y-m');
        
        // Calcul des KPIs
        $totalBiens = Bien::count();
        $biensOccupes = Bien::where('statut', 'occupé')->count();
        $tauxOccupation = $totalBiens > 0 ? round(($biensOccupes / $totalBiens) * 100) : 0;
        
        $loyersMois = Loyer::where('mois', $mois)->get();
        $loyersEmis = $loyersMois->where('statut', '!=', 'annulé')->sum('montant');
        $loyersPayes = $loyersMois->where('statut', 'payé')->sum('montant');
        $totalImpaye = Loyer::whereIn('statut', ['en_retard', 'émis', 'emis', 'partiellement_payé'])
            ->sum(DB::raw('montant - (SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.loyer_id = loyers.id)'));
        
        $tauxCollecte = $loyersEmis > 0 ? round(($loyersPayes / $loyersEmis) * 100) : 0;
        $commissionMensuelle = round($loyersPayes * 0.10); // 10% de commission
        
        // Revenus des 6 derniers mois
        $revenusPar6Mois = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i)->translatedFormat('M Y');
            $mRaw = Carbon::now()->subMonths($i)->format('Y-m');
            $revenusPar6Mois[] = [
                'mois' => $m,
                'montant' => Loyer::where('mois', $mRaw)->where('statut', 'payé')->sum('montant')
            ];
        }
        
        $data = [
            'biens_list' => Bien::latest()->get(),
            'kpis' => [
                'revenu_mensuel' => $loyersPayes,
                'taux_occupation' => $tauxOccupation,
                'impayes' => $totalImpaye,
                'taux_collecte' => $tauxCollecte,
                'loyers_emis' => $loyersEmis,
                'loyers_payes' => $loyersPayes,
                'total_impaye' => $totalImpaye,
                'commission_mensuelle' => $commissionMensuelle,
            ],
            'revenus_par_mois' => $revenusPar6Mois,
        ];
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rapport-mensuel', compact('data', 'mois'));
        
        $filename = 'Rapport_Mensuel_' . Carbon::parse($mois)->translatedFormat('F_Y') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
