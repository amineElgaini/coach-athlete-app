<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../includes/db.php';
include '../includes/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'coach') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

/* HANDLE FORM SUBMIT */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $biography  = trim($_POST['biography']);
    $years_exp  = (int)$_POST['years_experience'];
    $certs      = trim($_POST['certifications']);

    /* PHOTO UPLOAD */
    $photoPath = null;
    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = "../uploads/coaches/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = time() . "_" . basename($_FILES['photo']['name']);
        $photoPath = "uploads/coach_photos/" . $filename;
        move_uploaded_file($_FILES['photo']['tmp_name'], "../" . $photoPath);
    }

    /* UPDATE USERS TABLE */
    $stmt = $conn->prepare("
        UPDATE users 
        SET first_name=?, last_name=?, email=?, phone=?
        WHERE id=?
    ");
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $phone, $user_id);
    $stmt->execute();

    /* UPDATE / INSERT COACH PROFILE */
    $stmt = $conn->prepare("SELECT id FROM coach_profiles WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;

    if ($exists) {
        if ($photoPath) {
            $stmt = $conn->prepare("
                UPDATE coach_profiles 
                SET photo=?, biography=?, years_experience=?, certifications=?
                WHERE user_id=?
            ");
            $stmt->bind_param("ssisi", $photoPath, $biography, $years_exp, $certs, $user_id);
        } else {
            $stmt = $conn->prepare("
                UPDATE coach_profiles 
                SET biography=?, years_experience=?, certifications=?
                WHERE user_id=?
            ");
            $stmt->bind_param("sisi", $biography, $years_exp, $certs, $user_id);
        }
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("
            INSERT INTO coach_profiles (user_id, photo, biography, years_experience, certifications)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issis", $user_id, $photoPath, $biography, $years_exp, $certs);
        $stmt->execute();
    }

    $_SESSION['user_first_name'] = $first_name;
    $message = "Coach profile updated successfully ✅";
}

/* FETCH DATA */
$query = "
    SELECT u.first_name, u.last_name, u.email, u.phone,
           c.photo, c.biography, c.years_experience, c.certifications
    FROM users u
    LEFT JOIN coach_profiles c ON u.id = c.user_id
    WHERE u.id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Coach Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100  p-4">
    <?php include '../includes/coach-header.php'; ?>

    <div class="max-w-3xl mx-auto mt-10 bg-white p-6 rounded shadow">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Coach Profile</h1>

            <a href="dashboard.php"
                class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                ← Back to Dashboard
            </a>
        </div>

        <?php if ($message): ?>
            <div class="mb-4 text-green-600 font-medium"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-5">

            <!-- Personal Info -->
            <h2 class="font-semibold text-lg border-b pb-1">Personal Information</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">First Name</label>
                    <input type="text" name="first_name" required
                        value="<?= htmlspecialchars($data['first_name']) ?>"
                        class="w-full border p-2 rounded">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Last Name</label>
                    <input type="text" name="last_name" required
                        value="<?= htmlspecialchars($data['last_name']) ?>"
                        class="w-full border p-2 rounded">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" required
                    value="<?= htmlspecialchars($data['email']) ?>"
                    class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Phone</label>
                <input type="text" name="phone"
                    value="<?= htmlspecialchars($data['phone'] ?? '') ?>"
                    class="w-full border p-2 rounded">
            </div>

            <!-- Professional Info -->
            <h2 class="font-semibold text-lg border-b pb-1 mt-6">Professional Information</h2>

            <?php
            $photo = !empty($data['photo'])
                ? "../" . $data['photo']
                : "../uploads/coach_photos/default.png";
            ?>

            <div>
                <label class="block text-sm font-medium mb-1">Profile Photo</label>
                <img src="<?= $photo ?>"
                    class="w-32 h-32 rounded-full mb-2 object-cover border">

                <input type="file" name="photo"
                    class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Biography</label>
                <textarea name="biography" rows="4"
                    class="w-full border p-2 rounded"><?= htmlspecialchars($data['biography'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Years of Experience</label>
                <input type="number" name="years_experience" min="0"
                    value="<?= htmlspecialchars($data['years_experience'] ?? 0) ?>"
                    class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Certifications</label>
                <textarea name="certifications" rows="3"
                    class="w-full border p-2 rounded"><?= htmlspecialchars($data['certifications'] ?? '') ?></textarea>
            </div>

            <!-- Actions -->
            <div class="flex gap-4 pt-4">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Save Profile
                </button>
            </div>

        </form>
    </div>


</body>

</html>