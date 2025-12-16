<?php
session_start();

include "includes/db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hash, $role);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hash)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_role'] = $role;

        if ($role === 'coach') {
            header("Location: coach/dashboard.php");
        } else {
            header("Location: athlete/dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">

<form method="POST" class="bg-white p-6 rounded shadow w-80">
    <h1 class="text-xl font-bold mb-4 text-center">Login</h1>

    <?php if($error) echo "<p class='text-red-500 mb-3'>$error</p>"; ?>

    <input type="email" name="email" placeholder="Email" required class="w-full mb-3 px-3 py-2 border rounded">
    <input type="password" name="password" placeholder="Password" required class="w-full mb-4 px-3 py-2 border rounded">

    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>

    <p class="text-sm text-center mt-3">
        No account?
        <a href="register.php" class="text-blue-600">Register</a>
    </p>
</form>

</body>
</html>
