<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use App\Models\StockMovement;
use App\Models\ProductCodeHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderBy('name')->paginate(15);
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id'  => 'required|exists:roles,id',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role_id'   => $request->role_id,
            'is_active' => true,
        ]);

        return redirect()->route('users.index')->with('success', 'Yeni kullanıcı oluşturuldu.');
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
        ]);

        // Guard: Admin cannot deactivate or demote themselves
        if ($user->id === Auth::id()) {
            $adminRole = Role::where('name', 'Admin')->first();
            $isBeingDemoted = $adminRole && $request->role_id != $adminRole->id;
            $isBeingDeactivated = !$request->boolean('is_active');

            if ($isBeingDemoted || $isBeingDeactivated) {
                return redirect()->route('users.index')
                    ->with('error', 'Kendi hesabınızın rolünü düşüremez veya devre dışı bırakamazsınız.');
            }
        }

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'role_id'   => $request->role_id,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Kullanıcı bilgileri güncellendi.');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Kendi hesabınızı silemezsiniz.');
        }

        DB::transaction(function () use ($user) {
            // Unlink foreign keys before hard deleting user to prevent SQL FK error
            AuditLog::where('user_id', $user->id)->update(['user_id' => null]);
            StockMovement::where('user_id', $user->id)->update(['user_id' => null]);
            ProductCodeHistory::where('changed_by', $user->id)->update(['changed_by' => null]);

            // Hard permanent delete from database
            $user->delete();
        });

        return redirect()->route('users.index')->with('success', 'Kullanıcı hesabı veritabanından kalıcı olarak silindi.');
    }
}
