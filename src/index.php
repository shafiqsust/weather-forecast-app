<?php
// Include the database connection file
require 'database.php';

// Fetch all locations
$stmt = $db_handle->query("SELECT * FROM locations");
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERA Weather Forecast App</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .navbar-dark .nav-item > .nav-link.active  {
            color:white;
        }
        .container {
            max-width: 1024px;
            margin: 0 auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .submit-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            text-transform: uppercase;
            color: white;
            border: none;
            cursor: pointer;
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
        <a class="nav-link active" href="index.php">Home</a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="locations.php">Add Location</a>
      </li>      
    </ul>
    <form method="post" action="view_location.php" class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="text" name="location_name" value="" required placeholder="location name">
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
    
    <h1>Saved Locations</h1>
    <table>
        <tr>
            <th>Location Name</th>
            <th>X Coordinate</th>
            <th>Y Coordinate</th>
            <th>Action</th>
        </tr>
        <?php foreach ($locations as $location) { ?>
            <tr>
                <td><?php echo htmlspecialchars($location['location_name']); ?></td>
                <td><?php echo htmlspecialchars($location['x_coordinate']); ?></td>
                <td><?php echo htmlspecialchars($location['y_coordinate']); ?></td>
                <td>
                  <a href="view_location.php?id=<?php echo $location['id']; ?>">View weather forecast</a>
                  &nbsp; | &nbsp;
                  <a href="locations.php?id=<?php echo $location['id']; ?>">Edit</a>
              </td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
