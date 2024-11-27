<?php
include('db_connect.php');

// Ensure query parameters and session variables are sanitized
$search = isset($_GET['query']) ? mysqli_real_escape_string($conn, $_GET['query']) : '';
$student_id = isset($_GET['student_id']) ? mysqli_real_escape_string($conn, $_GET['student_id']) : '';

// Build the base query
$query = "SELECT * FROM rw_reviewer WHERE student_id = '$student_id'";

// Add search conditions if a query exists
if (!empty($search)) {
    $query .= " AND (
        reviewer_name LIKE '%$search%' OR 
        topic LIKE '%$search%'
    )";
}

// Order results for consistency
$query .= " ORDER BY reviewer_name ASC";

// Execute the query
$result = $conn->query($query);

if (!$result) {
    // Handle database query errors gracefully
    echo "<div class='error-message'>Error: " . $conn->error . "</div>";
    exit;
}

// Output the results
echo '<div class="course-container">';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reviewer_id = $row['reviewer_id'];
        ?>
        <div class="course-card">
                                <div class="course-card-body">
                                    <div class="meatball-menu-container">
                                        <button class="meatball-menu-btn">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="meatball-menu">
                                            <div class="arrow-up"></div>
                                            <a href="#" class="edit_reviewer" data-id="<?php echo $reviewer_id ?>"><span class="material-symbols-outlined">Edit</span>Edit</a>
                                            <a href="#" class="remove_reviewer" data-id="<?php echo $reviewer_id ?>"><span class="material-symbols-outlined">delete</span>Delete</a>
                                            <a href="#" class="share_reviewer" data-id="<?php echo $reviewer_id ?>" data-type="<?php echo $row['reviewer_type']; ?>" ><span class="material-symbols-outlined">key</span>Get Code</a>
                                        </div>
                                    </div>
                                    <div class="course-card-title"><?php echo $row['reviewer_name'] ?></div>
                                    <div class="course-card-text">
                                        Topic: <?php echo $row['topic'] ?><br>
                                        Type: <?php echo $row['reviewer_type'] == 1 ? 'Test Reviewer' : 'Flashcard Reviewer'  ?>
                                    </div>
                                    <div class="course-actions">
                                        <a class="tertiary-button" id="view_reviewer_details" 
                                        href="manage_reviewer.php?reviewer_id=<?php echo $reviewer_id ?>" type="button"> Manage</a>                                
                                        <button class="main-button take-reviewer" 
                                            id="take_reviewer" 
                                            data-id="<?php echo $row['reviewer_id']; ?>" 
                                            data-type="<?php echo $row['reviewer_type']; ?>" 
                                            type="button">
                                            Take Reviewer
                                        </button>
                                    </div>
                                </div>
                            </div>
        <?php
    }
} else {
    echo "<p class='no-assessments'>No reviewers found matching your search.</p>";
}
echo '</div>';
?>
