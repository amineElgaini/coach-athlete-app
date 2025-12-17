<?php
require '../includes/db.php';
$coach_id = intval($_GET['coach_id']);

$stmt = $conn->prepare("
SELECT * FROM availabilities
WHERE coach_id = ? AND is_available = 1 AND date >= CURDATE()
ORDER BY date, start_time
");
$stmt->bind_param("i", $coach_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Time Slots</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">

    <?php include '../includes/athlete-header.php'; ?>

    <div class="max-w-3xl mx-auto mt-6">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Available Time Slots</h2>

        <?php if ($result->num_rows == 0): ?>
            <p class="text-gray-600">No available slots at the moment.</p>
        <?php else: ?>
            <div class="grid gap-4">
                <?php while ($a = $result->fetch_assoc()): ?>
                    <form action="book.php" method="POST" class="bg-white p-4 rounded shadow flex justify-between items-center hover:bg-blue-50 transition">
                        <div class="text-gray-800 font-medium">
                            <span class="block"><?= date('D, M d, Y', strtotime($a['date'])) ?></span>
                            <span class="block text-gray-500"><?= date('H:i', strtotime($a['start_time'])) ?> - <?= date('H:i', strtotime($a['end_time'])) ?></span>
                        </div>
                        <input type="hidden" name="availability_id" value="<?= $a['id'] ?>">
                        <input type="hidden" name="coach_id" value="<?= $coach_id ?>">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Book</button>
                    </form>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>