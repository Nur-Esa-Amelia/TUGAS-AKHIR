<?php

namespace App\Http\Controllers\AdminSistem;

use App\Http\Controllers\Controller;
use App\Models\AiPrompt;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PromptAiController extends Controller
{
    public function index()
    {
        $prompts = AiPrompt::latest()->get();
        return view('adminsistem.prompt_ai.index', compact('prompts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'prompt_template' => 'required|string',
            'status'          => 'required|in:aktif,nonaktif',
            'keterangan'      => 'nullable|string|max:500',
        ]);

        // Jika status aktif, nonaktifkan yang lain terlebih dahulu
        if ($request->status === 'aktif') {
            AiPrompt::where('status', 'aktif')->update(['status' => 'nonaktif']);
        }

        AiPrompt::create($request->only('name', 'prompt_template', 'status', 'keterangan'));

        ActivityLog::log('Menambahkan Prompt AI', 'Prompt AI', "Menambahkan prompt '{$request->name}' dengan status {$request->status}");

        return redirect()->back()->with('success', 'Template prompt AI berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $prompt = AiPrompt::findOrFail($id);

        $request->validate([
            'name'            => 'required|string|max:255',
            'prompt_template' => 'required|string',
            'status'          => 'required|in:aktif,nonaktif',
            'keterangan'      => 'nullable|string|max:500',
        ]);

        // Jika status aktif, nonaktifkan yang lain terlebih dahulu
        if ($request->status === 'aktif') {
            AiPrompt::where('status', 'aktif')->where('id', '!=', $id)->update(['status' => 'nonaktif']);
        }

        $prompt->update($request->only('name', 'prompt_template', 'status', 'keterangan'));

        ActivityLog::log('Mengubah Prompt AI', 'Prompt AI', "Mengubah prompt '{$request->name}'");

        return redirect()->back()->with('success', 'Template prompt AI berhasil diperbarui.');
    }

    public function activate($id)
    {
        // Nonaktifkan semua prompt lainnya
        AiPrompt::where('status', 'aktif')->update(['status' => 'nonaktif']);

        $prompt = AiPrompt::findOrFail($id);
        $prompt->update(['status' => 'aktif']);

        ActivityLog::log('Mengaktifkan Prompt AI', 'Prompt AI', "Mengaktifkan prompt '{$prompt->name}'");

        return redirect()->back()->with('success', "Prompt '{$prompt->name}' berhasil diaktifkan.");
    }

    public function destroy($id)
    {
        $prompt = AiPrompt::findOrFail($id);
        $name = $prompt->name;
        $prompt->delete();

        ActivityLog::log('Menghapus Prompt AI', 'Prompt AI', "Menghapus prompt '{$name}'");

        return redirect()->back()->with('success', 'Template prompt AI berhasil dihapus.');
    }
}
