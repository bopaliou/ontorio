<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Afficher la page de gestion des rôles et permissions
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0]; // Grouper par module
        });
        $users = User::with('roles')->orderBy('name')->get();

        return view('dashboard.sections.parametres', [
            'roles' => $roles,
            'permissions' => $permissions,
            'users' => $users,
        ]);
    }

    /**
     * Mettre à jour les permissions d'un rôle
     */
    public function updatePermissions(Request $request, Role $role)
    {
        // On ne peut pas modifier le rôle admin
        if ($role->name === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Les permissions du rôle Admin ne peuvent pas être modifiées.',
            ], 403);
        }

        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => 'Permissions mises à jour avec succès.',
        ]);
    }

    /**
     * Assigner un rôle à un utilisateur
     */
    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        // Retirer tous les rôles et assigner le nouveau
        $user->syncRoles([$validated['role']]);

        // Mettre à jour aussi le champ legacy 'role'
        $user->update(['role' => $validated['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Rôle assigné avec succès.',
        ]);
    }
}
