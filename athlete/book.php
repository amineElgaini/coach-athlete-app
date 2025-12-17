<?php
session_start();
require '../includes/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'athlete') {
    header("Location: ../login.php");
    exit;
}

$athlete_id = $_SESSION['user_id'];
$coach_id = intval($_POST['coach_id'] ?? 0);
$availability_id = intval($_POST['availability_id'] ?? 0);

if (!$coach_id || !$availability_id) {
    die("Invalid request.");
}

// Get availability
$stmt = $conn->prepare("
SELECT date, start_time, end_time
FROM availabilities 
WHERE id = ? AND is_available = 1
");
$stmt->bind_param("i", $availability_id);
$stmt->execute();
$slot = $stmt->get_result()->fetch_assoc();

if (!$slot) {
    die("<div class='max-w-md mx-auto mt-6 p-4 bg-red-100 text-red-700 rounded'>Slot not available.</div>");
}

// Insert booking
$stmt = $conn->prepare("
INSERT INTO bookings 
(athlete_id, coach_id, availability_id, booking_date, start_time, end_time)
VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "iiisss",
    $athlete_id,
    $coach_id,
    $availability_id,
    $slot['date'],
    $slot['start_time'],
    $slot['end_time']
);
$stmt->execute();

// Mark availability unavailable
$update = $conn->prepare("UPDATE availabilities SET is_available = 0 WHERE id = ?");
$update->bind_param("i", $availability_id);
$update->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6 flex items-center justify-center">

    <div class="max-w-md bg-white p-6 rounded shadow text-center">
        <h2 class="text-2xl font-semibold mb-4 text-green-600">Booking Created!</h2>
        <p class="text-gray-700 mb-2">Your session with coach #<?= $coach_id ?> is now <strong>pending approval</strong>.</p>
        <p class="text-gray-600 mb-4">Date: <?= $slot['date'] ?> <br> Time: <?= $slot['start_time'] ?> - <?= $slot['end_time'] ?></p>
        <a href="my_bookings.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">View My Bookings</a>
    </div>

</body>

</html>
