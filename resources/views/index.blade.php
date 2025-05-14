@extends('layouts.main')

@section('content')
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
        var map = L.map("map", {
            zoomControl: false,
            maxZoom: 28,
            minZoom: 1,
        }).fitBounds([
            [-7.2388533721644714, 106.81483259106884],
            [-7.2338673417090416, 106.82421428501259],
        ]);
        var hash = new L.Hash(map);
        map.attributionControl.setPrefix(
            '<a href="https://github.com/tomchadwin/qgis2web" target="_blank">qgis2web</a> &middot; <a href="https://leafletjs.com" title="A JS library for interactive maps">Leaflet</a> &middot; <a href="https://qgis.org">QGIS</a>'
        );
        var autolinker = new Autolinker({
            truncate: {
                length: 30,
                location: "smart"
            },
        });
        // remove popup's row if "visible-with-data"
        function removeEmptyRowsFromPopupContent(content, feature) {
            var tempDiv = document.createElement("div");
            tempDiv.innerHTML = content;
            var rows = tempDiv.querySelectorAll("tr");
            for (var i = 0; i < rows.length; i++) {
                var td = rows[i].querySelector("td.visible-with-data");
                var key = td ? td.id : "";
                if (
                    td &&
                    td.classList.contains("visible-with-data") &&
                    feature.properties[key] == null
                ) {
                    rows[i].parentNode.removeChild(rows[i]);
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
