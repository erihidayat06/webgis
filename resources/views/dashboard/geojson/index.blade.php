@extends('dashboard.layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <div class="container py-4">


        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="card-title">
                        <h5>Daftar Tanah Transmigrasi</h5>
                    </div>
                    <a href="{{ route('tanah.create') }}" class="btn btn-primary">+ Tambah Data</a>
                </div>

                <div class="table-responsive">
                    <table class="table datatable align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>NIB</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Tempat&nbsp;/&nbsp;Tanggal Lahir</th>
                                <th>Alamat</th>
                                <th>Desa</th>
                                <th>Kecamatan</th>
                                <th>Luas&nbsp;(m²)</th>
                                <th>Penggunaan</th>
                                <th>Jenis&nbsp;Tanah</th>
                                <th>Kadar&nbsp;Air</th>
                                <th>Lereng</th>
                                <th>Rekomendasi&nbsp;Tanaman</th>

                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tanahs as $index => $tanah)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $tanah->nib }}</td>
                                    <td>{{ $tanah->nama }}</td>
                                    <td>{{ $tanah->nik }}</td>
                                    <td>{{ $tanah->tempat_lahir }},
                                        {{ \Carbon\Carbon::parse($tanah->tanggal_lahir)->format('d-m-Y') }}</td>
                                    <td>{{ $tanah->alamat }}</td>
                                    <td>{{ $tanah->desa }}</td>
                                    <td>{{ $tanah->kecamatan }}</td>
                                    <td>{{ number_format($tanah->luas, 2) }}</td>
                                    <td>{{ $tanah->penggunaan_tanah }}</td>
                                    <td>{{ $tanah->jenis_tanah }}</td>
                                    <td>{{ $tanah->kadar_air }}</td>
                                    <td>{{ $tanah->lereng }}</td>
                                    <td>{{ $tanah->rekomendasi_tanaman }}</td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                                                data-bs-target="#mapModal{{ $tanah->id }}">Lihat Peta</button>
                                            <a href="{{ route('tanah.edit', $tanah->id) }}"
                                                class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('tanah.destroy', $tanah->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Peta -->
                                <div class="modal fade" id="mapModal{{ $tanah->id }}" tabindex="-1"
                                    data-id="{{ $tanah->id }}" aria-labelledby="mapModalLabel{{ $tanah->id }}"
                                    aria-hidden="true">
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
                                <tr>
                                    <td colspan="16" class="text-center">Belum ada data tanah.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

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

                    const map = L.map(mapId).setView([-7.2626, 106.9179], 13);
                    leafletMaps[id] = map;

                    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    const satellite = L.tileLayer(
                        'https://server.arcgisonline.com/ArcGIS/rest/services/' +
                        'World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                            attribution: 'Tiles &copy; Esri'
                        });

                    const geoLayer = L.geoJSON(geojson).addTo(map);

                    L.control.layers({
                        "Peta Biasa": osm,
                        "Peta Satelit": satellite
                    }, {
                        "Wilayah Tanah": geoLayer
                    }, {
                        collapsed: false
                    }).addTo(map);

                    map.fitBounds(geoLayer.getBounds());
                });
            });
        });
    </script>
@endsection
