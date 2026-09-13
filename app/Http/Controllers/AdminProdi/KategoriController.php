<?php

namespace App\Http\Controllers\AdminProdi;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $kategori = Kategori::when($search, function ($query, $search) {
                return $query->where('nama_kategori', 'like', "%{$search}%")
                             ->orWhere('deskripsi', 'like', "%{$search}%");
            })
            ->orderBy('id', 'asc')
            ->paginate(request('per_page', 10))
            ->appends(['search' => $search]);
            
        return view('adminprodi.kategori.index', compact('kategori', 'search'));
    }

    public function create()
    {
        return view('adminprodi.kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori = Kategori::create($request->all());

        ActivityLog::log('Menambah data', 'Kategori IKU/IKT', 'Menambahkan kategori: ' . $kategori->nama_kategori);

        if ($request->ajax() || $request->wantsJson()) {
            session()->flash('success', 'Kategori berhasil ditambahkan.');
            return response()->json(['status' => 'success', 'message' => 'Kategori berhasil ditambahkan.']);
        }

        return redirect()->route('adminprodi.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        return view('adminprodi.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $kategori->id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update($request->all());

        ActivityLog::log('Mengubah data', 'Kategori IKU/IKT', 'Mengubah kategori: ' . $kategori->nama_kategori);

        if ($request->ajax() || $request->wantsJson()) {
            session()->flash('success', 'Kategori berhasil diperbarui.');
            return response()->json(['status' => 'success', 'message' => 'Kategori berhasil diperbarui.']);
        }

        return redirect()->route('adminprodi.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->iku()->exists()) {
            return redirect()->route('adminprodi.kategori.index')->with('error', 'Kategori gagal dihapus karena masih digunakan oleh data IKU.');
        }

        $namaKategori = $kategori->nama_kategori;
        $kategori->delete();

        ActivityLog::log('Menghapus data', 'Kategori IKU/IKT', 'Menghapus kategori: ' . $namaKategori);

        return redirect()->route('adminprodi.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
