<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\EmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:users.view', only: ['index']),
            new Middleware('permission:users.create', only: ['create', 'store']),
            new Middleware('permission:users.edit', only: ['edit', 'update']),
            new Middleware('permission:users.delete', only: ['destroy']),
        ];
    }

    public function index()
    {
        $employees = Employee::with('user.roles')
            ->latest()
            ->paginate(10);

        return view('backend.employees.index', compact('employees'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['admin', 'manager', 'seller'])->get();

        return view('backend.employees.create', compact('roles'));
    }

    public function store(EmployeeRequest $request)
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($request->role);

            Employee::create([
                'user_id' => $user->id,
                'phone'   => $request->phone,
                'address' => $request->address,
                'salary'  => $request->salary ?? 0,
                'status'  => $request->status,
            ]);
        });

        return redirect()
            ->route('employees.index')
            ->with('success', 'Xodim muvaffaqiyatli qo\'shildi!');
    }

    public function edit(Employee $employee)
    {
        $roles = Role::whereIn('name', ['admin', 'manager', 'seller'])->get();

        return view('backend.employees.edit', compact('employee', 'roles'));
    }

    public function update(EmployeeRequest $request, Employee $employee)
    {
        DB::transaction(function () use ($request, $employee) {
            $userData = [
                'name'  => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $employee->user->update($userData);
            $employee->user->syncRoles([$request->role]);

            $employee->update([
                'phone'   => $request->phone,
                'address' => $request->address,
                'salary'  => $request->salary ?? 0,
                'status'  => $request->status,
            ]);
        });

        return redirect()
            ->route('employees.index')
            ->with('success', 'Xodim muvaffaqiyatli yangilandi!');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->user_id === auth()->id()) {
            return redirect()
                ->route('employees.index')
                ->with('error', 'O\'zingizni o\'chira olmaysiz!');
        }

        // user ni o‘chiramiz
        if ($employee->user) {
            $employee->user->delete();
        }

        // employee ni ham o‘chiramiz
        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Xodim o\'chirildi!');
    }

}
