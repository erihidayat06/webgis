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

            #filterContainer {
                top: 150px !important;
                right: 100px !important;
                transform: translateX(50%);
                width: 40% !important;
            }

            label {
                font-size: 12px !important;
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

    <div id="filterContainer" class="card p-2" style="position: absolute; top: 120px; right: 10px; z-index: 1000;">
        <strong>Filter Luas Tanah:</strong>
        <div class="form-check d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="filterLuas" id="all" value="all" checked>
            <label class="form-check-label d-flex align-items-center" for="all">
                <span
                    style="background:#000; width:14px; height:14px; display:inline-block; margin-right:6px; border-radius:3px;"></span>
                Semua
            </label>
        </div>
        <div class="form-check d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="filterLuas" id="lt700" value="lt700">
            <label class="form-check-label d-flex align-items-center" for="lt700">
                <span
                    style="background:#3388ff; width:14px; height:14px; display:inline-block; margin-right:6px; border-radius:3px;"></span>
                &lt; 700 m²
            </label>
        </div>
        <div class="form-check d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="filterLuas" id="700to1900" value="700to1900">
            <label class="form-check-label d-flex align-items-center" for="700to1900">
                <span
                    style="background:#ff5733; width:14px; height:14px; display:inline-block; margin-right:6px; border-radius:3px;"></span>
                700–1900 m²
            </label>
        </div>
        <div class="form-check d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="filterLuas" id="gt1900" value="gt1900">
            <label class="form-check-label d-flex align-items-center" for="gt1900">
                <span
                    style="background:#33ff57; width:14px; height:14px; display:inline-block; margin-right:6px; border-radius:3px;"></span>
                &gt; 1900 m²
            </label>
        </div>
    </div>

    <div id="map"></div>
    <script src="/js/qgis2web_expressions.js"></script>
    <script src="/js/leaflet.js"></script>
    <script src="/js/L.Control.Layers.Tree.min.js"></script>
    <script src="/js/leaflet.rotatedMarker.js"></script>
    <script src="/js/leaflet.pattern.js"></script>
    <script src="/js/leaflet-hash.js"></script>
    <script src="/js/Autolinker.min.js"></script>
    <script src="/js/rbush.min.js"></script>
    <script src="/js/labelgun.min.js"></script>
    <script src="/js/labels.js"></script>
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

                    // Tambahkan legend
                    var legend = L.control({
                        position: 'bottomright'
                    });
                    legend.onAdd = function(map) {
                        var div = L.DomUtil.create('div', 'info legend');
                        var grades = [0, 700, 1900, 3000];
                        var colors = ['#3388ff', '#ff5733', '#33ff57', '#28a745'];

                        return div;
                    };
                    legend.addTo(map);

                    L.control.layers(baseLayers).addTo(map);

                    // Semua fitur untuk pencarian
                    const allFeatures = [];
                    let geojsonLayer = L.geoJSON(data, {
                        onEachFeature: function(feature, layer) {
                            const props = feature.properties;
                            layer.bindPopup(`
                                <table style="border-collapse: collapse; width: 100%;">
                                    <tr><td><strong>Kecamatan</strong></td><td>: ${props.kecamatan}</td></tr>
                                    <tr><td><strong>Kelurahan</strong></td><td>: ${props.desa}</td></tr>
                                    <tr><td><strong>Tipehak</strong></td><td>: ${props.hak_milik}</td></tr>
                                    <tr><td><strong>Tahun</strong></td><td>: ${props.tahun}</td></tr>
                                    <tr><td><strong>NIB</strong></td><td>: ${props.nib}</td></tr>
                                    <tr><td><strong>Luas</strong></td><td>: ${props.luas} m²</td></tr>
                                    <tr><td><strong>Penggunaan</strong></td><td>: ${props.penggunaan_tanah}</td></tr>
                                    <tr><td><strong>Jenis tanah</strong></td><td>: ${props.jenis_tanah}</td></tr>
                                    <tr><td><strong>Kadar Air</strong></td><td>: ${props.kadar_air}</td></tr>
                                    <tr><td><strong>Lereng</strong></td><td>: ${props.lereng}</td></tr>
                                    <tr><td><strong>Rekomendasi</strong></td><td>: ${props.rekomendasi_tanaman}</td></tr>
                                </table>
                            `);
                            allFeatures.push(layer);
                        },
                        style: function(feature) {
                            const luas = parseFloat(feature.properties.luas);
                            return {
                                color: luas < 700 ? '#3388ff' : (luas <= 1900 ? '#ff5733' : '#33ff57'),
                                weight: 0.5,
                                opacity: 1
                            };
                        }
                    }).addTo(map);

                    map.fitBounds(geojsonLayer.getBounds());

                    // Radio filter handler
                    document.querySelectorAll('input[name="filterLuas"]').forEach(radio => {
                        radio.addEventListener('change', function() {
                            const selected = this.value;
                            geojsonLayer.clearLayers();
                            const filteredFeatures = data.features.filter(feature => {
                                const luas = parseFloat(feature.properties.luas);
                                if (selected === 'lt700') return luas < 700;
                                if (selected === '700to1900') return luas >= 700 && luas <=
                                    1900;
                                if (selected === 'gt1900') return luas > 1900;
                                return true;
                            });
                            geojsonLayer.addData(filteredFeatures);
                            if (filteredFeatures.length > 0) {
                                const bounds = geojsonLayer.getBounds();
                                if (bounds.isValid()) map.fitBounds(bounds);
                            }
                        });
                    });

                    // Fitur pencarian
                    const searchInput = document.getElementById('searchInput');
                    searchInput.addEventListener('input', function() {
                        const query = this.value.toLowerCase();
                        if (query === '') {
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

                    // Kode di bawah ini (Polygon_CGMK_WEBGIS_FIKS_1, dsb) HARUS dipindahkan ke dalam blok ini jika ingin menggunakan variabel map yang sama
                    // Jika ingin tetap, pastikan variabel map sudah dideklarasikan sebelum digunakan

                } else {
                    console.error('Invalid GeoJSON data:', data);
                }
            });
    </script>
@endsection
