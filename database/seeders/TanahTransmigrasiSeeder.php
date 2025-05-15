<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TanahTransmigrasi;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TanahTransmigrasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $json = File::get(storage_path('app/tanah.json'));
        $data = json_decode($json, true);

        foreach ($data['features'] as $feature) {
            $prop = $feature['properties'];
            $geojson = json_encode([
                'type' => $feature['geometry']['type'],
                'coordinates' => $feature['geometry']['coordinates'],
            ]);

            TanahTransmigrasi::create([
                'kecamatan' => $prop['KECAMATAN'],
                'kelurahan' => $prop['KELURAHAN'],
                'tipe_hak' => $prop['TIPEHAK'],
                'tahum' => intval($prop['TAHUN']),
                'nib' => $prop['NIB'],
                'luas' => $prop['LUASTERTUL'],
                'penggunaan' => $prop['Penggunaan'],
                'jenis_tanah' => $prop['J_Tanah'],
                'kadar_air' => $prop['Kadar_Air'],
                'lereng' => $prop['Lereng'],
                'rekomendasi_tanaman' => $prop['Rekomen'],
                'geojson' => $geojson,
            ]);
        }
    }
}
