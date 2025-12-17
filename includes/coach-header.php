<?php
// Make sure session is started before including this
if (!isset($_SESSION)) session_start();

// Default user info if not set
$user_role = $_SESSION['user_role'] ?? '';
$user_first_name = $_SESSION['user_first_name'] ?? '';
?>

<header class="mb-6">
    <h1 class="text-2xl font-bold mb-4">
        Welcome, <?= htmlspecialchars($user_role) ?>
        <span class="text-blue-700"><?= htmlspecialchars($user_first_name) ?></span>
    </h1>
    <nav class="mb-6">
        <a href="bookings.php" class="text-blue-500 mr-4">My Bookings</a>
        <a href="availability.php" class="text-blue-500 mr-4">Manage Availability</a>
        <a href="profile.php" class="text-blue-500 mr-4">My Profile</a>
        <a href="../logout.php" class="text-red-500">Logout</a>
    </nav>
</header>