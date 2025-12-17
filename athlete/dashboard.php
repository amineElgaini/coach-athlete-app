<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/db.php';
include '../includes/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'athlete') {
    header("Location: ../login.php");
    exit;
}

$athlete_id = $_SESSION['user_id'];

// Get upcoming bookings
$stmt = $conn->prepare("
SELECT 
    b.id, b.booking_date, b.start_time, b.end_time, b.status,
    u.first_name, u.last_name
FROM bookings b
JOIN coach_profiles cp ON cp.id = b.coach_id
JOIN users u ON u.id = cp.user_id
WHERE b.athlete_id = ? AND b.booking_date >= CURDATE()
ORDER BY b.booking_date ASC
");
$stmt->bind_param("i", $athlete_id);
$stmt->execute();
$upcoming_bookings = $stmt->get_result();

// Count pending bookings
$stmt2 = $conn->prepare("SELECT COUNT(*) AS pending_count FROM bookings WHERE athlete_id = ? AND status = 'pending'");
$stmt2->bind_param("i", $athlete_id);
$stmt2->execute();
$pending_count = $stmt2->get_result()->fetch_assoc()['pending_count'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Athlete Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">

    <?php include '../includes/athlete-header.php'; ?>

    <div class="max-w-4xl mx-auto mt-6 space-y-6">

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold mb-2">Welcome, <?= htmlspecialchars($_SESSION['user_first_name']) ?>!</h2>
            <p class="text-gray-700">You have <strong class="text-blue-600"><?= $pending_count ?></strong> pending booking(s).</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Upcoming Bookings</h3>

            <?php if ($upcoming_bookings->num_rows == 0): ?>
                <p class="text-gray-600">No upcoming bookings.</p>
            <?php else: ?>
                <div class="grid gap-4">
                    <?php while ($b = $upcoming_bookings->fetch_assoc()): ?>
                        <div class="p-4 border rounded flex justify-between items-center hover:shadow-md transition">
                            <div>
                                <p><strong>Coach:</strong> <?= htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) ?></p>
                                <p><strong>Date:</strong> <?= $b['booking_date'] ?></p>
                                <p><strong>Time:</strong> <?= $b['start_time'] ?> - <?= $b['end_time'] ?></p>
                                <p>
                                    <strong>Status:</strong>
                                    <span class="px-2 py-1 rounded <?= $b['status'] == 'pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800' ?>">
                                        <?= ucfirst($b['status']) ?>
                                    </span>
                                </p>
                            </div>
                            <?php if ($b['status'] == 'pending'): ?>
                                <a href="cancel_booking.php?id=<?= $b['id'] ?>" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                                    Cancel
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="flex gap-4 flex-wrap">
                <a href="coaches.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">View Coaches</a>
                <a href="my_bookings.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">My Bookings</a>
                <a href="profile.php" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">My Profile</a>
            </div>
        </div>

    </div>

</body>

</html>