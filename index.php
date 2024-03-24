<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Response Data</title>
    <style>
        canvas {
            max-width: 600px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <table id="response-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Mobile Number</th>
                <th>Message</th>
                <th>Assign To</th>
                <th>Status</th>
                <th>Posting Date</th>
                <th>Assign Time</th>
                <th>Address</th>
                <th>Brgy_No.</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <canvas id="chart"></canvas>
    <canvas id="brgyChart"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <script>
        function reverseGeocoding(lat, lng) {
            const accessToken =
                'pk.eyJ1Ijoia3Jha2VuMTIzIiwiYSI6ImNsdHU1M3V3ajFhZjUya21vcGMwNG9ldDQifQ.B5h0r_S1blXJS0mJMgwYIA';
            const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${accessToken}`;

            return fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    const address = data.features[0].place_name;
                    console.log(address);
                    return address;
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }

        fetch('https://group65.towntechinnovations.com/emergency-response.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Check if data.reports is an array and contains at least one object
                if (!Array.isArray(data.reports) || data.reports.length === 0) {
                    throw new Error('Response data is not in the expected format');
                }

                const tableBody = document.querySelector('#response-table tbody');
                const chartData = [];

                const promises = [];

                let brgy171 = 0;
                let brgy175 = 0;

                // Iterate over each report and create table rows
                data.reports.forEach(report => {
                    const row = tableBody.insertRow();
                    row.insertCell().textContent = report.id;
                    row.insertCell().textContent = report.fullName;
                    row.insertCell().textContent = report.mobileNumber;
                    row.insertCell().textContent = report.message;
                    row.insertCell().textContent = report.assignTo;
                    row.insertCell().textContent = report.status;
                    row.insertCell().textContent = report.postingDate;
                    row.insertCell().textContent = report.assignTime;

                    const addressCell = row.insertCell();
                    reverseGeocoding(report.location.coordinates[1], report.location.coordinates[0])
                        .then(address => {
                            addressCell.textContent = address;
                        });

                    // Add a cell for the barangay number
                    const brgyCell = row.insertCell();
                    // Reverse geocode to get the barangay number
                    const brgyPromise = reverseGeocoding(report.location.coordinates[1], report.location
                            .coordinates[0])
                        .then(address => {
                            // Extract barangay number from the address
                            const brgyNumber = extractBrgyNumber(address);
                            // Set the extracted barangay number in the new cell
                            brgyCell.textContent = brgyNumber;
                            console.log(brgyNumber)

                            console.log(brgy171)
                            if (brgyNumber == 171) {
                                brgy171++;
                            }
                            if (brgyNumber == 175) {
                                brgy175++;
                            }
                        });

                    promises.push(brgyPromise);
                    // Push the data for the chart
                    chartData.push({
                        fullName: report.fullName,
                        postingDate: report.postingDate
                    });

                });

                Promise.all(promises)
                    .then(() => {
                        const brgychartCtx = document.getElementById('brgyChart').getContext('2d');

                        new Chart(brgychartCtx, {
                            type: 'bar',
                            data: {
                                labels: ['Barangay 171', 'Barangay 175'],
                                datasets: [{
                                    label: 'Number of Accidents per Barangay',
                                    data: [brgy171, brgy175],
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }]
                            }
                        });
                    });


                // Sort the chart data by posting date in descending order
                chartData.sort((a, b) => new Date(b.postingDate) - new Date(a.postingDate));

                // Extract the labels and data for the chart
                // Swap chartLabels and chartValues
                const chartLabels = chartData.map(entry => entry.fullName);
                const chartValues = chartData.map(entry => new Date(entry.postingDate));
                console.log(chartValues);


                // Create the chart
                const chartCtx = document.getElementById('chart').getContext('2d');
                new Chart(chartCtx, {
                    type: 'bar',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Posting Date',
                            data: chartValues.map(date => date.getTime()),
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Horizontal bar chart
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Posting Date'
                                },
                                type: 'time',
                                time: {
                                    unit: 'day',
                                    displayFormats: {
                                        day: 'MMM y' // Format for displaying day labels with year
                                    },
                                    min: new Date('2024-01-01'), // Set the minimum date to 2024
                                    max: new Date('2024-12-31') // Set the maximum date to 2024
                                },
                                ticks: {
                                    source: 'auto' // Auto-generate ticks based on the data range
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Full Name'
                                }
                            }
                        }
                    }
                });


            })
            .catch(error => {
                // Handle errors here
                console.error('There was a problem with the fetch operation:', error);
            });



        // Function to extract barangay number from address
        function extractBrgyNumber(address) {
            // Regular expression pattern to match barangay number
            const regex = /\b(?:Barangay|Brgy)\s+(\d+)\b/i;

            // Match the pattern in the address
            const match = address.match(regex);

            // If a match is found, return the barangay number
            if (match) {
                return match[1];
            } else {
                return 'N/A';
            }
        }
    </script>
</body>

</html>