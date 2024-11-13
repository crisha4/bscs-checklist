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

<!-- CSS file -->
<style>
    <?php include "css/home.css" ?>
</style>

<body>
    <?php
    // database connection
    include 'db_conn.php';

    $limit = isset($_POST["limit-records"]) ? $_POST["limit-records"] : (isset($_GET['limit']) ? $_GET['limit'] : 10);
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    $search_query = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_query'])) {
        $search_query = $_POST['search_query'];
    } elseif (isset($_GET['search_query'])) {
        $search_query = $_GET['search_query'];
    }

    if (!empty($search_query)) {
        // records based on the search query
        $sql = "SELECT sc.StudentCourseID, s.StudentID, s.LastName, s.FirstName, s.Middle,  s.Email, 
                cl.CourseCode, cl.CourseTitle, cl.Instructor, yl.Year, yl.Semester, sc.Grade, sc.Remarks
                FROM StudentCourse sc
                INNER JOIN Student s ON sc.StudentID = s.StudentID
                INNER JOIN CourseList cl ON sc.CourseID = cl.CourseID
                INNER JOIN YearLevel yl ON sc.YearLevelID = yl.YearLevelID
                WHERE s.StudentID LIKE '%$search_query%'
                OR cl.CourseCode LIKE '%$search_query%'
                OR cl.CourseTitle LIKE '%$search_query%'
                OR cl.Instructor LIKE '%$search_query%'
                OR yl.Year LIKE '%$search_query%'
                OR yl.Semester LIKE '%$search_query%'
                OR sc.Grade LIKE '%$search_query%'
                OR sc.Remarks LIKE '%$search_query%'
                LIMIT $start, $limit";
        $result = $con->query($sql);

        $count_query = "SELECT COUNT(*) AS total FROM StudentCourse sc
                        INNER JOIN Student s ON sc.StudentID = s.StudentID
                        INNER JOIN CourseList cl ON sc.CourseID = cl.CourseID
                        INNER JOIN YearLevel yl ON sc.YearLevelID = yl.YearLevelID
                        WHERE s.StudentID LIKE '%$search_query%'
                        OR cl.CourseCode LIKE '%$search_query%'
                        OR cl.CourseTitle LIKE '%$search_query%'
                        OR cl.Instructor LIKE '%$search_query%'
                        OR yl.Year LIKE '%$search_query%'
                        OR yl.Semester LIKE '%$search_query%'
                        OR sc.Grade LIKE '%$search_query%'
                        OR sc.Remarks LIKE '%$search_query%'";
        $total = $con->query($count_query)->fetch_assoc()['total'];
    } else {
        // retrieve records if search form is not submitted
        $sql = "SELECT sc.StudentCourseID, s.LastName, s.FirstName, s.Middle, s.Email, s.StudentID, 
                cl.CourseCode, cl.CourseTitle, cl.Instructor, yl.Year, yl.Semester, sc.Grade, sc.Remarks
                FROM StudentCourse sc
                INNER JOIN Student s ON sc.StudentID = s.StudentID
                INNER JOIN CourseList cl ON sc.CourseID = cl.CourseID
                INNER JOIN YearLevel yl ON sc.YearLevelID = yl.YearLevelID
                LIMIT $start, $limit";
        $result = $con->query($sql);

        $count_query = "SELECT COUNT(*) AS total FROM StudentCourse";
        $total = $con->query($count_query)->fetch_assoc()['total'];
    }

    $pages = ceil($total / $limit);
    $Previous = $page - 1;
    $Next = $page + 1;
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

    <!-- Records of Subjects -->
    <div class="container mt-4" >
        <div class="row">
            <div class="col-xl-12" style="padding:0px;">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background: #acef88">
                        <div class="col-md-2">
                            <form class="form-inline ml-auto" method="post" action="">
                                <div class="form-group">
                                    <label for="limit-records" style="color:white; margin-right: 10px;">Show </label>
                                    <select name="limit-records" id="limit-records" class="form-control" onchange="this.form.submit()">
                                        <option value="10" <?= $limit == 10 ? 'selected' : ''; ?>>10</option>
                                        <?php foreach ([20, 30, 40, 50, 60] as $limitOption) : ?>
                                            <option value="<?= $limitOption; ?>" <?= $limit == $limitOption ? 'selected' : ''; ?>><?= $limitOption; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="limit-records" style="color:white; margin-left: 10px;">entries</label>
                                </div>
                            </form>
                        </div>
                        <form class="form-inline ml-auto" method="post" action="">
                            <input type="hidden" name="limit-records" value="<?= $limit ?>">
                            <input class="form-control mr-sm-2 px-3" type="search" placeholder="Search here.." aria-label="Search" name="search_query" value="<?= htmlspecialchars($search_query); ?>">
                            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <?php
                        if ($result->num_rows > 0) {
                            ?>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="width: 12%;">Course Code</th>
                                        <th style="width: 30%;">Course Title</th>
                                        <th style="width: 10%;">Year</th>
                                        <th>Semester</th>
                                        <th>Grade</th>
                                        <th>Instructor</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td style="font-weight: bold;"><?= $row["StudentCourseID"]; ?></td>
                                            <td><?= $row["CourseCode"]; ?></td>
                                            <td><?= $row["CourseTitle"]; ?></td>
                                            <td><?= $row["Year"]; ?></td>
                                            <td><?= $row["Semester"]; ?></td>
                                            <td><?= $row["Grade"]; ?></td>
                                            <td><?= $row["Instructor"]; ?></td>
                                            <td><?= $row["Remarks"]; ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <?php
                        } else {
                            echo "<p>No records found</p>";
                        }
                        ?>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-10 ">
                                <div class="d-flex justify-content-end" aria-label="Page navigation" style="padding-right: 150px;">
                                    <ul class="pagination">
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $Previous ?>&limit=<?= $limit ?>&search_query=<?= htmlspecialchars($search_query) ?>" aria-label="Previous">
                                                <span aria-hidden="true">« Previous</span>
                                            </a>
                                        </li>
                                        <?php for ($i = 1; $i <= $pages; $i++) : ?>
                                            <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?= $i; ?>&limit=<?= $limit ?>&search_query=<?= htmlspecialchars($search_query) ?>"><?= $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $Next ?>&limit=<?= $limit ?>&search_query=<?= htmlspecialchars($search_query) ?>" aria-label="Next">
                                                <span aria-hidden="true">Next »</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script src="bootstrap/js/bootstrap.bundle.min.js"></script>

        <script>
            $(document).ready(function() {
                $("#limit-records").change(function() {
                    $(this).closest('form').submit();
                });
            });
        </script>

</body>

</html>
