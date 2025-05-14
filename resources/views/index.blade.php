@extends('layouts.main')

@section('content')
    <style>
        #map {
            width: 100%;
            height: 100vh;
        }

        #searchInput {
            position: absolute;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: 80%;
            max-width: 300px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        #judul {
            font-weight: bold;
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: 100%;
            max-width: 700px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 768px) {
            #searchInput {
                width: 60%;
                max-width: none;
            }

            #judul {
                top: auto;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                width: 80%;
                max-width: none;
            }
        }
    </style>

    <!-- Meta viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Input Pencarian -->
    <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan Nama, atau Desa">

    <div class="card" id="judul">
        <div class="text-center">
            Peta Transmigrasi Desa Curugluhur dan Desa Mekarsari Kecamatan Sagaranten
        </div>
    </div>

    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        fetch('/api/tanah-geojson')
            .then(response => response.json())
            .then(data => {
                if (data && data.features && data.features.length > 0) {
                    const firstFeature = data.features[0];
                    const geom = firstFeature.geometry;
                    const coords = geom.coordinates;

                    let latlng;
                    if (geom.type === 'Polygon') {
                        const firstCoord = coords[0][0];
                        latlng = [firstCoord[1], firstCoord[0]];
                    } else if (geom.type === 'Point') {
                        latlng = [coords[1], coords[0]];
                    } else if (geom.type === 'MultiPolygon') {
                        const firstCoord = coords[0][0][0];
                        latlng = [firstCoord[1], firstCoord[0]];
                    }

                    // Inisialisasi peta
                    var map = L.map('map', {
                        center: latlng,
                        zoom: 18
                    });

                    // Base layers
                    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    });

                    var esriSat = L.tileLayer(
                        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                            attribution: 'Imagery © Esri'
                        });

                    var esriAdminLabels = L.tileLayer(
                        'https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
                            attribution: 'Admin Labels © Esri'
                        });

                    var esriTransportationLabels = L.tileLayer(
                        'https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}', {
                            attribution: 'Transportation Labels © Esri'
                        });

                    var satelitLengkap = L.layerGroup([
                        esriSat,
                        esriAdminLabels,
                        esriTransportationLabels
                    ]);

                    satelitLengkap.addTo(map);

                    var baseLayers = {
                        "Peta Biasa": osm,
                        "Satelit + Jalan + Nama Kota/Desa": satelitLengkap
                    };

                    L.control.layers(baseLayers).addTo(map);

                    // Semua fitur untuk pencarian
                    const allFeatures = [];

                    // Tambahkan GeoJSON ke peta
                    const geojsonLayer = L.geoJSON(data, {
                        onEachFeature: function(feature, layer) {
                            const props = feature.properties;
                            layer.bindPopup(`
                                <table style="border-collapse: collapse; width: 100%;">

                                    <tr><td><strong>Kecamatan</strong></td><td>: ${props.kecamatan}</td></tr>
                                    <tr><td><strong>Kelurahan</strong></td><td>: ${props.desa}</td></tr>
                                    <tr><td><strong>Tipehak</strong></td><td>: ${props.hak_milik}</td></tr>
                                    <tr><td><strong>Tahun</strong></td><td>: ${props.tahun}</td></tr>
                                    <tr><td><strong>NIB</strong></td><td>: ${props.nib}</td></tr>
                                    <tr><td><strong>Luastertul</strong></td><td>: ${props.luas} m²</td></tr>
                                    <tr><td><strong>Penggunaan</strong></td><td>: ${props.penggunaan_tanah}</td></tr>
                                    <tr><td><strong>Jenis tanah</strong></td><td>: ${props.jenis_tanah}</td></tr>
                                    <tr><td><strong>Kadar Air</strong></td><td>: ${props.kadar_air}</td></tr>
                                    <tr><td><strong>Lereng</strong></td><td>: ${props.lereng}</td></tr>
                                    <tr><td><strong>Rekomendasi</strong></td><td>: ${props.rekomendasi_tanaman}</td></tr>
                                </table>
                            `);

                            // Simpan untuk pencarian
                            allFeatures.push(layer);
                        },
                        style: function(feature) {
                            const props = feature.properties;
                            const luas = parseFloat(props.luas);

                            return {
                                color: luas < 700 ? '#3388ff' : (luas <= 1900 ? '#ff5733' : '#33ff57'),
                                weight: 3,
                                opacity: 1
                            };
                        }
                    }).addTo(map);

                    map.fitBounds(geojsonLayer.getBounds());

                    // Fitur pencarian
                    const searchInput = document.getElementById('searchInput');

                    searchInput.addEventListener('input', function() {
                        const query = this.value.toLowerCase();

                        if (query === '') {
                            // Reset semua style dan tutup semua popup
                            allFeatures.forEach(layer => {
                                geojsonLayer.resetStyle(layer);
                                layer.closePopup();
                            });
                            return;
                        }

                        let found = false;

                        allFeatures.forEach(layer => {
                            const props = layer.feature.properties;
                            const nama = props.nama?.toLowerCase() || '';
                            const nik = props.nik?.toLowerCase() || '';
                            const desa = props.desa?.toLowerCase() || '';

                            if (
                                nama.includes(query) ||
                                nik.includes(query) ||
                                desa.includes(query)
                            ) {
                                layer.setStyle({
                                    color: 'yellow'
                                });
                                layer.openPopup();
                                map.fitBounds(layer.getBounds());
                                found = true;
                            } else {
                                geojsonLayer.resetStyle(layer);
                                layer.closePopup();
                            }
                        });

                        if (!found) {
                            console.log('Data tidak ditemukan');
                        }
                    });


                } else {
                    console.error('Invalid GeoJSON data:', data);
                }
            })
            .catch(error => {
                console.error('Error loading GeoJSON:', error);
            });
    </script>
@endsection
