<?php
/******* Instructions *****/
// The following code can be used to get a database object (`$db_handle`) that
// is connected to the MariaDB database container. Feel free to "require" this
// script in other php scripts and to use `global $db_handle;` as your means
// to execute queries in the database. If you prefer a different approach,
// feel free to use your own approach.

// Read the database connection parameters from environment variables
$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');

// Read the password file path from an environment variable
$password_file_path = getenv('PASSWORD_FILE_PATH');

// Read the password from the file
$db_pass = trim(file_get_contents($password_file_path));

// Create a new PDO instance
$db_handle = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
