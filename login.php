<?php
// MySQL connection details
$servername = "localhost";
$username = "haiku_user";  // Replace with your MySQL username
$password = "epicgarrett";  // Replace with your MySQL password
$dbname = "your_database";  // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get JSON input data
$data = json_decode(file_get_contents('php://input'), true);
$user = $data['username'];
$pass = $data['password'];
$captcha = $data['captcha'];  // CAPTCHA validation would need to be done here as well

// Here, you should add CAPTCHA validation against the session or a secret value
// For now, we assume CAPTCHA is validated and proceed with login validation

// SQL query to check username and password
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user);  // "s" denotes a string parameter
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Check password (hashing in production is highly recommended)
    $row = $result->fetch_assoc();
    if (password_verify($pass, $row['password'])) {  // Assuming the password is hashed
        // Login successful
        echo json_encode(['success' => true]);
    } else {
        // Incorrect password
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
} else {
    // No user found
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}

// Ensure to handle the POST request securely
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "Clients"); // Modify these credentials as needed

    // Check for connection errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use prepared statements to avoid SQL injection
    $stmt = $conn->prepare("SELECT Balance FROM ClientInfo WHERE Username = ? AND Password = ?");
    $stmt->bind_param("ss", $username, $password);  // "ss" means string, string for username and password

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, fetch balance
        $row = $result->fetch_assoc();
        $balance = $row['Balance'];

        // Return success and balance to the frontend
        echo json_encode(['success' => true, 'username' => $username, 'balance' => $balance]);
    } else {
        // Authentication failed
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $conn->close();
}


// Close the connection
$stmt->close();
$conn->close();
?>
