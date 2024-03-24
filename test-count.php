<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Counts by Quarter</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <h2>Report Counts by Quarter</h2>
    <canvas id="reportChart" width="400" height="400"></canvas>

    <script>
        // Fetch data from the API endpoint
        fetch('http://localhost/ordermanagement/api.php/reports')
            .then(response => response.json())
            .then(data => {
                // Process the data to count the number of reports in each quarter
                const reportCounts = {};
                data.forEach(report => {
                    const quarter = report.quarter;
                    if (reportCounts[quarter]) {
                        reportCounts[quarter]++;
                    } else {
                        reportCounts[quarter] = 1;
                    }
                });

                // Generate labels and data for the chart
                const labels = Object.keys(reportCounts);
                const counts = Object.values(reportCounts);

                // Create the chart
                const ctx = document.getElementById('reportChart').getContext('2d');
                const reportChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Report Counts',
                            data: counts,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    </script>
</body>

</html>