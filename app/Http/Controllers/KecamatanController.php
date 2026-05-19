<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kecamatan;
use App\Models\Kabupaten;

class KecamatanController extends Controller
{
    public function index()
    {
        $kecamatan = Kecamatan::with('kabupaten')->get();
        return view('admin.kecamatan.index', compact('kecamatan'));
    }

    public function create()
    {
        $kabupaten = Kabupaten::orderBy('id')->get();
        return view('admin.kecamatan.create', compact('kabupaten'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'nama_kecamatan' => 'required|string|max:255',
        ]);

        Kecamatan::create($request->all());

        return redirect()->route('admin.kecamatan.index')
                         ->with('success', 'Kecamatan berhasil ditambahkan.');
    }

    public function edit(Kecamatan $kecamatan)
    {
        $kabupaten = Kabupaten::orderBy('id')->get();
        return view('admin.kecamatan.edit', compact('kecamatan', 'kabupaten'));
    }

    public function update(Request $request, Kecamatan $kecamatan)
    {
        $request->validate([
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'nama_kecamatan' => 'required|string|max:255',
        ]);

        $kecamatan->update($request->all());

        return redirect()->route('admin.kecamatan.index')
                         ->with('success', 'Kecamatan berhasil diperbarui.');
    }

    public function destroy(Kecamatan $kecamatan)
    {
        $kecamatan->delete();

        return redirect()->route('admin.kecamatan.index')
                         ->with('success', 'Kecamatan berhasil dihapus.');
    }
}
