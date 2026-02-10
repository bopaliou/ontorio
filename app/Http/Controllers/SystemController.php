<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SystemController extends Controller
{
    /**
     * Run migrations via web request (strictly controlled fallback for constrained hosting).
     */
    public function migrate(Request $request)
    {
        if (! config('deploy.allow_web_migrate', false)) {
            abort(404);
        }

        $token = (string) $request->input('token', '');
        $deployToken = (string) config('deploy.token', '');

        if ($deployToken === '' || ! hash_equals($deployToken, $token)) {
            Log::warning('Tentative d\'accès non autorisé à la route de migration.', [
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
            ]);

            abort(403, 'Accès refusé.');
        }

        try {
            Log::info('Début de la migration via Web...');

            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:cache');

            Log::info('Migration terminée via Web.', [
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Migration et optimisation terminées.',
                'output' => $output,
            ]);
        } catch (\Throwable $e) {
            Log::error('Erreur durant la migration Web.', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur critique durant la migration.',
            ], 500);
        }
    }
}
