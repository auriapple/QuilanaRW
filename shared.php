<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <?php include('auth.php'); ?>
    <?php include('db_connect.php'); ?>
    <title>Shared | Quilana</title>
    <link rel="stylesheet" href="meatballMenuTest/meatball.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include('nav_bar.php'); ?>
    <div class="content-wrapper">
        <!-- Header Container -->
        <div class="join-class-container">
            <button class="secondary-button" id="joinClass">Enter Code</button>
            <form class="search-bar" action="#" method="GET">
                <input type="text" name="query" placeholder="Search" required>
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <ul class="tabs">
                <li class="tab-link active" data-tab="reviewer-tab">Shared Reviewers</li>
            </ul>
        </div>

        <div class="scrollable-content">
            <!-- Shared Reviewers Tab -->
            <div id="reviewer-tab" class="tab-content active">
                <div class="course-container">
                    <?php
                    $qry = $conn->query("SELECT * FROM user_reviewers WHERE student_id = '".$_SESSION['login_id']."' ORDER BY topic ASC");
                    if ($qry->num_rows > 0) {
                        while ($row = $qry->fetch_assoc()) {
                            $reviewer_id = $row['reviewer_id'];
                    ?>
                        <div class="course-card" data-id="<?php echo $reviewer_id; ?>"> <!-- Added data-id here -->
                            <div class="course-card-body">
                                <div class="meatball-menu-container">
                                    <button class="meatball-menu-btn">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="meatball-menu">                                    
                                        <a href="#" class="remove_reviewer" data-id="<?php echo $reviewer_id ?>">Remove</a>
                                    </div>
                                </div>
                                <div class="course-card-title"><?php echo $row['reviewer_name'] ?></div>
                                <div class="course-card-text">Topic: <br><?php echo $row['topic'] ?></div>
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
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Modal for entering reviewer code -->
        <div id="join-class-popup" class="popup-overlay">
            <div id="join-modal-content" class="popup-content">
                <span id="modal-close" class="popup-close">&times;</span>
                <h2 id="join-class-title" class="popup-title">Enter Shared Code</h2>

                <!-- Form to submit the reviewer code -->
                <form id="code-frm" action="" method="POST">
                    <div class="modal-body">
                        <div class="class-code">
                            <input type="text" name="get_code" required class="code" placeholder="Reviewer Code" />
                        </div>
                    </div>
                    <div class="join-button">
                        <button id="join" type="submit" class="secondary-button" name="join_by_code">Enter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal for success/error message -->
        <div id="message-popup" class="popup-overlay" style="display: none;">
            <div id="message-modal-content" class="popup-content">
                <span id="message-modal-close" class="popup-close">&times;</span>
                <h2 id="message-popup-title" class="popup-title">Message</h2>
                <div id="message-body" class="modal-body">
                    <!-- Message will be dynamically inserted here -->
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // Button Visibility
                function updateButtons() {
                    var activeTab = $('.tab-link.active').data('tab');
                    if (activeTab === 'assessments-tab') {
                        $('#joinClass').hide();
                    } else {
                        $('#joinClass').show();
                    }
                }

                // Tab Functionality
                $('.tab-link').click(function() {
                    var tab_id = $(this).data('tab');
                    $('.tab-link').removeClass('active');
                    $('.tab-content').removeClass('active');
                    $(this).addClass('active');
                    $("#" + tab_id).addClass('active');
                    updateButtons();
                });

                // Initialize button visibility
                updateButtons();

                $('#joinClass').click(function() {
                    $('#msg').html('');
                    $('#join-class-popup #code-frm')[0].reset();
                    $('#join-class-popup').show();
                });

                // Close the popup
                $('#modal-close').click(function() {
                    $('#join-class-popup').hide(); 
                });

                // Close the message popup
                $('#message-modal-close').click(function() {
                    $('#message-popup').hide();
                });

                // Handles code submission
                $('#code-frm').submit(function(event) {
                    event.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: 'fetch_reviewer.php',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function(response) {
                            $('#join-class-popup').hide(); 

                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while accessing the shared reviewer. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });

                // Initialize Meatball Menu
                initializeMeatballMenu();

                function initializeMeatballMenu() {
                    $(document).on('click', '.meatball-menu-btn', function(event) {
                        event.stopPropagation();
                        $('.meatball-menu-container').not($(this).parent()).removeClass('show');
                        $(this).parent().toggleClass('show');
                    });

                    $(document).on('click', function(event) {
                        if (!$(event.target).closest('.meatball-menu-container').length) {
                            $('.meatball-menu-container').removeClass('show');
                        }
                    });
                }

                // Handle removal of shared reviewer
                $(document).on('click', '.remove_reviewer', function(event) {
                    event.preventDefault();
                    var sharedId = $(this).data('id'); 

                    // Confirmation before removal
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: 'POST',
                                url: 'remove_shared_reviewer.php',
                                data: { shared_id: sharedId },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.status === 'success') {
                                        // Remove the course card from display
                                        $(`.course-card[data-id="${sharedId}"]`).remove();
                                        Swal.fire({
                                            title: 'Deleted!',
                                            text: response.message,
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: response.message,
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'An error occurred while deleting the shared reviewer. Please try again.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>
    </div>
</body>
</html>
