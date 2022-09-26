<?PHP
require_once('./configure.php');
$ID_course = $_POST['ID_course'];
$course_code = $_POST['course_code'];
$course_desc = $_POST['course_desc'];
$option = $_POST["option"];

//ACTION CONSTANTS
$SEARCH_COURSE = 'Search course';
$ADD_COURSE = 'Add course';
$EDIT_COURSE = 'Edit course';
$DELETE_COURSE = 'Delete course';

switch ($option) {
    case $SEARCH_COURSE:
        $select_statement_valid = 1;
        echo "Searching for <b>Course ID:</b> $ID_course <b>Course Code:</b> $course_code <b>Course Description:</b> $course_desc<br />";
        if ($ID_course == null and $course_code == null and $course_desc == null) {
            echo "Must include course information to search<br />";
            echo "<form action='./courses.html' method='get'><input type='submit' value='Go Back to Manage Courses'/></form>";
            echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            $select_statement_valid = 0;
        } elseif ($ID_course) {
            $SELECT = "SELECT * FROM t_courses WHERE t_courses.ID_course='$ID_course'";
        } elseif ($course_code != null and $ID_course != null) {
            $SELECT = "SELECT * FROM t_courses WHERE t_courses.course_code LIKE '%$course_code%' AND t_courses.ID_course LIKE '%$ID_course%'";
        } elseif ($course_code != null and $ID_course == null) {
            $SELECT = "SELECT * FROM t_courses WHERE t_courses.course_code LIKE '%$course_code%'";
        } else {
            echo "An error constructing SELECT statement.";
            $select_statement_valid = 0;
        }
        if ($select_statement_valid == 1) {
            $resultSet = $conn->query($SELECT);
            if ($resultSet->num_rows > 0) {
                echo "Search Results Found Records Listed. <br>Click course to pre-fill information form.<br />";
                while ($rows = $resultSet->fetch_assoc()) {
                    $ID_course = $rows['ID_course'];
                    $course_code = $rows['course_code'];
                    $course_desc = $rows['course_desc'];
                    $post_string = $ID_course;
                    $post_string = $post_string . "&" . "course_code=" . $course_code . "&" . "course_desc=" . $course_desc;

                    echo "<br/br/><form action='./courses.html' method='GET'><button type='submit' name='ID_course' id='ID_course' value='$post_string'>Course ID: $ID_course, Course Code: $course_code</button></form>";
                }
                echo "<br/><br/><form action='./courses.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
            } else {
                echo "Error in searching for courses record(s).";
                echo "<form action='./courses.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
            }
        }

        mysqli_close($conn);

        break;
    case $ADD_COURSE:
        if ($course_code != "" && $course_desc != "") {
            $INSERT = "INSERT INTO t_courses (course_code, course_desc) VALUES ('$course_code', '$course_desc')";
            $stmt = $conn->prepare($INSERT);
            $stmt->execute();
            $rnum = $stmt->affected_rows;
            printf("Number of rows effected: %d and %d.\n", $stmt->affected_rows, $rnum);
            if ($rnum == 1) {
                echo "New record inserted successfully";
                echo "<form action='./courses.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            } else {
                echo "Failure to Insert record.";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            }
            mysqli_close($conn);
        } else {
            echo "All fields (except Course ID) are required";
            echo "<form action='./courses.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
            die();
        }
        echo 'Were gonna go ahead and add a course';
        break;
    case $EDIT_COURSE:
        if ($ID_course != "") {
            $UPDATE = "UPDATE t_courses SET course_code='$course_code', course_desc='$course_desc' WHERE ID_course='$ID_course'";
            $stmt = $conn->prepare($UPDATE);
            $stmt->execute();
            $rnum = $stmt->affected_rows;
            printf("Number of rows effected: %d and %d.\n", $stmt->affected_rows, $rnum);
            if ($rnum == 1) {
                echo "Update courses record executed, changes were successfully made to database.";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            } else {
                echo "Update courses record executed, changes were not made to database.";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            }
            mysqli_close($conn);
        } else {
            echo "Error in updating course must include Course ID to edit record.";
            echo "<form action='./students.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
            die();
        }
        break;
    case $DELETE_COURSE:
        if ($ID_course != "") {
            $DELETE = "DELETE FROM t_courses WHERE ID_course='$ID_course'";
            $stmt = $conn->prepare($DELETE);
            $stmt->execute();
            $rnum = $stmt->affected_rows;
            printf("Number of rows effected: %d and %d.\n", $stmt->affected_rows, $rnum);
            if ($rnum == 1) {
                echo "Deleted student successfully.";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            } else {
                echo "Failure to Delete record.";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            }
            mysqli_close($conn);
        } else {
            echo "Error in deleting student must include student ID to delete record.";
            echo "<form action='./courses.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
            die();
        }
        break;
    default:
        return null;
}
