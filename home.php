<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Fonts -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@100;500;700&display=swap" rel="stylesheet">

    <title>CHECKLIST</title>
    <link rel="icon" href="img/cvsu logo-200h.png" type="image/x-icon">
</head>

<style>
    <?php include "css/home.css" ?>
</style>

<body>
    <?php
    // Database connection
    include 'db_conn.php';

    // Retrieve student information from the database
    $studentID = 202211843; // Example student ID, you can replace it with a dynamic value as needed
    $studentQuery = "SELECT * FROM Student WHERE StudentID = $studentID";
    $studentResult = $con->query($studentQuery);
    $student = $studentResult->fetch_assoc();

    // Retrieve GWA per semester
    $gwaQuery = "
        SELECT 
            Y.`Year`, Y.Semester, AVG(CAST(B.Grade as FLOAT)) as AverageGrade
        FROM Student A
        INNER JOIN StudentCourse B ON A.StudentID = B.StudentID
        INNER JOIN YearLevel Y ON B.YearLevelID = Y.YearLevelID 
        WHERE A.StudentID = $studentID AND (Y.`Year` = 1 AND Y.Semester IN (1, 2))   -- First year semesters
       OR (Y.`Year` = 2 AND Y.Semester IN (1, 2))   -- Second year semesters
        GROUP BY Y.`Year`, Y.Semester
        ORDER BY Y.`Year`, Y.Semester";
    $gwaResult = $con->query($gwaQuery);
    ?>

    <!-- Navbar with search box -->
    <div class="fixed-top-container">
        <nav class="navbar p-3">
            <a class="navbar-brand">BSCS Checklist Of Courses</a>
        </nav>
    </div>

    <nav class="navbar second-navbar bg-light shadow-sm navbar-expand-sm navbar-light pl-5">
        <!-- Second navbar with menu -->
        <div class="container-fluid">
            <ul class="navbar-nav d-flex flex-row me-1">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Student Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="subjects.php">List of Courses</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Student Profile Card -->
    <div class="container mt-5">
        <div class="card">
            <div class="row no-gutters">
                <div class="col-md-4">
                    <img src="img/REYES, CRISHA NICOLE M.(OVAL).png" class="card-img" alt="Student Picture">
                </div>
                <div class="col-sm-8">
                    <div class="card-block">
                        <h6 class="m-b-20 p-b-5 b-b-default f-w-600">Student Information</h6>
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="m-b-5 f-w-600">Student Name</p>
                                <h6 class="text-muted f-w-400"><?php echo $student['FirstName'] . ' ' . $student['Middle'] . ' ' . $student['LastName']; ?></h6>
                            </div>
                            <div class="col-sm-6">
                                <p class="m-b-5 f-w-600">Year Level</p>
                                <h6 class="text-muted f-w-400">2nd Year</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="m-b-5 f-w-600">Student Number</p>
                                <h6 class="text-muted f-w-400"><?php echo $student['StudentID']; ?></h6>
                            </div>
                            <div class="col-sm-6">
                                <p class="m-b-5 f-w-600">Email</p>
                                <h6 class="text-muted f-w-400"><?php echo $student['Email']; ?></h6>
                            </div>
                        </div>
                    </div>

                    <div class="card-block">
                        <h6 class="m-b-20 p-b-5 b-b-default f-w-600">Student Grades</h6>
                        <div class="row">
                            <?php
                            $columnToggle = true; // Toggle to alternate columns
                            if ($gwaResult->num_rows > 0) {
                                while ($row = $gwaResult->fetch_assoc()) {
                                    if ($columnToggle) {
                                        echo "<div class='col-sm-6'>";
                                        echo "<p class='m-b-5 f-w-600'>{$row['Year']} - {$row['Semester']} GWA</p>";
                                        echo "<h6 class='text-muted f-w-400'>" . number_format($row['AverageGrade'], 2) . "</h6>";
                                        echo "</div>";
                                        $columnToggle = false;
                                    } else {
                                        echo "<div class='col-sm-6'>";
                                        echo "<p class='m-b-5 f-w-600'>{$row['Year']} - {$row['Semester']} GWA</p>";
                                        echo "<h6 class='text-muted f-w-400'>" . number_format($row['AverageGrade'], 2) . "</h6>";
                                        echo "</div>";
                                        $columnToggle = true;
                                    }
                                }
                            } else {
                                echo "<div class='col-sm-6'>";
                                echo "<p class='m-b-5 f-w-600'>No grades available.</p>";
                                echo "<h6 class='text-muted f-w-400'>N/A</h6>";
                                echo "</div>";
                                echo "<div class='col-sm-6'>";
                                echo "<p class='m-b-5 f-w-600'>&nbsp;</p>";
                                echo "<h6 class='text-muted f-w-400'>N/A</h6>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>
