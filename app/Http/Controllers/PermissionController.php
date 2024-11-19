<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionController extends Controller
{
    // Listar todos los permisos
    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    // Crear un nuevo permiso
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions',
        ]);

        $permission = Permission::create(['name' => $request->name]);

        return response()->json([
            'message' => 'Permiso creado exitosamente.',
            'permission' => $permission,
        ], 201);
    }

    // Actualizar un permiso existente
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return response()->json([
            'message' => 'Permiso actualizado exitosamente.',
            'permission' => $permission,
        ]);
    }

    // Eliminar un permiso
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json([
            'message' => 'Permiso eliminado exitosamente.',
        ]);
    }

    // Asignar un permiso a un rol
    public function assignToRole(Request $request, Role $role)
    {
        $request->validate([
            'permission' => 'required|exists:permissions,name',
        ]);

        $permission = Permission::where('name', $request->permission)->first();
        $role->givePermissionTo($permission);

        return response()->json([
            'message' => "Permiso '{$permission->name}' asignado al rol '{$role->name}' exitosamente.",
        ]);
    }

    // Asignar un permiso a un usuario
    public function assignToUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $request->validate([
            'permission' => 'required|exists:permissions,name',
        ]);

        $permission = Permission::where('name', $request->permission)->first();
        $user->givePermissionTo($permission);

        return response()->json([
            'message' => "Permiso '{$permission->name}' asignado al usuario '{$user->name}' exitosamente.",
        ]);
    }
}
