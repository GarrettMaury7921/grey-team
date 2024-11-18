<?php 
session_start(); 

// Database credentials
$servername = "sql1"; // Assuming MySQL is on the same server
$username = "haiku_user";  // MySQL username
$password = "epicgarrett"; // MySQL password
$dbname = "clients"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['uname']) && isset($_POST['password'])) {

    // Function to validate user input
    function validate($data){
       $data = trim($data);
       $data = stripslashes($data);
       $data = htmlspecialchars($data);
       return $data;
    }

    $uname = validate($_POST['uname']);
    $pass = validate($_POST['password']);

    // Check if username or password is empty
    if (empty($uname)) {
        header("Location: index.php?error=User Name is required");
        exit();
    } else if (empty($pass)) {
        header("Location: index.php?error=Password is required");
        exit();
    } else {
        // SQL query to fetch the user from the database
        $sql = "SELECT * FROM users WHERE user_name = ? AND password = ?";

        // Prepare statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $uname, $pass);  // 'ss' specifies the types of the parameters (string, string)

        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user is found
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Verify the username and password match
            if ($row['user_name'] === $uname && $row['password'] === $pass) {
                // Set session variables
                $_SESSION['user_name'] = $row['user_name'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['id'] = $row['id'];
                // Redirect to home.php
                header("Location: home.php");
                exit();
            } else {
                header("Location: index.php?error=Incorrect User name or password");
                exit();
            }
        } else {
            header("Location: index.php?error=Incorrect User name or password");
            exit();
        }
    }

} else {
    header("Location: index.php");
    exit();
}

// Close the database connection
$conn->close();
?>
