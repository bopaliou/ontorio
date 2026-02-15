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
        $this->authorize('documents.upload');

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

            // STOCKAGE SÉCURISÉ : On utilise le disque 'local' (privé) au lieu de 'public'
            $path = $file->storeAs(
                'documents/locataires/'.$locataire->id,
                $fileName,
                'local'
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
                'document' => $document->fresh(),
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
        $this->authorize('documents.view');

        $documents = $locataire->documents()->latest()->get()->map(function ($doc) {
            return [
                'id' => $doc->id,
                'type' => $doc->type,
                'type_label' => $this->getTypeLabel($doc->type),
                'nom_original' => $doc->nom_original,
                'extension' => $doc->extension,
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
                'extension' => $document->extension,
                'url' => get_secure_url($document->chemin_fichier),
                'created_at' => $document->created_at->format(self::DATE_FORMAT),
            ],
        ]);
    }

    /**
     * Serve a document securely using a signed URL.
     */
    public function download(Request $request)
    {
        if (! $request->hasValidSignature()) {
            \Log::warning('Signature invalide URL', [
                'full_url' => $request->fullUrl(),
                'signature' => $request->query('signature'),
                'app_url' => config('app.url')
            ]);
            abort(403, 'Lien expiré ou signature invalide.');
        }

        // On récupère le paramètre 'path' depuis la query string
        $encryptedPath = $request->query('path');
        
        if (!$encryptedPath) {
            abort(400, 'Paramètre manquant.');
        }

        // On décode le chemin qui a été passé en paramètre
        try {
            $filePath = ltrim(decrypt($encryptedPath), '/');
            $filePath = str_replace('\\', '/', $filePath);
            \Log::info('Tentative accès document', ['path' => $filePath]);
        } catch (\Exception $e) {
            \Log::error('Erreur décryptage chemin document', ['path' => $encryptedPath, 'error' => $e->getMessage()]);
            abort(400, 'Lien corrompu.');
        }

        // On vérifie d'abord sur le disque local (privé) puis sur le public (pour la rétrocompatibilité)
        $disk = 'local';
        $fullPathLocal = config('filesystems.disks.local.root') . '/' . $filePath;
        
        if (!file_exists($fullPathLocal)) {
            \Log::warning('Document non trouvé sur local (disk root)', ['fullPath' => $fullPathLocal]);
            if (Storage::disk('public')->exists($filePath)) {
                $disk = 'public';
                \Log::info('Document trouvé sur public (rétrocompatibilité)', ['path' => $filePath]);
            } else {
                \Log::error('Fichier introuvable sur tous les disques', ['path' => $filePath, 'tried_local' => $fullPathLocal]);
                abort(404, 'Fichier introuvable.');
            }
        }

        // Log de consultation
        if (auth()->check()) {
            \App\Helpers\ActivityLogger::log(
                'Consultation Document',
                'Le document a été consulté : '.basename($filePath),
                'info'
            );
        }

        $mimeType = Storage::disk($disk)->mimeType($filePath);

        // Fallback MIME type si non détecté
        if (!$mimeType) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $map = [
                'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png', 'gif' => 'image/gif',
                'webp' => 'image/webp', 'pdf' => 'application/pdf',
                'doc' => 'application/msword', 
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            $mimeType = $map[$ext] ?? 'application/octet-stream';
        }

        return Storage::disk($disk)->response($filePath, basename($filePath), [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.basename($filePath).'"',
            'X-Frame-Options' => 'SAMEORIGIN'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $this->authorize('documents.delete');

        try {
            // Supprimer le fichier du storage PRIVÉ
            if (Storage::disk('local')->exists($document->chemin_fichier)) {
                Storage::disk('local')->delete($document->chemin_fichier);
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
