<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/togeojson@0.15.1/togeojson.min.js"></script>
    <script src="https://unpkg.com/shp-write@2.1.0/dist/shp-write.js"></script>
    <title>Document</title>
</head>

<body>
    <table id="geoJsonTable">
        <thead>
            <tr>
                <th>Feature</th>
                <th>Properties</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <script>
        // Fetch GeoJSON data from a server
        fetch('map.json')
            .then(response => response.json())
            .then(geojson => {
                const tableBody = document.querySelector('#geoJsonTable tbody');
                geojson.features.forEach((feature, index) => {
                    const row = tableBody.insertRow();
                    const cell1 = row.insertCell(0);
                    const cell2 = row.insertCell(1);
                    const cell3 = row.insertCell(2);
                    cell1.textContent = `Feature ${index + 1}`;
                    cell2.textContent = JSON.stringify(feature.properties);
                    const button = document.createElement('button');
                    button.textContent = 'Download Shapefile';
                    button.onclick = () => downloadShapefile(geojson, index);
                    cell3.appendChild(button);
                });
            })
            .catch(error => {
                console.error('Error fetching GeoJSON data:', error);
            });

        function downloadShapefile(geojson, index) {
            // Convert GeoJSON to TopoJSON
            const topojson = window.topojson;
            const topojsonData = topojson.topology({ collection: geojson });
            const convertedGeojson = topojson.feature(topojsonData, topojsonData.objects.collection);

            // Convert GeoJSON to Shapefile
            const shpwrite = window.shpwrite;
            const shapefileData = shpwrite.zip(convertedGeojson);
            const blob = new Blob([shapefileData], { type: 'application/octet-stream' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `output_feature_${index}.zip`;
            a.click();
            URL.revokeObjectURL(url);
        }
    </script>
</body>

</html>