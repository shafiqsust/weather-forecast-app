<?php
// Include the database connection file
require 'database.php';

// Fetch the location details
//$id = $_GET['id'];
//$skey = $_GET['id'] ? $_GET['id']

// Check if searching with valid lication id or location name

if(isset($_POST['location_name'])){
    $skey = $_POST['location_name'];
    $sql = "SELECT * FROM locations WHERE location_name = ?";

}
else if(isset($_GET['id'])){
    $skey = $_GET['id'];
    $sql = "SELECT * FROM locations WHERE id = ?";
}
else{
    //die("Invalid attempt!");
    $sql ="";
}


$stmt = $db_handle->prepare($sql);
$stmt->execute([$skey]);
$location = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if location exists
 if (!$location) {
     die("Location not found.");
 }




// Use cURL to fetch weather data from the NWS API
$api_url = "https://api.weather.gov/points/{$location['x_coordinate']},{$location['y_coordinate']}";
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3'
    ),
));

$response = curl_exec($curl);
curl_close($curl);

$weather_json = json_decode($response, true);

// Extract the relevant information
$city = $weather_json['properties']['relativeLocation']['properties']['city'];
$state = $weather_json['properties']['relativeLocation']['properties']['state'];
$distance = $weather_json['properties']['relativeLocation']['properties']['distance']['value'];
$bearing = $weather_json['properties']['relativeLocation']['properties']['bearing']['value'];
$time_zone = $weather_json['properties']['timeZone'];
$forecast_url = $weather_json['properties']['forecast'];

// Fetch the detailed forecast data
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $forecast_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3'
    ),
));

$forecast_response = curl_exec($curl);
curl_close($curl);

$forecast_json = json_decode($forecast_response, true);
$forecast_periods = $forecast_json['properties']['periods'];
$current_forecast = $forecast_json['properties']['periods'][0];


//echo "<pre>"; print_r($current_forecast); echo "</pre>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 1024px;
            margin: 0 auto;
            padding: 20px;
        }
        .location-details {
            margin-bottom: 20px;
        }
        .weather-details {
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .weather-img{
            width: 220px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="javascript:void(0)">
    <img src="https://www.assets.cms.vt.edu/images/logo-white-black.svg" alt="Logo" style="width:160px;">
  </a>
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navb">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navb">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="locations.php">Add Location</a>
      </li>      
    </ul>
    <form method="post" action="view_location.php" class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="text" name="location_name" value="" placeholder="location name" required>
      <button class="btn btn-success my-2 my-sm-0" type="submit">Search</button>
    </form>
    <a class="btn btn-dark px-3" target="_blank"
          href="https://github.com/shafiqsust/weather-forecast-app/"
          role="button"><svg aria-hidden="true" height="24" viewBox="0 0 24 24" version="1.1" width="24" data-view-component="true" class="octicon octicon-mark-github">
        <path d="M12.5.75C6.146.75 1 5.896 1 12.25c0 5.089 3.292 9.387 7.863 10.91.575.101.79-.244.79-.546 0-.273-.014-1.178-.014-2.142-2.889.532-3.636-.704-3.866-1.35-.13-.331-.69-1.352-1.18-1.625-.402-.216-.977-.748-.014-.762.906-.014 1.553.834 1.769 1.179 1.035 1.74 2.688 1.25 3.349.948.1-.747.402-1.25.733-1.538-2.559-.287-5.232-1.279-5.232-5.678 0-1.25.445-2.285 1.178-3.09-.115-.288-.517-1.467.115-3.048 0 0 .963-.302 3.163 1.179.92-.259 1.897-.388 2.875-.388.977 0 1.955.13 2.875.388 2.2-1.495 3.162-1.179 3.162-1.179.633 1.581.23 2.76.115 3.048.733.805 1.179 1.825 1.179 3.09 0 4.413-2.688 5.39-5.247 5.678.417.36.776 1.05.776 2.128 0 1.538-.014 2.774-.014 3.162 0 .302.216.662.79.547C20.709 21.637 24 17.324 24 12.25 24 5.896 18.854.75 12.5.75Z"></path>
        </svg>
    </a>
  </div>
</nav>
<div class="container">
    <h1>Location Details</h1>
    
    <div class="location-details">
        <p><strong>Location Name:</strong> <?php echo htmlspecialchars($location['location_name']); ?></p>
        <p><strong>Longitude (x coordinate):</strong> <?php echo htmlspecialchars($location['x_coordinate']); ?></p>
        <p><strong>Latitude (y coordinate):</strong> <?php echo htmlspecialchars($location['y_coordinate']); ?></p>
    </div>
    <div class="weather-details">
        <h2>Location Information</h2>
        <p><strong>City:</strong> <?php echo htmlspecialchars($city); ?></p>
        <p><strong>State:</strong> <?php echo htmlspecialchars($state); ?></p>
        <p><strong>Distance from Coordinates:</strong> <?php echo htmlspecialchars($distance); ?> meters</p>
        <p><strong>Bearing:</strong> <?php echo htmlspecialchars($bearing); ?> degrees</p>
        <p><strong>Time Zone:</strong> <?php echo htmlspecialchars($time_zone); ?></p>
        <p><strong>Forecast:</strong> 
        
        <?php if (!empty($current_forecast)): ?>
                    <div class="row">
                        
                            <div class="col-md-8">
                                <div class="card mb-12">
                                    
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $current_forecast['name'] ?></h5>
                                        <p class="card-text">
                                            <strong>Temperature:</strong> <?= $current_forecast['temperature'] ?> <?= $current_forecast['temperatureUnit'] ?><br>
                                            <strong>Wind Speed:</strong> <?= $current_forecast['windSpeed'] ?> <br>
                                            <strong>Wind Direction:</strong> <?= $current_forecast['windDirection'] ?><br>
                                            <strong>Short Forecast:</strong> <?= $current_forecast['shortForecast'] ?><br>
                                            <strong>Detailed Forecast:</strong> <?= $current_forecast['detailedForecast'] ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <img class="weather-img" src="<?= $current_forecast['icon'] ?>" alt="Weather icon">
                            </div>
                       
                    </div>
                <?php else: ?>
                    <p>No forecast data available.</p>
                <?php endif; ?>

            <h2>Current Week Weather Forecast</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#forecastModal">View Current Week Forecast</button></p>
    </div>
    

    <a href="index.php">Back to Locations</a>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="forecastModal" tabindex="-1" role="dialog" aria-labelledby="forecastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forecastModalLabel">Weather Forecast</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if (!empty($forecast_periods)): ?>
                    <div class="row">
                        <?php foreach ($forecast_periods as $period): ?>
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <img class="card-img-top" src="<?= $period['icon'] ?>" alt="Weather icon">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $period['name'] ?></h5>
                                        <p class="card-text">
                                            <strong>Temperature:</strong> <?= $period['temperature'] ?> <?= $period['temperatureUnit'] ?><br>
                                            <strong>Wind:</strong> <?= $period['windSpeed'] ?> <?= $period['windDirection'] ?><br>
                                            <strong>Forecast:</strong> <?= $period['detailedForecast'] ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No forecast data available.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
