<?php
session_start(); // Start the session
include('db_connect.php'); // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the reviewer code is submitted
    if (isset($_POST['reviewer_code']) && !empty(trim($_POST['reviewer_code']))) {
        $reviewer_code = trim($_POST['reviewer_code']);
        $_SESSION['reviewer_code'] = $reviewer_code; // Save the code in the session

        // Query the database for the provided reviewer code
        $query = $conn->prepare("SELECT * FROM rw_reviewer WHERE reviewer_code = ?");
        $query->bind_param('s', $reviewer_code);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            // If the reviewer code is found, display the reviewer details in the card format
            $reviewer = $result->fetch_assoc();
            $reviewer_id = htmlspecialchars($reviewer['reviewer_id']);
            $reviewer_name = htmlspecialchars($reviewer['reviewer_name']);
            $topic = htmlspecialchars($reviewer['topic']);
            $reviewer_type = htmlspecialchars($reviewer['reviewer_type']);

            echo "
            <div class='course-card'>
                <div class='course-card-body'>
                    <div class='meatball-menu-container'>
                        <button class='meatball-menu-btn'>
                            <i class='fas fa-ellipsis-v'></i>
                        </button>
                        <div class='meatball-menu'>
                            <a href='#' class='edit_reviewer' data-id='$reviewer_id'>Edit</a>
                            <a href='#' class='remove_reviewer' data-id='$reviewer_id'>Remove</a>
                            <a href='#' class='share_reviewer' data-id='$reviewer_id'>Get Code</a>
                        </div>
                    </div>
                    <div class='course-card-title'>$reviewer_name</div>
                    <div class='course-card-text'>Topic: <br>$topic</div>
                    <div class='course-actions'>
                        <button class='main-button' id='take_reviewer' data-id='$reviewer_id' data-type='$reviewer_type' type='button' onclick=\"window.location.href='take_reviewer.php?reviewer_id=$reviewer_id&reviewer_type=$reviewer_type'\">
                            Take Reviewer
                        </button>
                    </div>
                </div>
            </div>";
        } else {
            // If no matching reviewer is found
            echo "<p class='error'>No reviewer found for the provided code.</p>";
        }
    } else {
        // If no code is provided in the POST request
        echo "<p class='error'>Please provide a valid reviewer code.</p>";
    }
} else {
    // If the request method is not POST
    echo "<p class='error'>Invalid request method.</p>";
}
?>
