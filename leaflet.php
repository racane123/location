<!DOCTYPE html>
<html>

<head>
    <title>Display Shapefile in Browser</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/shapefile/dist/shapefile.min.js"></script>
</head>

<body>
    <div id="map" style="height: 400px;"></div>
    <script>
        var map = L.map('map').setView([0, 0], 2); // Center map at (0, 0) with zoom level 2
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map); // Add OpenStreetMap as basemap

        fetch('map.shp')
            .then(response => response.arrayBuffer())
            .then(buffer => {
                shapefile.read(buffer).then(function(geojson) {
                    L.geoJSON(geojson).addTo(map);
                });
            });
    </script>
</body>

</html>