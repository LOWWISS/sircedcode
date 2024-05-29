<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Transfer Money</title>
</head>
<body>
    <div class="container">
    <h2>Transfer Money</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Go back to home</a>
     <!-- Rest of your code -->
</div>
<div class="container">
    <?php
    include 'db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $from_account = $_POST['from_account'];
        $to_account = $_POST['to_account'];
        $amount = $_POST['amount'];

        // Check if both accounts exist and if the from_account has sufficient balance
        $sql = "SELECT balance FROM accounts WHERE account_number = ?";
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("i", $from_account);
        $stmt->execute();
        $stmt->bind_result($from_balance);
        $stmt->fetch();
        // No need to close the statement here

        $stmt->bind_param("i", $to_account);
        $stmt->execute();
        $stmt->bind_result($to_balance);
        $stmt->fetch();
        $stmt->close(); // Close the statement here

        if ($from_balance === null || $to_balance === null) {
            echo "<div class='alert alert-danger'>One or both account numbers are invalid.</div>";
        } elseif ($from_balance < $amount) {
            echo "<div class='alert alert-danger'>Insufficient balance in the source account.</div>";
        } else {
            // Deduct from the source account
            $new_from_balance = $from_balance - $amount;
            $sql = "UPDATE accounts SET balance = ? WHERE account_number = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("di", $new_from_balance, $from_account);
            $stmt->execute();
            $stmt->close();

            // Add to the destination account
            $new_to_balance = $to_balance + $amount;
            $sql = "UPDATE accounts SET balance = ? WHERE account_number = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("di", $new_to_balance, $to_account);
            $stmt->execute();
            $stmt->close();

            echo "<div class='alert alert-success'>Transfer successful.</div>";
        }

        $conn->close();
    }
    ?>

    <form method="post" action="transfer.php">
        <div class="form-group">
            <label for="from_account">From Account Number:</label>
            <input type="number" class="form-control" id="from_account" name="from_account" required>
        </div>
        <div class="form-group">
            <label for="to_account">To Account Number:</label>
            <input type="number" class="form-control" id="to_account" name="to_account" required>
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-primary">Transfer</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
