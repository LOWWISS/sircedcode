<?php
class Account {
    private $conn;
    private $table = 'accounts';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createAccount($accountNumber, $ownerName, $password, $role) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("INSERT INTO $this->table (account_number, owner_name, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $accountNumber, $ownerName, $hashed_password);
        $success = $stmt->execute();

        if ($success) {
            $accountId = $this->conn->insert_id;
            $this->assignRole($accountId, $role);
        }

        return $success;
    }

    public function assignRole($accountId, $role) {
        $stmt = $this->conn->prepare("INSERT INTO user_roles (account_id, role) VALUES (?, ?)");
        $stmt->bind_param("is", $accountId, $role);
        return $stmt->execute();
    }

    public function accountExists($accountNumber) {
        $stmt = $this->conn->prepare("SELECT id FROM $this->table WHERE account_number = ?");
        $stmt->bind_param("s", $accountNumber);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function deposit($accountNumber, $amount) {
        if (!$this->accountExists($accountNumber)) {
            return "Account number does not exist";
        }

        $stmt = $this->conn->prepare("UPDATE $this->table SET balance = balance + ? WHERE account_number = ?");
        $stmt->bind_param("ds", $amount, $accountNumber);
        $success = $stmt->execute();

        if ($success) {
            $this->recordTransaction($accountNumber, 'deposit', $amount);
        }

        return $success;
    }

    public function withdraw($accountNumber, $amount) {
        if (!$this->accountExists($accountNumber)) {
            return "Account number does not exist";
        }

        $stmt = $this->conn->prepare("UPDATE $this->table SET balance = balance - ? WHERE account_number = ? AND balance >= ?");
        $stmt->bind_param("dsd", $amount, $accountNumber, $amount);
        $success = $stmt->execute();

        if ($success) {
            $this->recordTransaction($accountNumber, 'withdrawal', $amount);
        }

        return $success;
    }

    public function getBalance($accountNumber) {
        if (!$this->accountExists($accountNumber)) {
            return "Account number does not exist";
        }

        $stmt = $this->conn->prepare("SELECT balance FROM $this->table WHERE account_number = ?");
        $stmt->bind_param("s", $accountNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['balance'];
    }

    private function recordTransaction($accountNumber, $type, $amount) {
        $stmt = $this->conn->prepare("INSERT INTO transactions (account_id, type, amount) VALUES ((SELECT id FROM accounts WHERE account_number = ?), ?, ?)");
        $stmt->bind_param("ssd", $accountNumber, $type, $amount);
        return $stmt->execute();
    }
}
?>