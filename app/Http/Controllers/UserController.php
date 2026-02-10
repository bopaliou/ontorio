<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const ERROR_GENERIC = 'Une erreur est survenue. Veuillez réessayer.';
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|exists:roles,name',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            // Sync Spatie Role
            $user->assignRole($request->role);

            ActivityLogger::log('Création Utilisateur', "Création de l'utilisateur {$user->name} ({$user->role})", 'success', $user);

            return response()->json(['success' => true, 'message' => 'Utilisateur créé avec succès !']);
        } catch (\Exception $e) {
            \Log::error('Erreur création utilisateur', ['error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => self::ERROR_GENERIC], 500);
        }
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|exists:roles,name',
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            // Sync Spatie Role
            $user->syncRoles([$request->role]);

            ActivityLogger::log('Modification Utilisateur', "Mise à jour de l'utilisateur {$user->name}", 'info', $user);

            return response()->json(['success' => true, 'message' => 'Utilisateur mis à jour !']);
        } catch (\Exception $e) {
            \Log::error('Erreur modification utilisateur', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => self::ERROR_GENERIC], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 403);
        }

        try {
            $nom = $user->name;
            $user->delete();

            ActivityLogger::log('Suppression Utilisateur', "Suppression de l'utilisateur {$nom}", 'warning');

            return response()->json(['success' => true, 'message' => 'Utilisateur supprimé !']);
        } catch (\Exception $e) {
            \Log::error('Erreur suppression utilisateur', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => self::ERROR_GENERIC], 500);
        }
    }

    /**
     * Display a simple users list (used by tests / admin panel).
     */
    public function index()
    {
        $users = User::orderBy('name')->get();

        // For tests, returning a simple JSON payload is enough
        return response()->json(['data' => $users]);
    }
}
