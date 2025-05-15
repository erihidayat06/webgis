<?php

namespace App\Http\Controllers;

use App\Models\TanahTransmigrasi;
use Illuminate\Http\Request;

class TanahController extends Controller
{
    public function index()
    {
        $tanahs = TanahTransmigrasi::latest()->get();
        return view('dashboard.geojson.index', compact('tanahs'));
    }

    /**
     * Tampilkan form untuk input data tanah transmigrasi
     */
    public function create()
    {
        return view('dashboard.geojson.create');
    }

    /**
     * Simpan data tanah transmigrasi ke database
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'nib' => 'required|string|max:10',
            'luas' => 'required|numeric',
            'sertifikat' => 'required|string|max:255',
            'hak_milik' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'nik' => 'required|string',
            'alamat' => 'required|string',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'penggunaan_tanah' => 'required|string|max:255',
            'jenis_tanah' => 'required|string|max:255',
            'kadar_air' => 'required|string|max:255',
            'lereng' => 'required|string|max:255',
            'rekomendasi_tanaman' => 'required|string|max:255',
            'geojson' => 'required|json', // Harus valid JSON
        ]);

        TanahTransmigrasi::create($validated);

        return redirect()->route('tanah.index')->with('success', 'Data tanah berhasil disimpan!');
    }

    public function edit($id)
    {
        $tanah = TanahTransmigrasi::findOrFail($id);
        return view('dashboard.geojson.edit', compact('tanah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([

            'nib' => 'required|string|max:10',
            'luas' => 'required|numeric',
            'sertifikat' => 'required|string|max:255',
            'hak_milik' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'nik' => 'required|string',
            'alamat' => 'required|string',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'penggunaan_tanah' => 'required|string|max:255',
            'jenis_tanah' => 'required|string|max:255',
            'kadar_air' => 'required|string|max:255',
            'lereng' => 'required|string|max:255',
            'rekomendasi_tanaman' => 'required|string|max:255',
            'geojson' => 'required|json', // Harus valid JSON
        ]);

        $tanah = TanahTransmigrasi::findOrFail($id);
        $tanah->update($request->all());

        return redirect()->route('tanah.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $tanah = TanahTransmigrasi::findOrFail($id);
        $tanah->delete();

        return redirect()->route('tanah.index')->with('success', 'Data berhasil dihapus!');
    }

    // Method untuk mengembalikan data GeoJSON
    public function geojson()
    {
        // Ambil semua data GeoJSON dari database
        $tanah = TanahTransmigrasi::all();

        // Format data GeoJSON
        $geoJsonData = $tanah->map(function ($item) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'kecamatan' => $item->kecamatan,
                    'desa' => $item->kelurahan,
                    'hak_milik' => $item->tipe_hak,
                    'tahun' => $item->tahun,
                    'luas' => $item->luas,
                    'nib' => $item->nib,
                    'penggunaan_tanah' => $item->penggunaan,
                    'jenis_tanah' => $item->jenis_tanah,
                    'kadar_air' => $item->kadar_air,
                    'lereng' => $item->lereng,
                    'rekomendasi_tanaman' => $item->rekomendasi_tanaman,
                ],
                'geometry' => json_decode($item->geojson), // Pastikan kolom geojson disimpan dalam format JSON
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $geoJsonData
        ]);
    }
}
