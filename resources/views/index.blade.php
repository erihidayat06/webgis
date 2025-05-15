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

    <div id="filterContainer" class="card p-2" style="position: absolute; top: 100px; right: 10px; z-index: 1000;">
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
    <script src="/data/Polygon_CGMK_WEBGIS_FIKS_1.js"></script>
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

                        // ⬇ Tambahkan legend untuk menjelaskan warna berdasarkan luas
                        var legend = L.control({
                            position: 'bottomright'
                        });

                        legend.onAdd = function(map) {
                            var div = L.DomUtil.create('div', 'info legend');
                            var grades = [0, 700, 1900, 3000]; // batasan luas
                            var colors = ['#3388ff', '#ff5733', '#33ff57', '#28a745']; // sesuaikan dengan logic warna

                            div.innerHTML += '<strong>Luas Tanah (m²)</strong><br>';

                            for (var i = 0; i < grades.length; i++) {
                                var from = grades[i];
                                var to = grades[i + 1];

                                div.innerHTML +=
                                    '<i style="background:' + colors[i] +
                                    ';width: 18px; height: 18px; display: inline-block; margin-right: 6px;"></i> ' +
                                    from + (to ? '&ndash;' + to + '<br>' : '+');
                            }

                            return div;
                        };

                        L.control.layers(baseLayers).addTo(map);

                        // Semua fitur untuk pencarian
                        const allFeatures = [];

                        // Tambahkan GeoJSON ke peta
                        geojsonLayer = L.geoJSON(data, {
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

                                geojsonLayer.clearLayers(); // Kosongkan layer lama

                                const filteredFeatures = data.features.filter(feature => {
                                    const luas = parseFloat(feature.properties.luas);
                                    if (selected === 'lt700') return luas < 700;
                                    if (selected === '700to1900') return luas >= 700 && luas <=
                                        1900;
                                    if (selected === 'gt1900') return luas > 1900;
                                    return true; // semua
                                });

                                geojsonLayer.addData(
                                    filteredFeatures); // Tambahkan kembali fitur yang lolos filter

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
                }
                return tempDiv.innerHTML;
            }
        // add class to format popup if it contains media
        function addClassToPopupIfMedia(content, popup) {
            var tempDiv = document.createElement("div");
            tempDiv.innerHTML = content;
            if (tempDiv.querySelector("td img")) {
                popup._contentNode.classList.add("media");
                // Delay to force the redraw
                setTimeout(function() {
                    popup.update();
                }, 10);
            } else {
                popup._contentNode.classList.remove("media");
            }
        }
        var title = new L.Control({
            position: "topright"
        });
        title.onAdd = function(map) {
            this._div = L.DomUtil.create("div", "info");
            this.update();
            return this._div;
        };
        title.update = function() {
            this._div.innerHTML =
                "<h5>Peta Transmigrasi Desa Curugluhur dan Desa Mekarsari Kecamatan Sagaranten</h5>";
        };
        title.addTo(map);
        var abstract = new L.Control({
            position: "topright"
        });
        abstract.onAdd = function(map) {
            this._div = L.DomUtil.create("div", "leaflet-control abstract");
            this._div.id = "abstract";

            abstract.show();
            return this._div;
        };
        abstract.show = function() {
            this._div.classList.remove("abstract");
            this._div.classList.add("abstractUncollapsed");
            this._div.innerHTML =
                "Peta ini memberikan informasi penggunaan tanah, jenis tanah, kadar air, lereng dan rekomendasi penggunaan tanah.";
        };
        abstract.addTo(map);
        var zoomControl = L.control
            .zoom({
                position: "topleft",
            })
            .addTo(map);
        var bounds_group = new L.featureGroup([]);

        function setBounds() {}
        map.createPane("pane_OSMStandard_0");
        map.getPane("pane_OSMStandard_0").style.zIndex = 400;
        var layer_OSMStandard_0 = L.tileLayer(
            "http://tile.openstreetmap.org/{z}/{x}/{y}.png", {
                pane: "pane_OSMStandard_0",
                opacity: 1.0,
                attribution: '<a href="https://www.openstreetmap.org/copyright">© OpenStreetMap contributors, CC-BY-SA</a>',
                minZoom: 1,
                maxZoom: 28,
                minNativeZoom: 0,
                maxNativeZoom: 19,
            }
        );
        layer_OSMStandard_0;
        map.addLayer(layer_OSMStandard_0);

        function pop_Polygon_CGMK_WEBGIS_FIKS_1(feature, layer) {
            var popupContent =
                '<table>\
                                                                <tr>\
                                                                    <th scope="row">KECAMATAN</th>\
                                                                    <td>' +
                (feature.properties["KECAMATAN"] !== null ?
                    autolinker.link(
                        String(feature.properties["KECAMATAN"])
                        .replace(/'/g, "'")
                        .toLocaleString()
                    ) :
                    "") +
                '</td>\
                                                                </tr>\
                                                                <tr>\
                                                                    <th scope="row">KELURAHAN</th>\
                                                                    <td>' +
                (feature.properties["KELURAHAN"] !== null ?
                    autolinker.link(
                        String(feature.properties["KELURAHAN"])
                        .replace(/'/g, "'")
                        .toLocaleString()
                    ) :
                    "") +
                '</td>\
                                                                </tr>\
                                                                <tr>\
                                                                    <th scope="row">TIPEHAK</th>\
                                                                    <td>' +
                (feature.properties["TIPEHAK"] !== null ?
                    autolinker.link(
                        String(feature.properties["TIPEHAK"])
                        .replace(/'/g, "'")
                        .toLocaleString()
                    ) :
                    "") +
                '</td>\
                                                                </tr>\
                                                                <tr>\
                                                                    <th scope="row">TAHUN</th>\
                                                                    <td>' +
                (feature.properties["TAHUN"] !== null ?
                    autolinker.link(
                        String(feature.properties["TAHUN"])
                        .replace(/'/g, "'")
                        .toLocaleString()
                    ) :
                    "") +
                '</td>\
                                                                </tr>\
                                                                <tr>\
                                                                    <th scope="row">NIB</th>\
                                                                    <td>' +
                (feature.properties["NIB"] !== null ?
                    autolinker.link(
                        String(feature.properties["NIB"])
                        .replace(/'/g, "'")
                        .toLocaleString()
                    ) :
                    "") +
                '</td>\
                                                                </tr>\
                                                                <tr>\
                                                                    <th scope="row">LUASTERTUL</th>\
                                                                    <td>' +
                (feature.properties["LUASTERTUL"] !== null ?
                    autolinker.link(
                        String(feature.properties["LUASTERTUL"])
                        .replace(/'/g, "'")
                        .toLocaleString()
                    ) :
                    "") +
                '</td>\
                                                                </tr>\
                                                                <tr>\
                                                                    <th scope="row">Penggunaan</th>\
                                                                    <td>' +
                (feature.properties["Penggunaan"] !== null ?
                    autolinker.link(
                        String(feature.properties["Penggunaan"])
                        .replace(/'/g, "'")
                        .toLocaleString()
                    ) :
                    "") +
                '</td>\
                                                                </tr>\
                                                                <tr>\
                                                                    <th scope="row">J_Tanah</th>\
                                                                    <td>' +
                (feature.properties["J_Tanah"] !== null ?
                    autolinker.link(
                        String(feature.properties["J_Tanah"])
                        .replace(/'/g, "'")
                        .toLocaleString()
                    ) :
                    "") +
                '</td>\
                                                                </tr>\
                                                                <tr>\
                                                                    <th scope="row">Kadar_Air</th>\
                                                                    <td>' +
                (feature.properties["Kadar_Air"] !== null ?
                    autolinker.link(
                        String(feature.properties["Kadar_Air"])
                        .replace(/'/g, "'")
                        .toLocaleString()
                    ) :
                    "") +
                '</td>\
                                                                </tr>\
                                                                <tr>\
                                                                    <th scope="row">Lereng</th>\
                                                                    <td>' +
                (feature.properties["Lereng"] !== null ?
                    autolinker.link(
                        String(feature.properties["Lereng"])
                        .replace(/'/g, "'")
                        .toLocaleString()
                    ) :
                    "") +
                '</td>\
                                                                </tr>\
                                                                <tr>\
                                                                    <th scope="row">Rekomen</th>\
                                                                    <td>' +
                (feature.properties["Rekomen"] !== null ?
                    autolinker.link(
                        String(feature.properties["Rekomen"])
                        .replace(/'/g, "'")
                        .toLocaleString()
                    ) :
                    "") +
                "</td>\
                                                                </tr>\
                                                            </table>";
            var content = removeEmptyRowsFromPopupContent(popupContent, feature);
            layer.on("popupopen", function(e) {
                addClassToPopupIfMedia(content, e.popup);
            });
            layer.bindPopup(content, {
                maxHeight: 400
            });
        }

        function style_Polygon_CGMK_WEBGIS_FIKS_1_0(feature) {
            if (
                feature.properties["LUASTERTUL"] >= 48.0 &&
                feature.properties["LUASTERTUL"] <= 717.0
            ) {
                return {
                    pane: "pane_Polygon_CGMK_WEBGIS_FIKS_1",
                    opacity: 1,
                    color: "rgba(35,35,35,1.0)",
                    dashArray: "",
                    lineCap: "butt",
                    lineJoin: "miter",
                    weight: 1.0,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: "rgba(255,255,255,1.0)",
                    interactive: true,
                };
            }
            if (
                feature.properties["LUASTERTUL"] >= 717.0 &&
                feature.properties["LUASTERTUL"] <= 1852.0
            ) {
                return {
                    pane: "pane_Polygon_CGMK_WEBGIS_FIKS_1",
                    opacity: 1,
                    color: "rgba(35,35,35,1.0)",
                    dashArray: "",
                    lineCap: "butt",
                    lineJoin: "miter",
                    weight: 1.0,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: "rgba(255,128,128,1.0)",
                    interactive: true,
                };
            }
            if (
                feature.properties["LUASTERTUL"] >= 1852.0 &&
                feature.properties["LUASTERTUL"] <= 5331.0
            ) {
                return {
                    pane: "pane_Polygon_CGMK_WEBGIS_FIKS_1",
                    opacity: 1,
                    color: "rgba(35,35,35,1.0)",
                    dashArray: "",
                    lineCap: "butt",
                    lineJoin: "miter",
                    weight: 1.0,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: "rgba(255,0,0,1.0)",
                    interactive: true,
                };
            }
        }
        map.createPane("pane_Polygon_CGMK_WEBGIS_FIKS_1");
        map.getPane("pane_Polygon_CGMK_WEBGIS_FIKS_1").style.zIndex = 401;
        map.getPane("pane_Polygon_CGMK_WEBGIS_FIKS_1").style["mix-blend-mode"] =
            "normal";
        var layer_Polygon_CGMK_WEBGIS_FIKS_1 = new L.geoJson(
            json_Polygon_CGMK_WEBGIS_FIKS_1, {
                attribution: "",
                interactive: true,
                dataVar: "json_Polygon_CGMK_WEBGIS_FIKS_1",
                layerName: "layer_Polygon_CGMK_WEBGIS_FIKS_1",
                pane: "pane_Polygon_CGMK_WEBGIS_FIKS_1",
                onEachFeature: pop_Polygon_CGMK_WEBGIS_FIKS_1,
                style: style_Polygon_CGMK_WEBGIS_FIKS_1_0,
            }
        );
        bounds_group.addLayer(layer_Polygon_CGMK_WEBGIS_FIKS_1);
        map.addLayer(layer_Polygon_CGMK_WEBGIS_FIKS_1);
        var overlaysTree = [{
                label: 'Polygon_CG&MK_WEBGIS_FIKS<br /><table><tr><td style="text-align: center;"><img src="legend/Polygon_CGMK_WEBGIS_FIKS_1_Luas07000.png" /></td><td>Luas ( 0 - 700 )</td></tr><tr><td style="text-align: center;"><img src="legend/Polygon_CGMK_WEBGIS_FIKS_1_Luas70019001.png" /></td><td>Luas ( 700 - 1900 )</td></tr><tr><td style="text-align: center;"><img src="legend/Polygon_CGMK_WEBGIS_FIKS_1_Luas190053002.png" /></td><td>Luas ( 1900 - 5300 )</td></tr></table>',
                layer: layer_Polygon_CGMK_WEBGIS_FIKS_1,
            },
            {
                label: "OSM Standard",
                layer: layer_OSMStandard_0
            },
        ];
        var lay = L.control.layers.tree(null, overlaysTree, {
            //namedToggle: true,
            //selectorBack: false,
            //closedSymbol: '&#8862; &#x1f5c0;',
            //openedSymbol: '&#8863; &#x1f5c1;',
            //collapseAll: 'Collapse all',
            //expandAll: 'Expand all',
            collapsed: true,
        });
        lay.addTo(map);
        setBounds();
    </script>
@endsection
