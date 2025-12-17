<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'athlete') {
    header("Location: ../login.php");
    exit;
}

$athlete_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT 
    b.id, b.booking_date, b.start_time, b.end_time, b.status,
    u.first_name, u.last_name
FROM bookings b
JOIN coach_profiles cp ON cp.id = b.coach_id
JOIN users u ON u.id = cp.user_id
WHERE b.athlete_id = ?
ORDER BY b.booking_date DESC
");

$stmt->bind_param("i", $athlete_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">

    <?php include '../includes/athlete-header.php'; ?>

    <div class="max-w-4xl mx-auto mt-6">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800">My Reservations</h2>

        <?php if ($result->num_rows == 0): ?>
            <p class="text-gray-600">You have no reservations yet.</p>
        <?php else: ?>
            <div class="grid gap-4">
                <?php while ($b = $result->fetch_assoc()): ?>
                    <div class="bg-white p-4 rounded shadow flex flex-col md:flex-row md:justify-between md:items-center hover:shadow-lg transition">
                        <div class="mb-2 md:mb-0">
                            <p><strong>Coach:</strong> <?= htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) ?></p>
                            <p><strong>Date:</strong> <?= htmlspecialchars($b['booking_date']) ?></p>
                            <p><strong>Time:</strong> <?= htmlspecialchars($b['start_time'] . ' - ' . $b['end_time']) ?></p>
                            <p>
                                <strong>Status:</strong> 
                                <?php 
                                    $status_colors = [
                                        'pending' => 'bg-yellow-200 text-yellow-800',
                                        'accepted' => 'bg-green-200 text-green-800',
                                        'rejected' => 'bg-red-200 text-red-800',
                                        'cancelled' => 'bg-gray-200 text-gray-800'
                                    ];
                                ?>
                                <span class="px-2 py-1 rounded <?= $status_colors[$b['status']] ?? 'bg-gray-200 text-gray-800' ?>">
                                    <?= ucfirst($b['status']) ?>
                                </span>
                            </p>
                        </div>

                        <?php if ($b['status'] == 'pending'): ?>
                            <a href="cancel_booking.php?id=<?= $b['id'] ?>" class="mt-2 md:mt-0 inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                                Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
