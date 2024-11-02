<?php
// Include your database connection
include('db_connect.php');

// Check if reviewerId is set
if (isset($_POST['reviewerId'])) {
    $reviewerId = $_POST['reviewerId'];

    // Prepare and execute the query to check for questions
    $stmt = $conn->prepare("SELECT COUNT(*) AS question_count FROM rw_questions WHERE reviewer_id = ?");
    $stmt->bind_param("i", $reviewerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch the result
    $row = $result->fetch_assoc();
    
    // Check if there are questions associated with the reviewer
    if ($row['question_count'] > 0) {
        // If questions exist
        echo json_encode(['success' => true, 'message' => 'Questions exist.']);
    } else {
        // If no questions exist
        echo json_encode(['success' => false, 'message' => 'No questions found for this reviewer.']);
    }

    $stmt->close();
} else {
    // If reviewerId is not set
    echo json_encode(['success' => false, 'message' => 'Reviewer ID not provided.']);
}

$conn->close();
?>
