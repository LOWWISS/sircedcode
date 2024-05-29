<?php
class Database {
    private $host = "localhost";
    private $db_name = "banking";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }

    public function checkDuplicate($account_number, $owner_name) {
        $sql = "SELECT COUNT(*) as count FROM accounts WHERE account_number = ? OR owner_name = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return "Error preparing statement: " . $this->conn->error;
        }
        $stmt->bind_param("is", $account_number, $owner_name);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }

    public function createAccount($account_number, $owner_name, $initial_balance, $password) {
        if ($this->checkDuplicate($account_number, $owner_name)) {
            return "Account number or username already exists. Please choose another.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO accounts (account_number, owner_name, balance, password) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                return "Error preparing statement: " . $this->conn->error;
            }
            $stmt->bind_param("isds", $account_number, $owner_name, $initial_balance, $hashed_password);
            if ($stmt->execute()) {
                $stmt->close();
                return "Account created successfully.";
            } else {
                $stmt->close();
                return "Error creating account. Please try again.";
            }
        }
    }

    public function login($username, $password) {
        $sql = "SELECT id, password FROM accounts WHERE owner_name = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return "Error preparing statement: " . $this->conn->error;
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $hashed_password);
        if ($stmt->fetch()) {
            if (password_verify($password, $hashed_password)) {
                $session_id = bin2hex(random_bytes(32));
                $stmt->close();

                $sql = "INSERT INTO sessions (account_id, session_id) VALUES (?, ?)";
                $stmt = $this->conn->prepare($sql);
                if ($stmt === false) {
                    return "Error preparing statement: " . $this->conn->error;
                }
                $stmt->bind_param("is", $id, $session_id);
                $stmt->execute();
                $stmt->close();

                setcookie("session_id", $session_id, time() + (86400 * 30), "/");
                return $id;
            }
        }
        return false;
    }

    public function getUserDetails($account_id) {
        $sql = "SELECT account_number, owner_name, balance FROM accounts WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return "Error preparing statement: " . $this->conn->error;
        }
        $stmt->bind_param("i", $account_id);
        $stmt->execute();
        $stmt->bind_result($account_number, $owner_name, $balance);
        $stmt->fetch();
        $details = [
            "account_number" => $account_number,
            "owner_name" => $owner_name,
            "balance" => $balance
        ];
        $stmt->close();
        return $details;
    }

    public function getSessionAccountID($session_id) {
        $sql = "SELECT account_id FROM sessions WHERE session_id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return "Error preparing statement: " . $this->conn->error;
        }
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $stmt->bind_result($account_id);
        $stmt->fetch();
        $stmt->close();
        return $account_id;
    }
}
?>
