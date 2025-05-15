<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tanah_transmigrasis', function (Blueprint $table) {
            $table->id();
            $table->string('kecamatan');
            $table->string('kelurahan');
            $table->string('tipe_hak');
            $table->year('tahun');
            $table->string('nib', 16);
            $table->decimal('luas', 10, 2);
            $table->string('penggunaan');
            $table->string('jenis_tanah');
            $table->string('kadar_air');
            $table->string('lereng');
            $table->string('rekomendasi_tanaman');
            $table->json('geojson')->nullable(); // untuk simpan geometri dalam GeoJSON
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanah_transmigrasi');
    }
};
