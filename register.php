<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "includes/db.php";

if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'athlete') {
        header("Location: athlete/dashboard.php");
        exit;
    } elseif ($_SESSION['user_role'] === 'coach') {
        header("Location: coach/dashboard.php");
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $password, $role);
    $stmt->execute();

    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['user_role'] = $role;
    $_SESSION['user_first_name'] = $first_name;

    // Redirection selon rôle
    if ($role === 'coach') {
        header("Location: coach/dashboard.php");
    } else {
        header("Location: athlete/dashboard.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">

<form method="POST" class="bg-white p-6 rounded shadow w-80">
    <h1 class="text-xl font-bold mb-4 text-center">Register</h1>
    <input type="text" name="first_name" placeholder="First Name" required class="w-full mb-3 px-3 py-2 border rounded">
    <input type="text" name="last_name" placeholder="Last Name" required class="w-full mb-3 px-3 py-2 border rounded">
    <input type="email" name="email" placeholder="Email" required class="w-full mb-3 px-3 py-2 border rounded">
    <input type="password" name="password" placeholder="Password" required class="w-full mb-3 px-3 py-2 border rounded">

    <select name="role" required class="w-full mb-4 px-3 py-2 border rounded">
        <option value="">Select role</option>
        <option value="athlete">Athlete</option>
        <option value="coach">Coach</option>
    </select>

    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Register</button>

    <p class="text-sm text-center mt-3">
        Already have an account?
        <a href="login.php" class="text-blue-600">Login</a>
    </p>
</form>

</body>
</html>
