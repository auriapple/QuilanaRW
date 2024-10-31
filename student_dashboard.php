<?php
include 'db_connect.php';
include 'auth.php';

// Check if user is logged in and redirect if not
if (!isset($_SESSION['login_user_type'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include('header.php') ?>
        <title>Dashboard | Quilana</title>
        <link rel="stylesheet" href="assets/css/faculty-dashboard.css">
        <link rel="stylesheet" href="assets/css/calendar.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
        <script src="assets/js/calendar.js" defer></script>
    </head>
    <body>
        <?php include 'nav_bar.php'; ?>
        <div class="content-wrapper dashboard-container">
            <!-- Summary -->
            <div class="dashboard-summary">
                <h1> Welcome, <?php echo $firstname ?> </h1>
                <h2> Summary </h2>
                <div class="cards">
                    <!-- Total Number of Classes -->
                    <div class="card" style="background-color: #FFE2E5;">
                        <img class="icons" src="image/DashboardCoursesIcon.png" alt="Classes Icon">
                        <?php
                        $result = $conn->query("SELECT COUNT(*) as totalClasses 
                                                FROM class c
                                                JOIN student_enrollment s ON c.class_id = s.class_id
                                                WHERE s.student_id = '".$_SESSION['login_id']."'
                                                AND s.status = '1'");
                        $resTotalClasses = $result->fetch_assoc();
                        $totalClasses = $resTotalClasses['totalClasses'];
                        ?>
                        <div class="card-data">
                            <h3> <?php echo $totalClasses ?> </h3>
                            <label>Total Classes</label> 
                        </div>
                    </div>
                    <!-- Total Number of Quizzes -->
                    <div class="card" style="background-color: #FADEFF"> 
                        <img class="icons" src="image/DashboardClassesIcon.png" alt="Quizzes Icon">
                        <?php
                        $result = $conn->query("SELECT COUNT(*) as totalQuizzes 
                                                FROM rw_reviewer rw
                                                WHERE rw.student_id = '".$_SESSION['login_id']."'
                                                AND reviewer_type = 1
                        ");
                        $resTotalQuizzes = $result->fetch_assoc();
                        $totalQuizzes = $resTotalQuizzes['totalQuizzes'];
                        ?>
                        <div class="card-data">
                            <h3> <?php echo $totalQuizzes ?> </h3>
                            <label>Total Quizzes</label> 
                        </div>
                    </div>
                    <!-- Total Number of Flashcards -->
                    <div class="card" style="background-color: #DCE1FC;"> 
                        <img class="icons" src="image/DashboardExamsIcon.png" alt="Exams Icon">
                        <?php
                        $result = $conn->query("SELECT COUNT(*) as totalFlashcards
                                                FROM rw_reviewer rw
                                                WHERE rw.student_id = '".$_SESSION['login_id']."'
                                                AND reviewer_type = 2
                        ");
                        $resTotalFlashcards = $result->fetch_assoc();
                        $totalFlashcards = $resTotalFlashcards['totalFlashcards'];
                        ?>
                        <div class="card-data">
                            <h3> <?php echo $totalFlashcards ?> </h3>
                            <label>Total Flashcards</label> 
                        </div>
                    </div>
                    <!-- Total Number of Shared Reviewers -->
                    <div class="card" style="background-color: #C5F1C5;"> 
                        <img class="icons" src="image/DashboardSharedIcon.png" alt="Shared Icon">
                        <?php
                        $result = $conn->query("SELECT COUNT(*) as totalShared 
                                            FROM user_reviewers ur 
                                            WHERE ur.student_id = '".$_SESSION['login_id']."'");
                        $resTotalShared = $result->fetch_assoc();
                        $totalShared = $resTotalShared['totalShared'];
                        ?>
                        <div class="card-data">
                            <h3> <?php echo $totalShared ?> </h3>
                            <label>Total Shared Reviewers</label> 
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recents -->
            <div class="recent-assessments">
                <h1> Recents </h1>
                <div class="recent-scrollable">
                    <?php
                    $result = $conn->query("
                        SELECT r.reviewer_name, r.topic, r.reviewer_type, ss.date_taken
                        FROM rw_student_results sr
                        JOIN rw_student_submission ss ON sr.rw_submission_id = ss.rw_submission_id
                        JOIN rw_reviewer r ON sr.reviewer_id = r.reviewer_id
                        WHERE r.reviewer_type = 1 
                        AND ss.student_id = '".$_SESSION['login_id']."'
                        ORDER BY ss.date_taken DESC
                    ");

                    $currentDate = '';

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $reviewerName = htmlspecialchars($row['reviewer_name']);
                            $reviewerTopic = htmlspecialchars($row['topic']);
                            $reviewerType = htmlspecialchars($row['reviewer_type']);
                            $dateTaken = date("Y-m-d", strtotime($row['date_taken']));

                            // Divider by date_taken
                            if ($dateTaken !== $currentDate) {
                                $currentDate = $dateTaken;
                                echo "<div class='assessment-separator'>";
                                echo "<span class='date'> " . $currentDate . "</span>";
                                echo "<hr class='separator-line'>";
                                echo "</div>";
                            }

                            $bgColor = '#FADEFF'; 
                            $icon = 'DashboardClassesIcon.png'; 


                            echo "<div id='recents' class='cards'>";
                                echo "<div id='recent-card' class='card' style='background-color: {$bgColor};'>";
                                    echo "<div id='recent-data' class='card-data'>";
                                        echo "<div class='recent-icon'>";
                                            echo "<img class='icons' src='image/{$icon}' alt='Quiz Icon'>";
                                        echo "</div>";
                                        echo "<div class='recent-details'>";
                                            echo "<h3>{$reviewerName}</h3>";
                                            echo "<label>{$reviewerTopic}</label>";
                                        echo "</div>";
                                    echo "</div>";
                                echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p class='no-assessments'>No recent quiz reviewers.</p>";
                    }
                    ?>
                </div>
            </div>

            <!-- Calendar -->
            <div class="dashboard-calendar">
                <div class="wrapper">
                    <header>
                        <div class="icons">
                            <span id="prev" class="material-symbols-rounded">chevron_left</span>
                        </div>
                        <p class="current-date"></p>
                        <div class="icons">
                            <span id="next" class="material-symbols-rounded">chevron_right</span>
                        </div>
                    </header>
                    <div class="calendar">
                        <ul class="weeks">
                        <li>Sun</li>
                        <li>Mon</li>
                        <li>Tue</li>
                        <li>Wed</li>
                        <li>Thu</li>
                        <li>Fri</li>
                        <li>Sat</li>
                        </ul>
                        <ul class="days"></ul>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>