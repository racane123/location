<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Location Detect</title>

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <!--<div id="map" style="height: 400px;"></div>-->

  <form id="locationForm">

    <label for="road">Street:</label>
    <input type="text" id="road" name="road" readonly><br><br>
    
    <label for="quarter">Barangay:</label>
    <input type="text" id="quarter" name="quarter" readonly><br><br>
    
    <label for="city">City:</label>
    <input type="text" id="city" name="city" readonly><br><br>
    
    <label for="neighbourhood">Neighbourhood:</label>
    <input type="text" id="neighbourhood" name="neighbourhood" readonly><br><br>
    
    <label for="postcode">Postcode:</label>
    <input type="text" id="postcode" name="postcode" readonly><br><br>
    
    <label for="region">State:</label>
    <input type="text" id="region" name="region" readonly><br><br>
    
    <label for="state_district">State District:</label>
    <input type="text" id="state_district" name="state_district" readonly><br><br>
    
    <label for="country">Country:</label>
    <input type="text" id="country" name="country" readonly><br><br>

    <input type="submit" value="Submit">
  </form>

  <script>


    if ("geolocation" in navigator) {

      navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    } else {
      alert("Geolocation is not supported by your browser.");
    }


    function successCallback(position) {
      var latitude = position.coords.latitude;
      var longitude = position.coords.longitude;



      fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + latitude + '&lon=' + longitude)
        .then(response => response.json())
        .then(data => {

          console.log(data);


          document.getElementById("road").value = data.address.road || "";
          document.getElementById("quarter").value = data.address.quarter || "";
          document.getElementById("city").value = data.address.city || "";
          document.getElementById("neighbourhood").value = data.address.neighbourhood || "";
          document.getElementById("postcode").value = data.address.postcode || "";
          document.getElementById("region").value = data.address.region || "";
          document.getElementById("state_district").value = data.address.state_district || "";
          document.getElementById("country").value = data.address.country || "";
        })
        .catch(error => console.log(error));
    }

    // Error callback function
    function errorCallback(error) {
      switch(error.code) {
        case error.PERMISSION_DENIED:
          alert("User denied the request for geolocation.");
          break;
        case error.POSITION_UNAVAILABLE:
          alert("Location information is unavailable.");
          break;
        case error.TIMEOUT:
          alert("The request to get user location timed out.");
          break;
        case error.UNKNOWN_ERROR:
          alert("An unknown error occurred.");
          break;
      }
    }

    // Submit form function
    $("#locationForm").submit(function(event) {
      event.preventDefault();
      var formData = $(this).serialize();
      $.ajax({
        type: "POST",
        url: "address.php", // palitang mo nalang to kung saan mo sya i stostore yung data
        data: formData,
        success: function(response) {
          alert("Form submitted successfully!");
          console.log(response);
        },
        error: function(xhr, status, error) {
          alert("An error occurred while submitting the form.");
          console.log(xhr.responseText);
        }
      });
    });
  </script>
</body>
</html>
