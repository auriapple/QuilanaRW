<?php
session_start();
include('db_connect.php'); // Include your database connection

if (isset($_POST['shared_id'])) {
    $shared_id = $_POST['shared_id'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM user_reviewers WHERE reviewer_id = ? AND student_id = ?");
    $stmt->bind_param("is", $shared_id, $_SESSION['login_id']); // Assuming 'login_id' is stored in session

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Reviewer has been removed successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error occurred while removing the reviewer.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}

$conn->close();
?>
