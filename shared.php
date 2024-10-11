<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <?php include('auth.php'); ?>
    <?php include('db_connect.php'); ?>
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
                    <p class="no-assessments">Enter a code to display a shared reviewer</p>
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

        // Handle the form submission via AJAX
        $('#code-frm').submit(function(e) {
            e.preventDefault();
            var code = $('input[name="reviewer_code"]').val().trim();

            if (code === "") {
                // Show the missing code modal if the input is empty
                $('#join-class-popup').fadeOut();
                $('#missing-code-popup').fadeIn();
            } else {
                // Proceed with AJAX request if code is provided
                $.ajax({
                    url: 'fetch_reviewer.php', // Call to backend to fetch reviewer by code
                    type: 'POST',
                    data: { reviewer_code: code },
                    success: function(response) {
                        // Display the fetched reviewer in the reviewers tab
                        $('.reviewer-container').html(response);
                        $('#join-class-popup').fadeOut();
                    },
                    error: function() {
                        alert('Failed to fetch reviewer. Please check the code.');
                    }
                });
            }
        });
    </script>
</body>
</html>
