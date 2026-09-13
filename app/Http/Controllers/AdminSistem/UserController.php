<?php

namespace App\Http\Controllers\AdminSistem;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Prodi;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $prodiId = $request->input('prodi_id');

        $users = User::query()
            ->with('prodi')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->when($prodiId, function ($query, $prodiId) {
                $query->where('prodi_id', $prodiId);
            })
            ->orderBy('name')
            ->paginate(request('per_page', 10))
            ->withQueryString();

        $prodis = Prodi::orderBy('nama_prodi')->get();
        $roles = [
            'admin_sistem' => 'Admin Sistem',
            'admin_p2mp' => 'Admin P2MP',
            'admin_prodi' => 'Admin Prodi',
            'kaprodi' => 'Kaprodi',
            'dosen' => 'Dosen',
        ];

        return view('adminsistem.users.index', compact('users', 'prodis', 'roles', 'search', 'role', 'prodiId'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin_sistem,admin_p2mp,admin_prodi,kaprodi,dosen'],
            'prodi_id' => ['required_unless:role,admin_p2mp,admin_sistem', 'nullable', 'exists:prodi,id'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'prodi_id.required_unless' => 'Program Studi wajib dipilih untuk role selain Admin P2MP dan Admin Sistem.',
            'prodi_id.exists' => 'Program Studi tidak valid.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'prodi_id' => in_array($request->role, ['admin_p2mp', 'admin_sistem']) ? null : $request->prodi_id,
        ];

        $newUser = User::create($data);

        ActivityLog::log('Menambah data', 'Kelola User', 'Menambahkan user baru: ' . $newUser->name);

        if ($request->ajax() || $request->wantsJson()) {
            session()->flash('success', 'User berhasil ditambahkan.');
            return response()->json(['status' => 'success', 'message' => 'User berhasil ditambahkan.']);
        }

        return redirect()->route('adminsistem.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:admin_sistem,admin_p2mp,admin_prodi,kaprodi,dosen'],
            'prodi_id' => ['required_unless:role,admin_p2mp,admin_sistem', 'nullable', 'exists:prodi,id'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'prodi_id.required_unless' => 'Program Studi wajib dipilih untuk role selain Admin P2MP dan Admin Sistem.',
            'prodi_id.exists' => 'Program Studi tidak valid.',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->prodi_id = in_array($request->role, ['admin_p2mp', 'admin_sistem']) ? null : $request->prodi_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        ActivityLog::log('Mengubah data', 'Kelola User', 'Mengubah data user: ' . $user->name);

        if ($request->ajax() || $request->wantsJson()) {
            session()->flash('success', 'User berhasil diperbarui.');
            return response()->json(['status' => 'success', 'message' => 'User berhasil diperbarui.']);
        }

        return redirect()->route('adminsistem.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Cegah menghapus akun sendiri
        if (auth()->id() === $user->id) {
            return redirect()->route('adminsistem.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::log('Menghapus data', 'Kelola User', 'Menghapus data user: ' . $userName);

        return redirect()->route('adminsistem.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
