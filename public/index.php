<?php

// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Login route
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: dashboard.php');
            exit();
        } else {
            echo "Invalid login credentials.";
        }
    } else {
        echo "No user found with this email.";
    }
}

// Dashboard route
if (isLoggedIn()) {
    // Fetch user info
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    // Display user dashboard
    echo "Welcome, " . $user['name'] . "!";
} else {
    echo "Please log in to view the dashboard.";
}

// PHP Router logic placeholder
// Add your 5 steps here for the application
// Step 1: ...
// Step 2: ...
// Step 3: ...
// Step 4: ...
// Step 5: ...

$conn->close();
?>
