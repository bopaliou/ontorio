<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Locataire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    private const DATE_FORMAT = 'd/m/Y H:i';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = Document::with('documentable')->latest()->paginate(20);

        return response()->json(['documents' => $documents]);
    }

    /**
     * Store a newly created resource in storage for a locataire.
     */
    public function storeForLocataire(Request $request, Locataire $locataire)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|mimetypes:application/pdf,image/jpeg,image/png,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:10240',
            'type' => 'required|string|in:cni,contrat_signe,attestation,justificatif,autre',
        ], [
            'document.required' => 'Veuillez sélectionner un fichier',
            'document.mimes' => 'Format non supporté (PDF, JPG, PNG, DOC autorisés)',
            'document.mimetypes' => 'Le type de fichier n\'est pas valide',
            'document.max' => 'Le fichier ne doit pas dépasser 10 Mo',
            'type.required' => 'Veuillez sélectionner un type de document',
        ]);

        try {
            $file = $request->file('document');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Générer un nom unique pour le fichier
            $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)).'_'.time().'.'.$extension;

            // Stocker le fichier dans storage/app/public/documents/locataires/{id}/
            $path = $file->storeAs(
                'documents/locataires/'.$locataire->id,
                $fileName,
                'public'
            );

            $document = Document::create([
                'type' => $request->type,
                'nom_original' => $originalName,
                'chemin_fichier' => $path,
                'entite_type' => Locataire::class,
                'entite_id' => $locataire->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document ajouté avec succès',
                'document' => [
                    'id' => $document->id,
                    'type' => $document->type,
                    'nom_original' => $document->nom_original,
                    'url' => get_secure_url($document->chemin_fichier),
                    'created_at' => $document->created_at->format(self::DATE_FORMAT),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur upload document', ['locataire_id' => $locataire->id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'upload. Veuillez réessayer.',
            ], 500);
        }
    }

    /**
     * Get all documents for a locataire.
     */
    public function getForLocataire(Locataire $locataire)
    {
        $documents = $locataire->documents()->latest()->get()->map(function ($doc) {
            return [
                'id' => $doc->id,
                'type' => $doc->type,
                'type_label' => $this->getTypeLabel($doc->type),
                'nom_original' => $doc->nom_original,
                'url' => get_secure_url($doc->chemin_fichier),
                'created_at' => $doc->created_at->format(self::DATE_FORMAT),
            ];
        });

        return response()->json([
            'success' => true,
            'documents' => $documents,
        ]);
    }

    /**
     * Get human-readable label for document type.
     */
    private function getTypeLabel($type)
    {
        $labels = [
            'cni' => 'Carte Nationale d\'Identité',
            'contrat_signe' => 'Contrat Signé',
            'attestation' => 'Attestation',
            'justificatif' => 'Justificatif',
            'autre' => 'Autre Document',
        ];

        return $labels[$type] ?? ucfirst($type);
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        return response()->json([
            'document' => [
                'id' => $document->id,
                'type' => $document->type,
                'type_label' => $this->getTypeLabel($document->type),
                'nom_original' => $document->nom_original,
                'url' => get_secure_url($document->chemin_fichier),
                'created_at' => $document->created_at->format(self::DATE_FORMAT),
            ],
        ]);
    }

    /**
     * Serve a document securely using a signed URL.
     */
    public function download(Request $request, string $path)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Lien expiré ou signature invalide.');
        }

        // On décode le chemin qui a été passé en paramètre
        $filePath = decrypt($path);

        if (! Storage::disk('public')->exists($filePath)) {
            abort(404, 'Fichier introuvable.');
        }

        // Log de consultation
        if (auth()->check()) {
            \App\Helpers\ActivityLogger::log(
                'Consultation Document',
                'Le document a été consulté : '.basename($filePath),
                'info'
            );
        }

        return Storage::disk('public')->response($filePath);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        try {
            // Supprimer le fichier du storage
            if (Storage::disk('public')->exists($document->chemin_fichier)) {
                Storage::disk('public')->delete($document->chemin_fichier);
            }

            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document supprimé avec succès',
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur suppression document', ['document_id' => $document->id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression. Veuillez réessayer.',
            ], 500);
        }
    }
}
