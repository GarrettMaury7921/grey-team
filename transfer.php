<?php
session_start();
include('db_connection.php'); // Include your database connection file

// Get the POST data
$amount = $_POST['amount'];
$recipient = $_POST['recipient'];
$note = $_POST['note'];
$balance = $_POST['balance'];

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['account_number'])) {
    echo json_encode(["success" => false, "error" => "User not logged in."]);
    exit();
}

$sendingAccountNumber = $_SESSION['account_number'];

// Check if the balance is sufficient
if ($amount > $balance) {
    echo json_encode(["success" => false, "error" => "Insufficient funds."]);
    exit();
}

// Call the transfer stored procedure
$sql = "CALL transfer(?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $amount, $note, $sendingAccountNumber, $recipient);

if ($stmt->execute()) {
    // Update the session balance after the transfer
    $newBalance = $balance - $amount;
    $_SESSION['balance'] = $newBalance;

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Error transferring funds."]);
}

$stmt->close();
$conn->close();
?>
