<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../includes/db.php';
include '../includes/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'coach') {
    header("Location: ../login.php");
    exit;
}

$coach_user_id = $_SESSION['user_id'];

// Récupérer l'ID du coach_profile
$stmt = $conn->prepare("SELECT id FROM coach_profiles WHERE user_id = ?");
$stmt->bind_param("i", $coach_user_id);
$stmt->execute();
$coach_id = $stmt->get_result()->fetch_assoc()['id'];

// Comptes
$stmt = $conn->prepare("
SELECT 
    SUM(status='pending') AS pending,
    SUM(status='accepted' AND booking_date=CURDATE()) AS today,
    SUM(status='accepted' AND booking_date=CURDATE()+INTERVAL 1 DAY) AS tomorrow
FROM bookings
WHERE coach_id = ?
");
$stmt->bind_param("i", $coach_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Prochain rendez-vous
$stmt = $conn->prepare("
SELECT b.booking_date, b.start_time, b.end_time, u.first_name, u.last_name
FROM bookings b
JOIN users u ON u.id = b.athlete_id
WHERE b.coach_id = ? AND b.status='accepted' AND b.booking_date >= CURDATE()
ORDER BY b.booking_date ASC, b.start_time ASC LIMIT 1
");
$stmt->bind_param("i", $coach_id);
$stmt->execute();
$next = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coach Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

<?php include '../includes/coach-header.php'; ?>

<div class="max-w-4xl mx-auto space-y-6">
    <h1 class="text-2xl font-bold mb-4">Welcome, <?= htmlspecialchars($_SESSION['user_first_name']) ?></h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded shadow">
            <h2 class="font-semibold">Pending Bookings</h2>
            <p class="text-2xl"><?= $stats['pending'] ?></p>
        </div>
        <div class="bg-green-100 text-green-800 p-4 rounded shadow">
            <h2 class="font-semibold">Today Accepted</h2>
            <p class="text-2xl"><?= $stats['today'] ?></p>
        </div>
        <div class="bg-blue-100 text-blue-800 p-4 rounded shadow">
            <h2 class="font-semibold">Tomorrow Accepted</h2>
            <p class="text-2xl"><?= $stats['tomorrow'] ?></p>
        </div>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-2">Next Session</h2>
        <?php if ($next): ?>
            <p><strong>Athlete:</strong> <?= htmlspecialchars($next['first_name'] . ' ' . $next['last_name']) ?></p>
            <p><strong>Date:</strong> <?= $next['booking_date'] ?></p>
            <p><strong>Time:</strong> <?= $next['start_time'] ?> - <?= $next['end_time'] ?></p>
        <?php else: ?>
            <p>No upcoming sessions.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
