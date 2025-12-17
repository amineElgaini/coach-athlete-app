<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../includes/db.php';
include '../includes/auth.php';

if ($_SESSION['user_role'] !== 'coach') {
    header("Location: ../login.php");
    exit;
}

// Récupérer l'ID du coach
$stmt = $conn->prepare("SELECT id FROM coach_profiles WHERE user_id=?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$coach_id_row = $stmt->get_result()->fetch_assoc();
$coach_id = $coach_id_row['id'];

// Accepter ou refuser
if (isset($_GET['action'], $_GET['booking_id'])) {
    $action = $_GET['action'];
    $booking_id = intval($_GET['booking_id']);
    if ($action == 'accept' || $action == 'reject') {
        $stmt2 = $conn->prepare("UPDATE bookings SET status=? WHERE id=? AND coach_id=?");
        $status = $action=='accept' ? 'accepted' : 'rejected';
        $stmt2->bind_param("sii", $status, $booking_id, $coach_id);
        $stmt2->execute();
    }
}

// Récupérer toutes les réservations
$stmt3 = $conn->prepare("
SELECT b.id, b.booking_date, b.start_time, b.end_time, b.status,
       u.first_name, u.last_name
FROM bookings b
JOIN users u ON u.id = b.athlete_id
WHERE b.coach_id=?
ORDER BY b.booking_date DESC, b.start_time DESC
");
$stmt3->bind_param("i", $coach_id);
$stmt3->execute();
$bookings = $stmt3->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Bookings</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<?php include '../includes/coach-header.php'; ?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Manage Bookings</h1>

    <div class="space-y-4">
    <?php while ($b = $bookings->fetch_assoc()): ?>
        <div class="bg-white p-4 rounded shadow flex justify-between items-center hover:shadow-lg transition">
            <div>
                <p><strong>Athlete:</strong> <?= htmlspecialchars($b['first_name'].' '.$b['last_name']) ?></p>
                <p><strong>Date:</strong> <?= $b['booking_date'] ?></p>
                <p><strong>Time:</strong> <?= $b['start_time'].'-'.$b['end_time'] ?></p>
                <p><strong>Status:</strong> <?= ucfirst($b['status']) ?></p>
            </div>
            <?php if ($b['status'] === 'pending'): ?>
                <div class="space-x-2">
                    <a href="?action=accept&booking_id=<?= $b['id'] ?>" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Accept</a>
                    <a href="?action=reject&booking_id=<?= $b['id'] ?>" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Reject</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
    </div>
</div>

</body>
</html>
