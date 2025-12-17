<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'includes/db.php'; // include database connection

// Redirect if user is already logged in
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'athlete') {
        header("Location: athlete/dashboard.php");
        exit;
    } elseif ($_SESSION['user_role'] === 'coach') {
        header("Location: coach/dashboard.php");
        exit;
    }
}

// Fetch available disciplines from "disciplines" table
$disciplines = [];
$sql = "SELECT name FROM disciplines ORDER BY name ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $disciplines[] = $row['name'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athletes & Coaches Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Sport & Coach</h1>
            <nav>
                <a href="login.php" class="mr-4 hover:underline">Login</a>
                <a href="register.php" class="hover:underline">Register</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-grow container mx-auto px-4 mt-12">
        <section class="text-center mb-12">
            <h2 class="text-4xl font-bold mb-4">Welcome to your sports platform</h2>
            <p class="text-lg mb-6">Find professional coaches or manage your sessions easily.</p>
            <div>
                <a href="register.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition mr-4">Register</a>
                <a href="login.php" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition">Login</a>
            </div>
        </section>

        <!-- Athletes / Coaches Sections -->
        <section class="grid md:grid-cols-2 gap-6 mb-12">
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <h3 class="text-2xl font-semibold mb-2">For Athletes</h3>
                <p>Browse coach profiles, book sessions, and track your progress.</p>
                <a href="register.php" class="mt-4 inline-block text-blue-600 hover:underline">Join as Athlete</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <h3 class="text-2xl font-semibold mb-2">For Coaches</h3>
                <p>Manage your availability, accept bookings, and grow your client base.</p>
                <a href="register.php" class="mt-4 inline-block text-blue-600 hover:underline">Join as Coach</a>
            </div>
        </section>

        <!-- Popular Disciplines -->
        <section class="mb-12">
            <h3 class="text-3xl font-bold mb-4 text-center">Popular Disciplines</h3>
            <div class="flex flex-wrap justify-center gap-4">
                <?php if (!empty($disciplines)): ?>
                    <?php foreach($disciplines as $d): ?>
                        <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full shadow-sm"><?= htmlspecialchars($d) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500">No disciplines available at the moment.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-4 text-center">
        &copy; <?= date('Y'); ?> Sport & Coach. All rights reserved.
    </footer>

</body>
</html>
