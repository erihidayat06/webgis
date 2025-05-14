<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tanah_transmigrasis', function (Blueprint $table) {
            $table->id();
            $table->string('nib', 10);
            $table->decimal('luas', 10, 2);
            $table->date('sertifikat');
            $table->string('hak_milik');
            $table->string('desa');
            $table->string('kecamatan');
            $table->string('nama');
            $table->string('nik', 16);
            $table->text('alamat');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('penggunaan_tanah');
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
