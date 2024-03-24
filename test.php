<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Chart Example</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="myChart" width="400" height="400"></canvas>

    <script>
        // Function to fetch JSON data from a file
        async function fetchJSONData(url) {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            return await response.json();
        }

        // Function to create a chart
        function createChart(labels, data) {
            const config = {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Data',
                        backgroundColor: 'blue',
                        borderColor: 'black',
                        data: data,
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
            };
            return new Chart(
                document.getElementById('myChart'),
                config
            );
        }

        // Fetch data and create the chart
        fetchJSONData('map.json')
            .then(data => {
                const myChart = createChart(data.labels, data.data);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    </script>
</body>
</html>
