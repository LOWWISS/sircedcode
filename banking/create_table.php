<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Create Account</title>
</head>
<body>
    <div class="container">
    <h2>Create Account</h2>
    <a href="login.php" class="btn btn-secondary mb-3">Login</a>
<div class="container">
    <form action="create_table.php" method="post">
        <div class="form-group">
            <label for="accountNumber">Account Number:</label>
            <input type="text" class="form-control" id="accountNumber" name="accountNumber" required>
        </div>
        <div class="form-group">
            <label for="ownerName">Owner Name:</label>
            <input type="text" class="form-control" id="ownerName" name="ownerName" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="role">Role:</label>
            <select class="form-control" id="role" name="role" required>
                <option value="customer">Customer</option>
                <option value="teller">Teller</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create Account</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include 'db.php';
        include 'Account.php';

        $account = new Account($conn);
        $accountNumber = $_POST['accountNumber'];
        $ownerName = $_POST['ownerName'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        if ($account->createAccount($accountNumber, $ownerName, $password, $role)) {
            echo "<div class='alert alert-success'>Account created successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error creating account!</div>";
        }
    }
    ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
