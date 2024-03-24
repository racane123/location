<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h3 id="reportCount"></h3>
    <table id="reportTable">
        <thead>
            <tr>
                <th>Report ID</th>
                <th>Quarter</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody id="reportTableBody">
        </tbody>
    </table>
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.6.1/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.6.1/mapbox-gl.css" rel="stylesheet" />

    <script>
        function fetchReportCount() {
            fetch('https://group65.towntechinnovations.com/emergency-response.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!Array.isArray(data.reports)) {
                        throw new Error('Response data is not in the expected format');
                    }

                    const numberOfReports = data.reports.length;

                    // Update the HTML content with the count of reports
                    document.getElementById('reportCount').textContent = `Number of reports: ${numberOfReports}`;

                    // Display report data in table
                    const reportTableBody = document.getElementById('reportTableBody');
                    reportTableBody.innerHTML = ''; // Clear previous results

                    data.reports.forEach(report => {
                        const coordinates = report.location.coordinates;
                        reverseGeocode(coordinates[1], coordinates[0])
                            .then(result => {
                                console.log(result)
                                let quarter = result.features[0].properties.quarter;
                                const address = result.features[0].properties.formatted;
                                if (quarter === undefined) {
                                    quarter = result.features[0].properties.suburb;
                                    if (quarter === undefined) {
                                        var barangay = 'Barangay 175'; // Corrected variable name
                                        quarter = barangay; // Assigning the correct variable
                                    }
                                }

                                const row = document.createElement('tr');
                                row.innerHTML = `
                                <td>${report.id}</td>
                                <td>${quarter}</td>
                                <td>${address}</td>
                            `;
                                reportTableBody.appendChild(row);
                            })
                            .catch(error => {
                                console.error('Error in reverse geocoding:', error);
                            });
                    });
                })
                .catch(error => {
                    // Handle errors here
                    console.error('There was a problem with the fetch operation:', error);
                });
        }

        // Function to perform reverse geocoding using Mapbox API
        function reverseGeocode(latitude, longitude) {
            var requestOptions = {
                method: 'GET',
            };
            const apiKey = '236351096d5f4b03a5b0ce68dd2dbe30';
            return fetch(`https://api.geoapify.com/v1/geocode/reverse?lat=${latitude}&lon=${longitude}&apiKey=${apiKey}`, requestOptions)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Reverse geocoding request failed');
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('Error in reverse geocoding:', error);
                    return {
                        features: [{
                            properties: {
                                quarter: 'Unknown Quarter',
                                formatted: 'Unknown Address'
                            }
                        }]
                    };
                });
        }

        // Call fetchReportCount initially
        fetchReportCount();

        // Set interval to fetch data every 30 seconds (adjust interval as needed)
        setInterval(fetchReportCount, 30000); // 30 seconds
    </script>
</body>

</html>