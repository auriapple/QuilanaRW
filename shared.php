<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
    include('header.php'); 
    include('auth.php'); 
    include('db_connect.php'); 
    ?>
    <title>Shared Reviewers | Quilana</title>
    <link rel="stylesheet" href="meatballMenuTest/meatball.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include('nav_bar.php'); ?>

    <div class="content-wrapper">
        <div class="add-course-container">
            <button class="secondary-button" id="get_code">Enter Code</button>
            <form class="search-bar" action="#" method="GET">
                <input type="text" name="query" placeholder="Search" required>
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>

        <div class="tabs-container">
            <ul class="tabs">
                <li class="tab-link active" data-tab="reviewers-tab">Reviewers</li>
                <li class="tab-link" id="details-tab-link" style="display: none;" data-tab="details-tab">Reviewer Details</li>
            </ul>
        </div>

        <div class="scrollable-content">
            <!-- Reviewers Tab -->
            <div id="reviewers-tab" class="tab-content active">
                <div class="reviewer-container">
                    <?php
                    // Check if there is a saved reviewer code in the session
                    if (isset($_SESSION['reviewer_code'])) {
                        $reviewer_code = $_SESSION['reviewer_code'];
                        // Display the reviewer by re-querying
                        $query = $conn->prepare("SELECT * FROM rw_reviewer WHERE reviewer_code = ?");
                        $query->bind_param('s', $reviewer_code);
                        $query->execute();
                        $result = $query->get_result();

                        if ($result->num_rows > 0) {
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
                            echo "<p class='error'>No reviewer found for the provided code.</p>";
                        }
                    } else {
                        echo "<p class='no-assessments'>Enter a code to display a shared reviewer</p>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Modal for entering the shared reviewer code -->
        <div id="join-class-popup" class="popup-overlay">
            <div id="join-modal-content" class="popup-content">
                <span id="modal-close" class="popup-close">&times;</span>
                <h2 id="join-class-title" class="popup-title">Enter Shared Code</h2>

                <!-- Form to submit the shared reviewer code -->
                <form id='code-frm' method="POST">
                    <div class="modal-body">
                        <div class="class-code">
                            <input type="text" name="reviewer_code" required="required" class="code" placeholder="Enter Reviewer Code" />
                        </div>
                    </div>
                    <div class="join-button">
                        <button id="join" type="submit" class="secondary-button" name="join_by_code">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal for missing reviewer code -->
        <div id="missing-code-popup" class="popup-overlay">
            <div id="missing-code-content" class="popup-content">
                <span id="missing-code-close" class="popup-close">&times;</span>
                <h2 id="missing-code-title" class="popup-title">Missing Code</h2>
                <p>Please provide a reviewer code to proceed.</p>
                <button class="secondary-button" id="missing-code-ok">OK</button>
            </div>
        </div>
    </div>

    <script>
        // Handle the popup opening for entering the code
        $('#get_code').click(function() {
            $('#join-class-popup').fadeIn();
        });

        // Handle the popup closing
        $('#modal-close').click(function() {
            $('#join-class-popup').fadeOut();
        });

        // Handle the missing code modal close
        $('#missing-code-close, #missing-code-ok').click(function() {
            $('#missing-code-popup').fadeOut();
        });

        // Handle the form submission to fetch the reviewer
        $('#code-frm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const reviewer_code = $('input[name="reviewer_code"]').val();
            // Check if the reviewer code is empty
            if (reviewer_code.trim() === '') {
                $('#missing-code-popup').fadeIn(); // Show the missing code popup
            } else {
                $.ajax({
                    type: 'POST',
                    url: 'fetch_reviewer.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('.reviewer-container').html(response); // Update the reviewer container with the response
                        localStorage.setItem('reviewer_code', reviewer_code); // Store the code in local storage
                        $('#join-class-popup').fadeOut(); // Hide the code entry popup
                    },
                    error: function() {
                        alert('Error occurred while fetching the reviewer. Please try again.');
                    }
                });
            }
        });

        // On page load, check local storage for reviewer code
        $(document).ready(function() {
            const stored_code = localStorage.getItem('reviewer_code');
            if (stored_code) {
                $('input[name="reviewer_code"]').val(stored_code); // Pre-fill the input field with the stored code
                $('#code-frm').submit(); // Automatically submit the form to fetch the reviewer
            }
        });
    </script>
</body>
</html>
