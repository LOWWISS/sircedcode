<?php
include 'Database.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_name = $_POST['owner_name'];
    $password = $_POST['password'];
    $account_id = $db->login($owner_name, $password);
    if ($account_id) {
        header("Location: account_details.php");
        exit();
    } else {
        $message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Login</title>
</head>
<body>
<div class="container"> 
    <h2>Login</h2>
    <a href="create_table.php" class="btn btn-secondary mb-3">Register</a>
    <?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accountNumber = $_POST['account_number'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM accounts WHERE account_number = ?");
    $stmt->bind_param("s", $accountNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $account = $result->fetch_assoc();
        if (password_verify($password, $account['password'])) {
            // Get the user's role
            $stmtRole = $conn->prepare("SELECT role FROM user_roles WHERE account_id = ?");
            $stmtRole->bind_param("i", $account['id']);
            $stmtRole->execute();
            $resultRole = $stmtRole->get_result();
            $role = $resultRole->fetch_assoc()['role'];

            // Set session variables
            $_SESSION['account_id'] = $account['id'];
            $_SESSION['account_number'] = $accountNumber;
            $_SESSION['role'] = $role;

            // Redirect based on role
            if ($role == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($role == 'teller') {
                header("Location: teller_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Account not found.";
    }
}
?>

    <?php if (isset($message)) { echo "<div class='alert alert-danger'>$message</div>"; } ?>

    <form method="post" action="login.php">
        <div class="form-group">
            <label for="owner_name">User Name:</label>
            <input type="text" class="form-control" id="owner_name" name="owner_name" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
