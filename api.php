<?php

function reverseGeocode($latitude, $longitude)
{
    $apiKey = '236351096d5f4b03a5b0ce68dd2dbe30';
    $url = "https://api.geoapify.com/v1/geocode/reverse?lat=$latitude&lon=$longitude&apiKey=$apiKey";

    try {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if ($response === false) {
            throw new Exception('Reverse geocoding request failed: ' . curl_error($curl));
        }

        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode !== 200) {
            throw new Exception('Reverse geocoding request failed with status code: ' . $statusCode);
        }

        $data = json_decode($response, true);
        curl_close($curl);

        return $data;
    } catch (Exception $e) {
        return ['features' => [['properties' => ['quarter' => 'Unknown Quarter', 'formatted' => 'Unknown Address']]]];
    }
}

function getReports()
{
    $url = 'https://group65.towntechinnovations.com/emergency-response.php';

    try {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if ($response === false) {
            throw new Exception('API request failed: ' . curl_error($curl));
        }

        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode !== 200) {
            throw new Exception('API request failed with status code: ' . $statusCode);
        }

        $data = json_decode($response, true);
        curl_close($curl);

        if (is_array($data['reports'])) {
            $reports = [];

            foreach ($data['reports'] as $report) {
                $coordinates = $report['location']['coordinates'];
                $reverseGeocode = reverseGeocode($coordinates[1], $coordinates[0]);

                $quarter = $reverseGeocode['features'][0]['properties']['quarter'] ?? $reverseGeocode['features'][0]['properties']['suburb'] ?? 'Barangay 175';
                $address = $reverseGeocode['features'][0]['properties']['formatted'] ?? 'Unknown Address';

                $reports[] = [
                    'id' => $report['id'],
                    'quarter' => $quarter,
                    'address' => $address
                ];
            }

            return $reports;
        } else {
            throw new Exception('Response data is not in the expected format');
        }
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

// Handle API endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_URI'] === 'reports') {
    $reports = getReports();

    header('Content-Type: application/json');
    echo json_encode($reports);
} else {
    http_response_code(404);
    echo 'Not Found';
}