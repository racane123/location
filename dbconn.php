<?php

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'lgu';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "SELECT *,ST_AsGeoJSON(coordinates) as locations FROM drawn_features";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $features = array(); // Initialize an array to hold features
        while ($row = $result->fetch_assoc()) {
            $geojson = json_decode($row['locations']);
            $name = $row['name'];
            $features[] = array($geojson,$name); // Append each feature to the array
        }
        $geojsonFeatureCollection = array(
            'type' => 'FeatureCollection',
            'features' => $features,
        );
        echo json_encode($geojsonFeatureCollection); // Encode the entire FeatureCollection
    } else {
        http_response_code(404);
        echo json_encode("No data found");
    }
} else {
    http_response_code(404);
    echo "Invalid request method.";
}
?>
