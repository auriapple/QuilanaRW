<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php') ?>
    <?php include('auth.php') ?>
    <?php include('db_connect.php') ?>
    <title>Shared | Quilana</title>
    <link rel="stylesheet" href="meatballMenuTest/meatball.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include('nav_bar.php') ?>
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
                        <!-- Dynamically loaded shared reviewers will be displayed here -->
                </div>
            </div>

        </div>

        <!-- Modal for entering reviewer code -->
        <div id="join-class-popup" class="popup-overlay">
            <div id="join-modal-content" class="popup-content">
                <span id="modal-close" class="popup-close">&times;</span>
                <h2 id="join-class-title" class="popup-title">Enter Shared Code</h2>

                <!-- Form to submit the reviewer code -->
                <form id='code-frm' action="" method="POST">
                    <div class="modal-body">
                        <div class="class-code">
                            <input type="text" name="get_code" required="required" class="code" placeholder="Reviewer Code" />
                        </div>
                    </div>
                    <div class="join-button">
                        <button id="join" type="submit" class="secondary-button" name="join_by_code">Enter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal for Unenrolling -->
        <div class="modal fade" id="unenroll_modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Unenroll from Class</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to unenroll from <strong id="unenroll_class_name" style="font-weight: bold;"></strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" id="confirm_unenroll_btn" data-student-id="<?php echo $_SESSION['login_id'] ?>">Unenroll</button>
                    </div>
                </div>
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
                        $('#join_class').hide();
                    } else {
                        $('#join_class').show();
                    }
                }

                // Tab Functionality
                $('.tab-link').click(function() {
                    var tab_id = $(this).attr('data-tab');
                    $('.tab-link').removeClass('active');
                    $('.tab-content').removeClass('active');
                    $(this).addClass('active');
                    $("#" + tab_id).addClass('active');

                    // If the "Classes" tab is clicked, hide the assessment tab
                    if (tab_id === 'classes-tab') {
                        $('#class-name-tab').hide();
                        $('#assessments-tab').removeClass('active').empty(); // Optionally empty the content
                    }
                    updateButtons();
                });

                // Initialize button visibility
                updateButtons();

                $('#joinClass').click(function() {
                    $('#msg').html('');
                    $('#join-class-popup #code-frm').get(0).reset();
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
                        url: 'fetch_reviewer.php', // Change to your processing PHP script
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function(response) {
                        // Close the join class popup
                        $('#join-class-popup').hide(); 

                        if (response.status === 'success') {
                            $('.course-container').append(`
                                <div class="course-card">
                                    <div class="course-card-body">
                                        <div class="meatball-menu-container">
                                            <button class="meatball-menu-btn">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="meatball-menu">
                                                <a href="#" class="edit_reviewer" data-id="${response.reviewer_id}">Edit</a>
                                                <a href="#" class="remove_reviewer" data-id="${response.reviewer_id}">Remove</a>
                                               
                                            </div>
                                        </div>
                                        <div class="course-card-title">${response.reviewer_name}</div>
                                        <div class="course-card-text">Topic: <br>${response.topic}</div>
                                        <div class="course-actions">
                                            <button class="main-button" 
                                                id="take_reviewer" 
                                                data-id="${response.reviewer_id}" 
                                                data-type="${response.reviewer_type}" 
                                                type="button" 
                                                onclick="window.location.href='take_reviewer.php?reviewer_id=${response.reviewer_id}&reviewer_type=${response.reviewer_type}'">
                                                Take Reviewer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `);
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
    
                        error: function(jqXHR, textStatus, errorThrown) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while accessing the shared reviewer. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });

                initializeMeatballMenu();

                function initializeMeatballMenu() {
                    // Ensure the click event is bound to dynamically loaded elements
                    $(document).on('click', '.meatball-menu-btn', function(event) {
                        event.stopPropagation();
                        $('.meatball-menu-container').not($(this).parent()).removeClass('show');
                        $(this).parent().toggleClass('show');
                    });

                    // Close the menu if clicked outside
                    $(document).on('click', function(event) {
                        if (!$(event.target).closest('.meatball-menu-container').length) {
                            $('.meatball-menu-container').removeClass('show');
                        }
                    });
                }
            });
        </script>
    </div>
</body>
</html>
