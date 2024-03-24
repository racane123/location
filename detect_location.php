<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leaflet Drawing Library with Reverse Geocoding</title>
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <!-- Leaflet JavaScript -->
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <div id="map" style="height: 400px;"></div>

  <script>
    var map = L.map('map').setView([0, 0], 2); // Set initial map view to center

    // Add tile layer (you can use your preferred tile provider)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Event listener for map clicks
    map.on('click', function(event) {
      // Get coordinates of the clicked point
      var latitude = event.latlng.lat;
      var longitude = event.latlng.lng;

      // Add marker to the map
      var marker = L.marker([latitude, longitude]).addTo(map);

      // Reverse geocoding to get address from coordinates
      fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + latitude + '&lon=' + longitude)
        .then(response => response.json())
        .then(data => {
          // Parse address components
          var address = data.address;
          var popupContent = "<b>Address:</b><br>";
          if (address.road) popupContent += "Street: " + address.road + "<br>";
          if (address.barangay) popupContent += "Barangay: " + address.barangay + "<br>";
          if (address.city) popupContent += "City: " + address.city + "<br>";
          if (address.neighbourhood) popupContent += "Neighbourhood: " + address.neighbourhood + "<br>";
          if (address.postcode) popupContent += "Postcode: " + address.postcode + "<br>";
          if (address.region) popupContent += "Region: " + address.region + "<br>";
          if (address.state_district) popupContent += "State District: " + address.state_district + "<br>";
          if (address.country) popupContent += "Country: " + address.country + "<br>";

          marker.bindPopup(popupContent).openPopup();
        })
        .catch(error => console.log(error));
    });
  </script>
</body>
</html>
