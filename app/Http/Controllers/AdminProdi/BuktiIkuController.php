<?php

namespace App\Http\Controllers\AdminProdi;

use App\Http\Controllers\Controller;
use App\Models\BuktiIku;
use App\Models\Iku;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class BuktiIkuController extends Controller
{
    public function index(Request $request)
    {
        $ikuList = Iku::orderBy('nama_iku', 'asc')->get();
        $selectedIku = $request->input('id_iku');
        $search = $request->input('search');

        $bukti = BuktiIku::with('iku')
            ->when($selectedIku, function ($query, $selectedIku) {
                return $query->where('id_iku', $selectedIku);
            })
            ->when($search, function ($query, $search) {
                return $query->where('nama_bukti', 'like', "%{$search}%")
                             ->orWhere('deskripsi', 'like', "%{$search}%");
            })
            ->orderBy('id', 'asc')
            ->paginate(request('per_page', 10))
            ->appends(['id_iku' => $selectedIku, 'search' => $search]);

        return view('adminprodi.bukti.index', compact('bukti', 'ikuList', 'selectedIku', 'search'));
    }

    public function create()
    {
        $iku = Iku::all();
        return view('adminprodi.bukti.create', compact('iku'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_iku' => 'required|exists:iku,id',
            'nama_bukti' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $newBukti = BuktiIku::create($request->all());

        ActivityLog::log('Menambah data', 'Jenis Bukti IKU/IKT', 'Menambahkan Jenis Bukti: ' . $newBukti->nama_bukti);

        if ($request->ajax() || $request->wantsJson()) {
            session()->flash('success', 'Jenis Bukti IKU/IKT berhasil ditambahkan.');
            return response()->json(['status' => 'success', 'message' => 'Jenis Bukti IKU/IKT berhasil ditambahkan.']);
        }

        return redirect()->route('adminprodi.bukti.index')->with('success', 'Jenis Bukti IKU/IKT berhasil ditambahkan.');
    }

    public function edit(BuktiIku $bukti)
    {
        $iku = Iku::all();
        return view('adminprodi.bukti.edit', compact('bukti', 'iku'));
    }

    public function update(Request $request, BuktiIku $bukti)
    {
        $request->validate([
            'id_iku' => 'required|exists:iku,id',
            'nama_bukti' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $bukti->update($request->all());

        ActivityLog::log('Mengubah data', 'Jenis Bukti IKU/IKT', 'Mengubah Jenis Bukti: ' . $bukti->nama_bukti);

        if ($request->ajax() || $request->wantsJson()) {
            session()->flash('success', 'Jenis Bukti IKU/IKT berhasil diperbarui.');
            return response()->json(['status' => 'success', 'message' => 'Jenis Bukti IKU/IKT berhasil diperbarui.']);
        }

        return redirect()->route('adminprodi.bukti.index')->with('success', 'Jenis Bukti IKU/IKT berhasil diperbarui.');
    }

    public function destroy(BuktiIku $bukti)
    {
        $namaBukti = $bukti->nama_bukti;
        $bukti->delete();

        ActivityLog::log('Menghapus data', 'Jenis Bukti IKU/IKT', 'Menghapus Jenis Bukti: ' . $namaBukti);

        return redirect()->route('adminprodi.bukti.index')->with('success', 'Jenis Bukti IKU/IKT berhasil dihapus.');
    }
}
