<?php
session_start();
include('db_connect.php');

// Check if the code is set
if (isset($_POST['get_code'])) {
    $code = $_POST['get_code'];

    // Query the reviewer using the provided code
    $query = $conn->prepare("
        SELECT reviewer_id, reviewer_name, topic, reviewer_type 
        FROM rw_reviewer 
        WHERE reviewer_code = ? 
        LIMIT 1
    ");
    $query->bind_param("s", $code);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $reviewer = $result->fetch_assoc();
        
        // Prepare response data
        $response = [
            'status' => 'success',
            'reviewer_id' => $reviewer['reviewer_id'],
            'reviewer_name' => $reviewer['reviewer_name'],
            'topic' => $reviewer['topic'],
            'reviewer_type' => $reviewer['reviewer_type'],
            'message' => 'Reviewer found successfully.'
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'No reviewer found with the given code.'
        ];
    }

    echo json_encode($response);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No code provided.'
    ]);
}
?>
