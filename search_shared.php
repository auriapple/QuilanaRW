<?php
include('db_connect.php');

// Sanitize query parameters
$search = isset($_GET['query']) ? mysqli_real_escape_string($conn, $_GET['query']) : '';
$student_id = isset($_GET['student_id']) ? mysqli_real_escape_string($conn, $_GET['student_id']) : '';

// Build the base query to get only shared reviewers for the student
$query = "
    SELECT ur.reviewer_id, ur.reviewer_name, ur.topic, ur.reviewer_type 
    FROM user_reviewers ur
    JOIN rw_reviewer rw ON ur.reviewer_id = rw.reviewer_id
    WHERE ur.student_id = '$student_id'
";

// Add search conditions if a query exists
if (!empty($search)) {
    $query .= " AND (
        ur.reviewer_name LIKE '%$search%' OR 
        ur.topic LIKE '%$search%'
    )";
}

// Order results for consistency
$query .= " ORDER BY ur.reviewer_name ASC";

// Execute the query
$result = $conn->query($query);

if (!$result) {
    // Handle database query errors
    echo json_encode([
        'status' => 'error',
        'message' => 'Database query failed: ' . $conn->error
    ]);
    exit;
}

// Display the output if results exist
echo '<div class="course-container">';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reviewer_id = $row['reviewer_id'];
        ?>
        <div class="course-card" data-id="<?php echo $reviewer_id; ?>">
            <div class="course-card-body">
                <div class="meatball-menu-container">
                    <button class="meatball-menu-btn">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="meatball-menu">
                        <div class="arrow-up"></div>                                  
                        <a href="#" class="remove_reviewer" data-id="<?php echo $reviewer_id ?>">
                            <span class="material-symbols-outlined">delete</span>Delete
                        </a>
                    </div>
                </div>
                <div class="course-card-title"><?php echo $row['reviewer_name'] ?></div>
                <div class="course-card-text">
                    Topic: <?php echo $row['topic'] ?><br>
                    Type: <?php echo $row['reviewer_type'] == 1 ? 'Test Reviewer' : 'Flashcard Reviewer'  ?>
                </div>
                <div class="course-actions">                
                    <button class="main-button" 
                        id="take_reviewer" 
                        data-id="<?php echo $row['reviewer_id']; ?>" 
                        data-type="<?php echo $row['reviewer_type']; ?>" 
                        type="button" 
                        onclick="window.location.href='take_shared_reviewer.php?reviewer_id=<?php echo $row['reviewer_id']; ?>&reviewer_type=<?php echo $row['reviewer_type']; ?>'">
                        Take Reviewer
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    echo "<p class='no-assessments'>No shared reviewers found matching your search.</p>";
}
echo '</div>';
?>
