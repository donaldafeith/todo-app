<?php
$servername = "localhost";
$username = "u774687393_menatombotodo";
$password = "M@pr=HzDG9rEGW+";
$dbname = "u774687393_todo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>