<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:roles.view')->only(['index', 'show']);
        $this->middleware('permission:roles.create')->only('store');
        $this->middleware('permission:roles.edit')->only('update');
        $this->middleware('permission:roles.delete')->only('destroy');
        $this->middleware('permission:permissions.assign')->only(['assignPermissions', 'syncPermissions']);
    }

    /**
     * Get all roles
     */
    public function index(Request $request)
    {
        $roles = Role::with('permissions')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->get();

        return response()->json($roles);
    }

    /**
     * Create new role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role->load('permissions'),
        ], 201);
    }

    /**
     * Get role details
     */
    public function show(Role $role)
    {
        return response()->json([
            'role' => $role->load('permissions'),
        ]);
    }

    /**
     * Update role
     */
    public function update(Request $request, Role $role)
    {
        // Prevent updating default roles
        if (in_array($role->name, ['admin', 'manager', 'seller'])) {
            return response()->json([
                'message' => 'Cannot update default roles',
            ], 403);
        }

        $request->validate([
            'name' => ['sometimes', 'string', Rule::unique('roles')->ignore($role->id)],
        ]);

        $role->update($request->only('name'));

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role->load('permissions'),
        ]);
    }

    /**
     * Delete role
     */
    public function destroy(Role $role)
    {
        // Prevent deleting default roles
        if (in_array($role->name, ['admin', 'manager', 'seller'])) {
            return response()->json([
                'message' => 'Cannot delete default roles',
            ], 403);
        }

        // Check if role is assigned to any user
        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete role that is assigned to users',
            ], 403);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
        ]);
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->givePermissionTo($request->permissions);

        return response()->json([
            'message' => 'Permissions assigned successfully',
            'role' => $role->load('permissions'),
        ]);
    }

    /**
     * Sync permissions to role (replace all)
     */
    public function syncPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Permissions synchronized successfully',
            'role' => $role->load('permissions'),
        ]);
    }

    /**
     * Remove permission from role
     */
    public function removePermission(Request $request, Role $role)
    {
        $request->validate([
            'permission' => 'required|exists:permissions,name',
        ]);

        $role->revokePermissionTo($request->permission);

        return response()->json([
            'message' => 'Permission removed successfully',
            'role' => $role->load('permissions'),
        ]);
    }

    /**
     * Get all permissions
     */
    public function permissions(Request $request)
    {
        $permissions = Permission::when($request->search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })
            ->get()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

        return response()->json($permissions);
    }

    /**
     * Get role statistics
     */
    public function statistics()
    {
        $roles = Role::withCount('users')->get();

        return response()->json([
            'total_roles' => $roles->count(),
            'roles' => $roles,
        ]);
    }
}
