<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SystemController extends Controller
{
    /**
     * Run migrations via web request (Protected by Token)
     * Usage: /system/migrate/YOUR_SECRET_TOKEN
     */
    public function migrate(Request $request)
    {
        // 1. Verify Token (timing-safe comparison)
        $token = $request->input('token');
        $deployToken = config('deploy.token');

        if (! $deployToken || ! hash_equals($deployToken, (string) $token)) {
            Log::warning("Tentative d'accès non autorisé à la route de migration.", [
                'ip' => $request->ip(),
                'user_id' => auth()->id()
            ]);
            abort(403, 'Accès refusé.');
        }

        // 2. Run Migration
        try {
            Log::info('Début de la migration via Web...');

            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            Log::info('Migration terminée.', ['output' => $output]);

            // 3. Clear Cache too (Good practice after deploy)
            Artisan::call('optimize:clear');
            Artisan::call('view:cache'); // Re-cache views
            $output .= "\n".Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Migration et Optimisation terminées.',
                'output' => $output,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur durant la migration Web : '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur critique durant la migration.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
