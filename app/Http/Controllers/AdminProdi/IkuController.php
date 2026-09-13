<?php

namespace App\Http\Controllers\AdminProdi;

use App\Http\Controllers\Controller;
use App\Models\Iku;
use App\Models\Kategori;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class IkuController extends Controller
{
    public function index(Request $request)
    {
        $kategoriList = Kategori::orderBy('nama_kategori', 'asc')->get();
        $selectedKategori = $request->input('id_kategori');

        $search = $request->input('search');

        $iku = Iku::with('kategori')
            ->when($selectedKategori, function ($query, $selectedKategori) { //filter kategori
                return $query->where('id_kategori', $selectedKategori);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('kode_iku', 'like', "%{$search}%")
                      ->orWhere('nama_iku', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'asc')
            ->paginate(request('per_page', 10))
            ->appends(['id_kategori' => $selectedKategori, 'search' => $search]);

        return view('adminprodi.iku.index', compact('iku', 'kategoriList', 'selectedKategori', 'search'));
    }

    public function create()
    {
        $kategori = Kategori::all();
        return view('adminprodi.iku.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id',
            'kode_iku' => 'nullable|string|max:50|unique:iku,kode_iku',
            'nama_iku' => 'required|string|max:255|unique:iku,nama_iku',
            'deskripsi' => 'nullable|string',
        ]);

        $newIku = Iku::create($request->all());

        ActivityLog::log('Menambah data', 'Data IKU/IKT', 'Menambahkan IKU/IKT: ' . $newIku->nama_iku);

        if ($request->ajax() || $request->wantsJson()) {
            session()->flash('success', 'Data IKU/IKT berhasil ditambahkan.');
            return response()->json(['status' => 'success', 'message' => 'Data IKU/IKT berhasil ditambahkan.']);
        }

        return redirect()->route('adminprodi.iku.index')->with('success', 'Data IKU/IKT berhasil ditambahkan.');
    }

    public function edit(Iku $iku)
    {
        $kategori = Kategori::all();
        return view('adminprodi.iku.edit', compact('iku', 'kategori'));
    }

    public function update(Request $request, Iku $iku)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id',
            'kode_iku' => 'nullable|string|max:50|unique:iku,kode_iku,' . $iku->id,
            'nama_iku' => 'required|string|max:255|unique:iku,nama_iku,' . $iku->id,
            'deskripsi' => 'nullable|string',
        ]);

        $iku->update($request->all());

        ActivityLog::log('Mengubah data', 'Data IKU/IKT', 'Mengubah IKU/IKT: ' . $iku->nama_iku);

        if ($request->ajax() || $request->wantsJson()) {
            session()->flash('success', 'Data IKU/IKT berhasil diperbarui.');
            return response()->json(['status' => 'success', 'message' => 'Data IKU/IKT berhasil diperbarui.']);
        }

        return redirect()->route('adminprodi.iku.index')->with('success', 'Data IKU/IKT berhasil diperbarui.');
    }

    public function destroy(Iku $iku)
    {
        $ikuName = $iku->nama_iku;
        $iku->delete();

        ActivityLog::log('Menghapus data', 'Data IKU/IKT', 'Menghapus IKU/IKT: ' . $ikuName);

        return redirect()->route('adminprodi.iku.index')->with('success', 'Data IKU/IKT berhasil dihapus.');
    }
}
