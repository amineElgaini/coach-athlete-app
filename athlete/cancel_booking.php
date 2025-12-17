<?php
session_start();
require '../includes/db.php';

$id = intval($_GET['id']);
$athlete_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
UPDATE bookings 
SET status = 'cancelled'
WHERE id = ? AND athlete_id = ?
");
$stmt->bind_param("ii", $id, $athlete_id);
$stmt->execute();

header("Location: my_bookings.php");
