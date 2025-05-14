@extends('dashboard.layouts.main')

@section('content')
    <style>
        #map {
            height: 500px;
            margin-top: 20px;
        }

        .leaflet-container {
            z-index: 0;
        }

        .leaflet-draw {
            z-index: 1000 !important;
        }
    </style>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <h5>Tambah Data Lokasi Tanah Transmigrasi</h5>
                </div>
                <form action="{{ route('tanah.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="mt-3" for="nib">NIB</label>
                                <input type="text" class="form-control" id="nib" name="nib" required>
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="sertifikat">Tanggal Sertifikat</label>
                                <input type="date" class="form-control" id="sertifikat" name="sertifikat" required>
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="hak_milik">Hak Milik</label>
                                <input type="text" step="0.01" class="form-control" id="hak_milik" name="hak_milik"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="luas">Luas Tanah</label>
                                <input type="number" step="0.01" class="form-control" id="luas" name="luas"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="desa">Desa</label>
                                <input type="text" class="form-control" id="desa" name="desa" required>
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="kecamatan">Kecamatan</label>
                                <input type="text" class="form-control" id="kecamatan" name="kecamatan" required>
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="nama">Nama Pemilik</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="kadar_air">Kadar Air</label>
                                <input type="text" class="form-control" id="kadar_air" name="kadar_air"
                                    value="{{ old('kadar_air') }}" required>
                            </div>

                            <div class="form-group">
                                <label class="mt-3" for="lereng">Lereng</label>
                                <input type="text" class="form-control" id="lereng" name="lereng"
                                    value="{{ old('lereng') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="mt-3" for="nik">NIK</label>
                                <input type="text" class="form-control" id="nik" name="nik" required>
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="alamat">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" required></textarea>
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="tempat_lahir">Tempat Lahir</label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" required>
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="penggunaan_tanah">Penggunaan Tanah</label>
                                <input type="text" class="form-control" id="penggunaan_tanah" name="penggunaan_tanah"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="jenis_tanah">Jenis Tanah</label>
                                <input type="text" class="form-control" id="jenis_tanah" name="jenis_tanah" required>
                            </div>



                            <div class="form-group">
                                <label class="mt-3" for="rekomendasi_tanaman">Rekomendasi Tanaman</label>
                                <input type="text" class="form-control" id="rekomendasi_tanaman"
                                    name="rekomendasi_tanaman" value="{{ old('rekomendasi_tanaman') }}" required>
                            </div>

                            <div class="form-group">
                                <label class="mt-3" for="geojson">GeoJSON (Titik Lokasi)</label>
                                <textarea class="form-control" id="geojson" name="geojson" readonly required></textarea>
                                <small>GeoJSON akan dihasilkan otomatis setelah menggambar pada peta.</small>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Simpan Data</button>
                </form>
            </div>
        </div>

        <!-- Peta -->
        <div id="map" style="height: 500px;"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script src="https://unpkg.com/leaflet-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Base layers
            var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            });

            var satelliteLayer = L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles © Esri'
                }
            );

            // Inisialisasi awal peta
            var map = L.map('map', {
                center: [-7.2626, 106.9179], // fallback
                zoom: 15,
                layers: [osmLayer] // layer default
            });

            // Layer switcher
            var baseMaps = {
                "OpenStreetMap": osmLayer,
                "Citra Satelit": satelliteLayer
            };
            L.control.layers(baseMaps).addTo(map);

            // Layer GeoJSON untuk tanah
            fetch('/api/tanah-geojson')
                .then(response => response.json())
                .then(data => {
                    if (data && data.features && data.features.length > 0) {
                        const geojsonLayer = L.geoJSON(data, {
                            onEachFeature: function(feature, layer) {
                                const props = feature.properties;
                                layer.bindPopup(`
                                    <strong>${props.nama}</strong><br>
                                    NIK: ${props.nik}<br>
                                    Desa: ${props.desa}<br>
                                    Rekomendasi: ${props.rekomendasi_tanaman}
                                `);
                            },
                            style: function(feature) {
                                return {
                                    color: '#3388ff',
                                    weight: 3,
                                    opacity: 1
                                };
                            }
                        }).addTo(map);

                        // Zoom ke fitur
                        map.fitBounds(geojsonLayer.getBounds());
                    } else {
                        console.error('Data GeoJSON tidak valid');
                    }
                })
                .catch(error => {
                    console.error('Gagal memuat GeoJSON:', error);
                });

            // ====== Drawing ======
            var drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            var drawControl = new L.Control.Draw({
                draw: {
                    polygon: true,
                    polyline: false,
                    rectangle: true,
                    circle: false,
                    marker: true,
                    circlemarker: false
                },
                edit: {
                    featureGroup: drawnItems
                }
            });
            map.addControl(drawControl);

            // Event ketika menggambar selesai
            map.on('draw:created', function(e) {
                var layer = e.layer;
                drawnItems.addLayer(layer);

                // Konversi ke GeoJSON dan tampilkan di textarea (dengan id 'geojson')
                var geojson = drawnItems.toGeoJSON();
                var pretty = JSON.stringify(geojson, null, 2);
                var output = document.getElementById('geojson');
                if (output) output.value = pretty;
            });

            // ====== Geocoder (pencarian lokasi) ======
            if (typeof L.Control.Geocoder !== 'undefined') {
                L.Control.geocoder({
                        defaultMarkGeocode: false
                    })
                    .on('markgeocode', function(e) {
                        var bbox = e.geocode.bbox;
                        var poly = L.polygon([
                            bbox.getSouthEast(),
                            bbox.getNorthEast(),
                            bbox.getNorthWest(),
                            bbox.getSouthWest()
                        ]).addTo(map);
                        map.fitBounds(poly.getBounds());
                    })
                    .addTo(map);
            } else {
                console.warn("Geocoder tidak ditemukan. Pastikan sudah menyertakan plugin geocoder Leaflet.");
            }
        });
    </script>
@endsection
