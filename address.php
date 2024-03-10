<?php
// Database connection settings
$servername = "localhost";
$username = "your_username"; // Replace with your MySQL username
$password = "your_password"; // Replace with your MySQL password
$database = "your_database"; // Replace with your MySQL database name

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $road = $_POST["road"] ?? "";
    $quarter = $_POST["quarter"] ?? "";
    $city = $_POST["city"] ?? "";
    $neighbourhood = $_POST["neighbourhood"] ?? "";
    $postcode = $_POST["postcode"] ?? "";
    $region = $_POST["region"] ?? "";
    $state_district = $_POST["state_district"] ?? "";
    $country = $_POST["country"] ?? "";
    $latitude = $_POST["latitude"] ?? "";
    $longitude = $_POST["longitude"] ?? "";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to insert data into the database
    $sql = "INSERT INTO location_data (road, quarter, city, neighbourhood, postcode, region, state_district, country, latitude, longitude)
            VALUES ('$road', '$quarter', '$city', '$neighbourhood', '$postcode', '$region', '$state_district', '$country', '$latitude', '$longitude')";

    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close connection
    $conn->close();
} else {
    // If the form is not submitted, return an error message
    echo "Error: Form not submitted.";
}
?>
