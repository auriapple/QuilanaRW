<?php
include 'db_connect.php';

if (isset($_POST['reviewer_id'])) {
    $reviewer_id = $_POST['reviewer_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Step 1: Get all question IDs related to the reviewer
        $qry = $conn->query("SELECT rw_question_id FROM rw_questions WHERE reviewer_id = '$reviewer_id'");
        $question_ids = [];
        while ($row = $qry->fetch_assoc()) {
            $question_ids[] = $row['rw_question_id'];
        }

        if (!empty($question_ids)) {
            $question_ids_str = implode(',', $question_ids);

            // Step 2: Delete all options related to the questions
            $conn->query("DELETE FROM rw_question_opt WHERE rw_question_id IN ($question_ids_str)");

            // Step 3: Delete all identifications related to the questions
            $conn->query("DELETE FROM rw_question_identifications WHERE rw_question_id IN ($question_ids_str)");

            // Step 4: Delete all questions related to the reviewer
            $conn->query("DELETE FROM rw_questions WHERE rw_question_id IN ($question_ids_str)");
        }

        // Step 5: Delete the reviewer itself
        $delete_reviewer = $conn->query("DELETE FROM rw_reviewer WHERE reviewer_id = '$reviewer_id'");

        if ($delete_reviewer) {
            // Commit the transaction if everything was successful
            $conn->commit();
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Error deleting reviewer.');
        }
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
