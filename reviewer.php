<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php') ?>
    <?php include('auth.php') ?>
    <?php include('db_connect.php') ?>
    <title>Reviewer | Quilana</title>
    <link rel="stylesheet" href="meatballMenuTest/meatball.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include('nav_bar.php') ?>

    <div class="content-wrapper">
        <div class="scrollable-content">
            <!-- Header Container -->
            <div class="add-course-container">
                <button class="secondary-button" id="add_reviewer_button">Add Reviewer</button>
                <form class="search-bar" action="#" method="GET">
                    <input type="text" name="query" placeholder="Search" required>
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>

            <div class="tabs-container">
                <ul class="tabs">
                    <li class="tab-link active" data-tab="courses-tab">Reviewer</li>
                    <li class="tab-link" id="classes-tab-link" style="display: none;" data-tab="classes-tab">Reviewer Details</li>
                </ul>
            </div>

            <div id="courses-tab" class="tab-content active">
                <div class="course-container">
                    <?php
                    $qry = $conn->query("SELECT * FROM rw_reviewer WHERE student_id = '".$_SESSION['login_id']."' ORDER BY topic ASC");
                    if ($qry->num_rows > 0) {
                        while ($row = $qry->fetch_assoc()) {
                            $reviewer_id =  $row['reviewer_id'];
                    ?>
                    <div class="course-card">
                        <div class="course-card-body">
                            <div class="meatball-menu-container">
                                <button class="meatball-menu-btn">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="meatball-menu">
                                    <a href="#" class="edit_reviewer" data-id="<?php echo $reviewer_id ?>">Edit</a>
                                    <a href="#" class="remove_reviewer" data-id="<?php echo $reviewer_id ?>">Remove</a>
                                    <a href="#" class="share_reviewer" data-id="<?php echo $reviewer_id ?>">Get Code</a>
                                </div>
                            </div>
                            <div class="course-card-title"><?php echo $row['reviewer_name'] ?></div>
                            <div class="course-card-text">Topic: <br><?php echo $row['topic'] ?></div>
                            <div class="course-actions">
                                <a class="tertiary-button" id="view_reviewer_details" 
                                href="manage_reviewer.php?reviewer_id=<?php echo $reviewer_id ?>" type="button"> Manage</a>                                
                                <button class="main-button" 
                                    id="take_reviewer" 
                                    data-id="<?php echo $row['reviewer_id']; ?>" 
                                    data-type="<?php echo $row['reviewer_type']; ?>" 
                                    type="button" 
                                    onclick="window.location.href='take_reviewer.php?reviewer_id=<?php echo $row['reviewer_id']; ?>&reviewer_type=<?php echo $row['reviewer_type']; ?>'">
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

            <!-- ADD/EDIT Modal -->
            <div class="modal fade" id="addReviewerModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="addReviewerModalLabel">Add Reviewer</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="addReviewerForm" action="" method="POST">
                            <div class="modal-body">
                                <div id="msg"></div>
                                <input type="hidden" id="reviewer_id" name="reviewer_id">
                                <div class="form-group">
                                    <label for="reviewer_type">Select Reviewer Type</label>
                                    <select id="reviewer_type" name="reviewer_type" class="form-control" required>
                                        <option value="1">Test</option>
                                        <option value="2">Flashcard</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="reviewer_name">Reviewer Name</label>
                                    <input type="text" id="reviewer_name" name="reviewer_name" class="form-control" placeholder="Enter Reviewer Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="topic">Topic</label>
                                    <input type="text" id="topic" name="topic" class="form-control" placeholder="Enter Topic" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Reviewer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

           <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="delete_assessment_modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Delete Reviewer</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this reviewer?</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button class="btn btn-danger" id="confirm_delete_btn">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Get Code Modal -->
            <div class="modal fade" id="manage_get_code" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">Reviewer Code</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div id="msg"></div>
                            <div class="form-group">
                                <h1 id="modal_code"></h1>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-dismiss="modal">Return</button>
                        </div>
                    </div>
                </div>
        </div>
    </div>

        <script>
        // Function to open modal for adding or editing
        function openReviewerModal(mode, reviewerId = null) {
            if (mode === 'add') {
                $('#addReviewerModalLabel').text('Add Reviewer');
                $('#addReviewerForm').attr('action', 'save_reviewer.php');
                $('#addReviewerForm')[0].reset(); // Clear form
                $('#reviewer_id').val(''); // Clear the reviewer_id
            } else if (mode === 'edit') {
                $('#addReviewerModalLabel').text('Edit Reviewer');
                $('#addReviewerForm').attr('action', 'update_reviewer.php');
                
                // Fetch reviewer details
                $.ajax({
                    url: 'get_reviewer.php',
                    type: 'POST',
                    data: { reviewer_id: reviewerId },
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            $('#reviewer_type').val(result.reviewer.reviewer_type);
                            $('#reviewer_name').val(result.reviewer.reviewer_name);
                            $('#topic').val(result.reviewer.topic);
                            $('#reviewer_id').val(reviewerId);
                        } else {
                            alert('Error fetching reviewer details.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Error fetching reviewer details.');
                    }
                });
            }
            $('#addReviewerModal').modal('show');
        }

        // Event listener for "Add Reviewer" button
        $('#add_reviewer_button').click(function() {
            openReviewerModal('add');
        });

        // Event listener for "Edit" button in meatball menu
        $(document).on('click', '.edit_reviewer', function() {
            var reviewerId = $(this).data('id');
            openReviewerModal('edit', reviewerId);
        });

        // Form submission handler
        $('#addReviewerForm').submit(function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            var url = $(this).attr('action');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message || 'Reviewer saved successfully');
                        $('#addReviewerModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Unable to save reviewer'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('XHR:', xhr);
                    console.error('Status:', status);
                    console.error('Error:', error);
                    alert('Error: ' + status + ' - ' + error + '\n' + xhr.responseText);
                }
            });
        });

        $(document).ready(function () {
            // Toggle the meatball menu visibility when the button is clicked
            $(document).on('click', '.meatball-menu-btn', function (e) {
                e.stopPropagation(); // Prevent click from bubbling up
                var $menu = $(this).siblings('.meatball-menu');
                $('.meatball-menu').not($menu).hide(); // Hide other open meatball menus
                $menu.toggle(); // Toggle the current menu visibility
            });

            // Close the meatball menu if clicking outside of it
            $(document).click(function () {
                $('.meatball-menu').hide(); // Hide all open menus when clicking outside
            });

            // Prevent the menu from closing when clicking inside the menu
            $(document).on('click', '.meatball-menu', function (e) {
                e.stopPropagation();
            });
        });

        // Delete button functionality for reviewers
        $(document).on('click', '.remove_reviewer', function() {
            var reviewerId = $(this).data('id'); // Get the reviewer ID from the clicked element
            $('#confirm_delete_btn').data('id', reviewerId); // Set reviewer ID on the confirm button
            $('#delete_assessment_modal').modal('show'); // Show the confirmation modal
        });

        $('#confirm_delete_btn').click(function() {
            var reviewerId = $(this).data('id');

            $.ajax({
                url: 'delete_reviewer.php', 
                method: 'POST', 
                data: { reviewer_id: reviewerId }, 
                dataType: 'json', 
                success: function(response) {
                    if (response.success) { 
                        alert('Reviewer successfully deleted');
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Error: ' + (response.error || 'Unable to delete the reviewer.'));
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error: Unable to process the request. ' + textStatus + ': ' + errorThrown);
                }
            });
        });

        $(document).on('click', '.share_reviewer', function() { 
            var reviewerId = $(this).data('id'); 

            $('#msg').html(''); // Clear any previous messages
            $('#manage_get_code .modal-title').html('Reviewer Code'); // Set modal title to 'Reviewer Code'

            // Fetch the code dynamically using AJAX
            $.ajax({
                url: 'reviewer_code.php', 
                type: 'POST', 
                data: { reviewer_id: reviewerId }, 
                success: function(response) {
                    var result = JSON.parse(response); 
                    if (result.success) {
                        $('#modal_code').text(result.code); // Display the generated code
                    } else {
                        $('#modal_code').text('Error: ' + result.error); 
                    }
                    $('#manage_get_code').modal('show'); // Show the modal
                },
                error: function(xhr, status, error) {
                    $('#modal_code').text('Error fetching code. Please try again.'); 
                    console.error('Error:', error); 
                }
            });
        });

</script>
</body>
</html>
