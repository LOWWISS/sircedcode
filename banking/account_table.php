// account_table.php
<?php
session_start();
require 'db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Rest of the code for displaying accounts table
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accounts Table</title>
</head>
<body>
    <h1>Accounts Table</h1>
    <!-- Display accounts table here -->
</body>
</html>
