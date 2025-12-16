<?php
// db.php
$host = "localhost";
$user = "root";
$pass = "root";
$dbname = "coach_athlete_app";

// Connexion MySQLi
$conn = new mysqli($host, $user, $pass, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
