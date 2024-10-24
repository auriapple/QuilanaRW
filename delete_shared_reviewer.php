<?php
session_start();
include('db_connect.php'); // Include your database connection

if (isset($_POST['shared_id'])) {
    $shared_id = $_POST['shared_id'];

    // Prepare and execute the SQL query to delete the reviewer
    $stmt = $conn->prepare("DELETE FROM user_reviewers WHERE shared_id = ?");
    $stmt->bind_param("i", $shared_id);

    if ($stmt->execute()) {
        // Successfully deleted
        echo json_encode(['status' => 'success', 'message' => 'Reviewer has been removed successfully.']);
    } else {
        // Error during deletion
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete the reviewer.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}

$conn->close();
?>
