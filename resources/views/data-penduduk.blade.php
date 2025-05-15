@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <div class="container py-4">
        <h5 class="mb-4">Daftar Tanah Transmigrasi</h5>

        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control"
                placeholder="Cari berdasarkan NIB, Desa, atau Jenis Tanah">
        </div>


        <div class="row g-4">
            @forelse ($tanahs as $tanah)
                <div class="col-md-6 col-lg-4 tanah-card" data-kecamatan="{{ strtolower($tanah->kecamatan) }}"
                    data-desa="{{ strtolower($tanah->kelurahan) }}" data-nib="{{ strtolower($tanah->nib) }}"
                    data-rekom="{{ strtolower($tanah->rekomendasi_tanaman) }}">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">

                            <table class="table table-borderless table-sm mb-3">
                                <tbody>
                                    <tr>
                                        <th scope="row">Kecamatan</th>
                                        <td>: {{ $tanah->kecamatan }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Kelurahan</th>
                                        <td>: {{ $tanah->kelurahan }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tipe Hak</th>
                                        <td>: {{ $tanah->tipe_hak }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tahun</th>
                                        <td>: {{ date('Y', strtotime($tanah->tahun)) }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">NIB</th>
                                        <td>: {{ $tanah->nib }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Luas Terdaftar</th>
                                        <td>: {{ number_format($tanah->luas, 2) }} m²</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Penggunaan</th>
                                        <td>: {{ $tanah->penggunaan }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Jenis Tanah</th>
                                        <td>: {{ $tanah->jenis_tanah }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Kadar Air</th>
                                        <td>: {{ $tanah->kadar_air }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Lereng</th>
                                        <td>: {{ $tanah->lereng }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Rekomendasi</th>
                                        <td>: {{ $tanah->rekomendasi_tanaman }}</td>
                                    </tr>



                                </tbody>
                            </table>


                            <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal"
                                data-bs-target="#mapModal{{ $tanah->id }}">
                                Lihat Peta
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Peta -->
                <div class="modal fade" id="mapModal{{ $tanah->id }}" tabindex="-1" data-id="{{ $tanah->id }}"
                    aria-labelledby="mapModalLabel{{ $tanah->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mapModalLabel{{ $tanah->id }}">
                                    Lokasi Tanah - {{ $tanah->nama }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="map{{ $tanah->id }}" style="height: 400px;"></div>
                                <script type="application/json" id="geojson-data-{{ $tanah->id }}">
                                    {!! $tanah->geojson !!}
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center">Belum ada data tanah.</div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const cards = document.querySelectorAll('.tanah-card');

            cards.forEach(card => {
                const kecamatan = card.dataset.kecamatan;
                const desa = card.dataset.desa;
                const nib = card.dataset.nib;
                const rekom = card.dataset.rekom;

                const isMatch = kecamatan.includes(query) || desa.includes(query) || nib.includes(query) ||
                    rekom.includes(query);
                card.style.display = isMatch ? 'block' : 'none';
            });
        });
    </script>


    <script>
        const leafletMaps = {};

        document.addEventListener('DOMContentLoaded', function() {
            const modals = document.querySelectorAll('.modal');

            modals.forEach(function(modal) {
                modal.addEventListener('shown.bs.modal', function() {
                    const id = modal.getAttribute('data-id');
                    const mapId = `map${id}`;
                    const geojsonEl = document.getElementById(`geojson-data-${id}`);
                    if (!geojsonEl || leafletMaps[id]) return;

                    const geojson = JSON.parse(geojsonEl.textContent);
                    const mapContainer = document.getElementById(mapId);
                    mapContainer.innerHTML = '';

                    // Ambil koordinat pertama dari fitur pertama
                    let latlng = [-7.2626, 106.9179]; // fallback koordinat default

                    if (geojson.features && geojson.features.length > 0) {
                        const feature = geojson.features[0];
                        const geom = feature.geometry;

                        if (geom.type === 'Polygon') {
                            const firstCoord = geom.coordinates[0][0];
                            latlng = [firstCoord[1], firstCoord[0]];
                        } else if (geom.type === 'Point') {
                            latlng = [geom.coordinates[1], geom.coordinates[0]];
                        } else if (geom.type === 'MultiPolygon') {
                            const firstCoord = geom.coordinates[0][0][0];
                            latlng = [firstCoord[1], firstCoord[0]];
                        }
                    }

                    console.log(latlng);

                    // Inisialisasi peta dengan setView dari data koordinat
                    const map = L.map(mapId).setView(latlng, 18);
                    leafletMaps[id] = map;

                    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    const satellite = L.tileLayer(
                        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                            attribution: 'Tiles &copy; Esri'
                        });

                    // Tambahkan GeoJSON layer
                    const geoLayer = L.geoJSON(geojson).addTo(map);

                    // Kontrol Layer
                    L.control.layers({
                        "Peta Biasa": osm,
                        "Peta Satelit": satellite
                    }, {
                        "Wilayah Tanah": geoLayer
                    }, {
                        collapsed: false
                    }).addTo(map);

                    // Fit bounds agar seluruh area terlihat (bisa override setView)
                    map.fitBounds(geoLayer.getBounds());
                });
            });
        });
    </script>
@endsection
