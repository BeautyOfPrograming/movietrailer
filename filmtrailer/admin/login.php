<?php
session_start();
include '../includes/db.php';

// Temporary section to generate the hash and update the database
if (isset($_GET['update_password'])) {
    // Generate a new password hash
    $password = 'admin123'; // The plain-text password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Update the database with the new hash
    $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hashedPassword]);

    echo "Password updated successfully. New hashed password: " . $hashedPassword;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the statement
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);

    // Fetch the admin data as an associative array
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debugging output
    if ($admin) {
        echo "Admin found: ";
        print_r($admin); // This will show the contents of the $admin array
    } else {
        echo "No admin found with that username.";
    }

    // Check if the admin exists and verify the password
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}

?>
<?php include '../includes/header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4">Admin Login</h2>
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
