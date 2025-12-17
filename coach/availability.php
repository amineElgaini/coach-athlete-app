<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../includes/db.php';
include '../includes/auth.php';

if ($_SESSION['user_role'] !== 'coach') {
    header("Location: ../login.php");
    exit;
}

$coach_id = $_SESSION['user_id'];
$coach_profile = $conn->query("SELECT id FROM coach_profiles WHERE user_id = $coach_id")->fetch_assoc();
$profile_id = $coach_profile['id'];

// Add availability
if (isset($_POST['add'])) {
    $date = $_POST['date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $conn->query("INSERT INTO availabilities (coach_id, date, start_time, end_time) 
                  VALUES ($profile_id, '$date', '$start', '$end')");
}

// Fetch availabilities
$availabilities = $conn->query("SELECT * FROM availabilities WHERE coach_id = $profile_id ORDER BY date ASC, start_time ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Availability</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-4">
    <?php include '../includes/coach-header.php'; ?>

    <h1 class="text-2xl font-bold mb-4">Manage Availability</h1>
    <a href="dashboard.php" class="text-blue-500 mb-4 inline-block">Back to Dashboard</a>

    <form method="POST" class="bg-white p-4 rounded shadow mb-4 grid grid-cols-1 md:grid-cols-4 gap-2">
        <input type="date" name="date" required class="border p-2 rounded">
        <input type="time" name="start_time" required class="border p-2 rounded">
        <input type="time" name="end_time" required class="border p-2 rounded">
        <button type="submit" name="add" class="bg-blue-500 text-white p-2 rounded">Add Availability</button>
    </form>

    <table class="min-w-full bg-white rounded shadow">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-2">Date</th>
                <th class="p-2">Start</th>
                <th class="p-2">End</th>
                <th class="p-2">Available</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($a = $availabilities->fetch_assoc()): ?>
                <tr class="border-t">
                    <td class="p-2"><?= $a['date'] ?></td>
                    <td class="p-2"><?= $a['start_time'] ?></td>
                    <td class="p-2"><?= $a['end_time'] ?></td>
                    <td class="p-2"><?= $a['is_available'] ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>

</html>