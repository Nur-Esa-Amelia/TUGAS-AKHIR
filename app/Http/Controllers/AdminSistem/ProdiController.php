<?php

namespace App\Http\Controllers\AdminSistem;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ProdiController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $prodis = Prodi::query()
            ->when($search, function ($query, $search) {
                $query->where('nama_prodi', 'like', "%{$search}%")
                      ->orWhere('kode_prodi', 'like', "%{$search}%");
            })
            ->withCount('users')
            ->orderBy('nama_prodi')
            ->paginate(request('per_page', 10))
            ->withQueryString();

        return view('adminsistem.prodi.index', compact('prodis', 'search'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_prodi' => ['required', 'string', 'max:20', 'unique:prodi,kode_prodi'],
            'nama_prodi' => ['required', 'string', 'max:255'],
        ], [
            'kode_prodi.required' => 'Kode Program Studi wajib diisi.',
            'kode_prodi.unique' => 'Kode Program Studi sudah terdaftar.',
            'nama_prodi.required' => 'Nama Program Studi wajib diisi.',
        ]);

        $newProdi = Prodi::create($validated);

        ActivityLog::log('Menambah data', 'Kelola Program Studi', 'Menambahkan program studi: ' . $newProdi->nama_prodi);

        if ($request->ajax() || $request->wantsJson()) {
            session()->flash('success', 'Program Studi berhasil ditambahkan.');
            return response()->json(['status' => 'success', 'message' => 'Program Studi berhasil ditambahkan.']);
        }

        return redirect()->route('adminsistem.prodi.index')
            ->with('success', 'Program Studi berhasil ditambahkan.');
    }


    public function update(Request $request, Prodi $prodi)
    {
        $validated = $request->validate([
            'kode_prodi' => ['required', 'string', 'max:20', "unique:prodi,kode_prodi,{$prodi->id}"],
            'nama_prodi' => ['required', 'string', 'max:255'],
        ], [
            'kode_prodi.required' => 'Kode Program Studi wajib diisi.',
            'kode_prodi.unique' => 'Kode Program Studi sudah terdaftar.',
            'nama_prodi.required' => 'Nama Program Studi wajib diisi.',
        ]);

        $prodi->update($validated);

        ActivityLog::log('Mengubah data', 'Kelola Program Studi', 'Mengubah data program studi: ' . $prodi->nama_prodi);

        if ($request->ajax() || $request->wantsJson()) {
            session()->flash('success', 'Program Studi berhasil diperbarui.');
            return response()->json(['status' => 'success', 'message' => 'Program Studi berhasil diperbarui.']);
        }

        return redirect()->route('adminsistem.prodi.index')
            ->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy(Prodi $prodi)
    {
        // Keamanan opsional: batasi penghapusan jika ada user yang terikat ke prodi ini, atau tangani cascade/set null
        // Mari kita hapus langsung, user yang terikat dengannya akan memiliki prodi_id bernilai null karena onDelete('set null') pada migrasi.
        $prodiName = $prodi->nama_prodi;
        $prodi->delete();

        ActivityLog::log('Menghapus data', 'Kelola Program Studi', 'Menghapus program studi: ' . $prodiName);

        return redirect()->route('adminsistem.prodi.index')
            ->with('success', 'Program Studi berhasil dihapus.');
    }
}
