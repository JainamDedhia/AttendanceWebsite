// attendance.js

function checkLocationPeriodically() {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            var id = document.getElementById('id').value;
            var dob = document.getElementById('dob').value;

            // Prepare data for checking location
            var data = new FormData();
            data.append('latitude', latitude);
            data.append('longitude', longitude);
            data.append('id', id);
            data.append('dob', dob);

            // Send data via AJAX to check_location.php
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "check_location.php", true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    document.getElementById('geofenceStatus').innerText = response.message;
                }
            };
            xhr.send(data);

        }, function(error) {
            document.getElementById('geofenceStatus').innerText = 'Error getting location: ' + error.message;
        });
    } else {
        document.getElementById('geofenceStatus').innerText = 'Geolocation is not supported by this browser.';
    }
}
