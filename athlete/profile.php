<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../includes/db.php';
include '../includes/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'athlete') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);

    $stmt = $conn->prepare("
        UPDATE users 
        SET first_name = ?, last_name = ?, email = ?, phone = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $phone, $user_id);

    if ($stmt->execute()) {
        $_SESSION['user_first_name'] = $first_name;
        $message = "Profile updated successfully";
    } else {
        $message = "Error updating profile";
    }
}

/* FETCH USER DATA */
$stmt = $conn->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">

<div class="w-full max-w-md bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Edit My Profile</h1>

    <?php if ($message): ?>
        <div class="mb-4 px-4 py-2 rounded <?= strpos($message, 'success') !== false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">

        <div>
            <label class="block font-medium mb-1">First Name</label>
            <input type="text" name="first_name" required
                   value="<?= htmlspecialchars($user['first_name']) ?>"
                   class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block font-medium mb-1">Last Name</label>
            <input type="text" name="last_name" required
                   value="<?= htmlspecialchars($user['last_name']) ?>"
                   class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block font-medium mb-1">Email</label>
            <input type="email" name="email" required
                   value="<?= htmlspecialchars($user['email']) ?>"
                   class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block font-medium mb-1">Phone</label>
            <input type="text" name="phone"
                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                   class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex justify-between items-center">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Update Profile
            </button>
            <a href="dashboard.php" class="text-gray-600 hover:underline">
                ← Back to Dashboard
            </a>
        </div>

    </form>
</div>

</body>
</html>
