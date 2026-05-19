<?php

namespace App\Http\Controllers;

use App\Models\Kabupaten;
use Illuminate\Http\Request;

class KabupatenController extends Controller
{
    public function index()
    {
        $kabupaten = Kabupaten::orderBy('id')->get();
        return view('admin.kabupaten.index', compact('kabupaten'));
    }

    public function create()
    {
        return view('admin.kabupaten.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kabupaten' => 'required|string|max:100|unique:kabupaten,nama_kabupaten'
        ]);

        Kabupaten::create([
            'nama_kabupaten' => $request->nama_kabupaten
        ]);

        return redirect()->route('admin.kabupaten.index')
            ->with('success', 'Kabupaten berhasil ditambahkan');
    }

    public function edit(Kabupaten $kabupaten)
    {
        return view('admin.kabupaten.edit', compact('kabupaten'));
    }

    public function update(Request $request, Kabupaten $kabupaten)
    {
        $request->validate([
            'nama_kabupaten' => 'required|string|max:100|unique:kabupaten,nama_kabupaten,' . $kabupaten->id
        ]);

        $kabupaten->update([
            'nama_kabupaten' => $request->nama_kabupaten
        ]);

        return redirect()->route('admin.kabupaten.index')
            ->with('success', 'Kabupaten berhasil diperbarui');
    }
}
