<?php
function get_connection(){
    $servername = "localhost";
    $username = "rperez";
    $password = "ST8mXtMFs";
    $dbname = "rperez";

    $connection = new mysqli($servername, $username, $password, $dbname);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    return $connection;
}
