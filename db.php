<?php
$servername = "localhost";
$username = "root";   // your DB user
$password = "";       // your DB password
$dbname = "school_voting";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>