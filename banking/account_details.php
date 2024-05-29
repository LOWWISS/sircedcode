<?php
include 'Database.php';

$db = new Database();
$conn = $db->getConnection();

if (isset($_COOKIE['session_id'])) {
    $session_id = $_COOKIE['session_id'];
    $account_id = $db->getSessionAccountID($session_id);
    if ($account_id) {
        $details = $db->getUserDetails($account_id);
    } else {
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Account Details</title>
</head>
<body>
<div class="container">
    <h2>Account Details</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Go back to home</a>

    <?php if (isset($details)) { ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Account Number: <?php echo htmlspecialchars($details['account_number']); ?></h5>
                <p class="card-text">Owner Name: <?php echo htmlspecialchars($details['owner_name']); ?></p>
                <p class="card-text">Balance: <?php echo htmlspecialchars($details['balance']); ?></p>
            </div>
        </div>
    <?php } else { ?>
        <div class="alert alert-danger">Could not retrieve account details.</div>
    <?php } ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
