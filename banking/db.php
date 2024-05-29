<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "banking";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create tables if they do not exist
$createAccountsTable = "
CREATE TABLE IF NOT EXISTS accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_number VARCHAR(20) NOT NULL UNIQUE,
    owner_name VARCHAR(100) NOT NULL,
    balance DECIMAL(10, 2) DEFAULT 0.00,
    password VARCHAR(255) NOT NULL
)";

$createTransactionsTable = "
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    type ENUM('deposit', 'withdrawal') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id)
)";

$createSessionsTable = "
CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    session_id VARCHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id)
)";

$createUserRolesTable = "
CREATE TABLE IF NOT EXISTS user_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    role ENUM('admin', 'teller', 'customer') NOT NULL,
    FOREIGN KEY (account_id) REFERENCES accounts(id)
)";

if ($conn->query($createAccountsTable) === FALSE ||
    $conn->query($createTransactionsTable) === FALSE ||
    $conn->query($createSessionsTable) === FALSE ||
    $conn->query($createUserRolesTable) === FALSE) {
    die("Error creating tables: " . $conn->error);
}
?>