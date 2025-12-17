<?php
require '../includes/db.php';
$coach_id = intval($_GET['id']);

// Get coach info
$sql = "
SELECT 
    u.first_name, u.last_name,
    cp.photo, cp.biography, cp.certifications, cp.years_experience
FROM coach_profiles cp
JOIN users u ON u.id = cp.user_id
WHERE cp.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $coach_id);
$stmt->execute();
$coach = $stmt->get_result()->fetch_assoc();

// Get disciplines
$disciplines_stmt = $conn->prepare("
SELECT d.name, cd.level
FROM coach_disciplines cd
JOIN disciplines d ON d.id = cd.discipline_id
WHERE cd.coach_id = ?
");
$disciplines_stmt->bind_param("i", $coach_id);
$disciplines_stmt->execute();
$disciplines = $disciplines_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $coach['first_name'] ?> <?= $coach['last_name'] ?> - Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">

    <?php include '../includes/athlete-header.php'; ?>

    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex flex-col md:flex-row items-start md:items-center mb-6">
            <img src="../<?= $coach['photo'] ?>" alt="<?= $coach['first_name'] ?>" class="w-40 h-40 object-cover rounded mr-6 mb-4 md:mb-0">
            <div>
                <h1 class="text-3xl font-bold mb-2"><?= $coach['first_name'] ?> <?= $coach['last_name'] ?></h1>
                <p class="text-gray-600 mb-1"><?= $coach['biography'] ?></p>
                <p class="text-gray-500 mb-1"><strong>Experience:</strong> <?= $coach['years_experience'] ?> years</p>
                <p class="text-gray-500 mb-1"><strong>Certifications:</strong> <?= $coach['certifications'] ?></p>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-2xl font-semibold mb-2">Disciplines</h2>
            <ul class="list-disc list-inside text-gray-700">
                <?php while ($d = $disciplines->fetch_assoc()): ?>
                    <li><?= $d['name'] ?> (<?= $d['level'] ?>)</li>
                <?php endwhile; ?>
            </ul>
        </div>

        <a href="availability.php?coach_id=<?= $coach_id ?>" class="inline-block bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition">
            Book a Session
        </a>
    </div>

</body>
</html>
