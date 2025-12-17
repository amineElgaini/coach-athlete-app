<?php
require '../includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Coaches</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">

    <?php include '../includes/athlete-header.php'; ?>

    <div class="max-w-5xl mx-auto mt-6">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800">Available Coaches</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $sql = "
            SELECT 
                cp.id AS coach_id,
                u.first_name,
                u.last_name,
                cp.photo,
                cp.biography,
                cp.years_experience
            FROM coach_profiles cp
            JOIN users u ON u.id = cp.user_id
            ";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()):
            ?>
                <div class="bg-white rounded shadow p-4 hover:shadow-lg transition">
                    <img src="../<?= $row['photo'] ?>" alt="<?= $row['first_name'] ?>" class="w-full h-40 object-cover rounded mb-4">
                    <h3 class="text-xl font-semibold mb-2"><?= $row['first_name'] ?> <?= $row['last_name'] ?></h3>
                    <p class="text-gray-600 mb-2"><?= $row['biography'] ?></p>
                    <p class="text-gray-500 mb-4">Experience: <?= $row['years_experience'] ?> years</p>
                    <a href="coach_profile.php?id=<?= $row['coach_id'] ?>" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">View Profile</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>
