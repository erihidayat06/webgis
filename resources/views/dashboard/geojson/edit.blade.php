@extends('dashboard.layouts.main')

@section('content')
    <style>
        #map {
            height: 500px;
            margin-top: 20px;
        }
    </style>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <h5>Edit Data Tanah</h5>
                <form action="{{ route('tanah.update', $tanah->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="mt-3" for="nib">NIB</label>
                                <input type="text" class="form-control @error('nib') is-invalid @enderror" id="nib"
                                    name="nib" value="{{ old('nib', $tanah->nib ?? '') }}" required>
                                @error('nib')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="tahun">Tanggal tahun</label>
                                <input type="number" class="form-control @error('tahun') is-invalid @enderror"
                                    id="tahun" name="tahun" value="{{ old('tahun', $tanah->tahun ?? '') }}" required>
                                @error('tahun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="tipe_hak">Tipe Hak</label>
                                <input type="text" step="0.01"
                                    class="form-control @error('tipe_hak') is-invalid @enderror" id="tipe_hak"
                                    name="tipe_hak" value="{{ old('tipe_hak', $tanah->tipe_hak ?? '') }}" required>
                                @error('tipe_hak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="luas">Luas Tanah</label>
                                <input type="number" step="0.01"
                                    class="form-control @error('luas') is-invalid @enderror" id="luas" name="luas"
                                    value="{{ old('luas', $tanah->luas ?? '') }}" required>
                                @error('luas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="kelurahan">kelurahan</label>
                                <input type="text" class="form-control @error('kelurahan') is-invalid @enderror"
                                    id="kelurahan" name="kelurahan" value="{{ old('kelurahan', $tanah->kelurahan ?? '') }}"
                                    required>
                                @error('kelurahan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="kecamatan">Kecamatan</label>
                                <input type="text" class="form-control @error('kecamatan') is-invalid @enderror"
                                    id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $tanah->kecamatan ?? '') }}"
                                    required>
                                @error('kecamatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="kadar_air">Kadar Air</label>
                                <input type="text" class="form-control @error('kadar_air') is-invalid @enderror"
                                    id="kadar_air" name="kadar_air" value="{{ old('kadar_air', $tanah->kadar_air ?? '') }}"
                                    required>
                                @error('kadar_air')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="lereng">Lereng</label>
                                <input type="text" class="form-control @error('lereng') is-invalid @enderror"
                                    id="lereng" name="lereng" value="{{ old('lereng', $tanah->lereng ?? '') }}"
                                    required>
                                @error('lereng')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="mt-3" for="penggunaan">Penggunaan Tanah</label>
                                <input type="text" class="form-control @error('penggunaan') is-invalid @enderror"
                                    id="penggunaan" name="penggunaan"
                                    value="{{ old('penggunaan', $tanah->penggunaan ?? '') }}" required>
                                @error('penggunaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="mt-3" for="jenis_tanah">Jenis Tanah</label>
                                <input type="text" class="form-control @error('jenis_tanah') is-invalid @enderror"
                                    id="jenis_tanah" name="jenis_tanah"
                                    value="{{ old('jenis_tanah', $tanah->jenis_tanah ?? '') }}" required>
                                @error('jenis_tanah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="rekomendasi_tanaman">Rekomendasi Tanaman</label>
                                <input type="text"
                                    class="form-control @error('rekomendasi_tanaman') is-invalid @enderror"
                                    id="rekomendasi_tanaman" name="rekomendasi_tanaman"
                                    value="{{ old('rekomendasi_tanaman', $tanah->rekomendasi_tanaman ?? '') }}" required>
                                @error('rekomendasi_tanaman')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="geojson">GeoJSON (Titik Lokasi)</label>
                                <textarea class="form-control @error('geojson') is-invalid @enderror" name="geojson" id="geojson" readonly
                                    required>{{ old('geojson', $tanah->geojson ?? '') }}</textarea>
                                @error('geojson')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>



                    <button type="submit" class="btn btn-success">Update Data</button>
                </form>
            </div>
        </div>

        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script src="https://unpkg.com/leaflet-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            });

            var satellite = L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: '© Esri'
                });

            var map = L.map('map', {
                center: [-7.2626, 106.9179], // default center
                zoom: 15,
                layers: [osm] // default layer
            });

            var baseLayers = {
                "Peta Biasa": osm,
                "Citra Satelit": satellite
            };

            L.control.layers(baseLayers).addTo(map); // Add the layer switcher

            var drawnItems = new L.FeatureGroup().addTo(map);

            @if ($tanah->geojson)
                var existingGeoJson = {!! $tanah->geojson !!};
                L.geoJSON(existingGeoJson).eachLayer(function(layer) {
                    drawnItems.addLayer(layer);
                    map.fitBounds(layer.getBounds());
                });
            @endif

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

            map.on('draw:created', function(e) {
                drawnItems.clearLayers();
                drawnItems.addLayer(e.layer);

                var geojson = drawnItems.toGeoJSON();
                document.getElementById('geojson').value = JSON.stringify(geojson, null, 2);
            });

            map.on('draw:edited', function() {
                var geojson = drawnItems.toGeoJSON();
                document.getElementById('geojson').value = JSON.stringify(geojson, null, 2);
            });

            if (typeof L.Control.Geocoder !== 'undefined') {
                L.Control.geocoder({
                        defaultMarkGeocode: false
                    })
                    .on('markgeocode', function(e) {
                        var bbox = e.geocode.bbox;
                        map.fitBounds(bbox);
                    }).addTo(map);
            }
        });
    </script>
@endsection
