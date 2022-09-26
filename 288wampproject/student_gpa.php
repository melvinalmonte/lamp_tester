<?php
echo '<style>';
include "./styles/styles.css";
echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@100&display=swap">';
echo '</style>';

require_once('./configure.php');
$ID_schedule = $_POST['ID_schedule'];
$ID_student = $_POST['ID_student'];
$ID_course = $_POST['ID_course'];
$sched_yr = $_POST["sched_yr"];
$sched_sem = $_POST['sched_sem'];
$grade_letter = strtoupper($_POST['grade_letter']);
$option = $_POST["option"];

//ACTION CONSTANTS
$SEARCH_SCHEDULE = 'search_schedule';
$ADD_SCHEDULE = 'add_schedule';
$EDIT_SCHEDULE = 'edit_schedule';
$DELETE_SCHEDULE = 'delete_schedule';

//SEARCH SELECTOR ACTIONS:
$EMPTY_FORM = 'EMPTY_FORM';
$SEARCH_BY_SCHEDULE_ID = 'SEARCH_BY_SCHEDULE_ID';
$SEARCH_BY_STUDENT_ID = 'SEARCH_BY_STUDENT_ID';
$SEARCH_BY_COURSE_ID = 'SEARCH_BY_COURSE_ID';
$SEARCH_COURSES_BY_YEAR = 'SEARCH_COURSES_BY_YEAR';
$SEARCH_BY_STUDENT_ID_AND_YEAR = 'SEARCH_BY_STUDENT_ID_AND_YEAR';
$SEARCH_ALL_STUDENTS = 'SEARCH_ALL_STUDENTS';

function searchSelector($ID_schedule, $ID_student, $ID_course, $sched_yr, $sched_sem){
    if((!$ID_schedule or $ID_schedule == 'No value provided.') and !$ID_student and !$ID_course and !$sched_yr and !$sched_sem){
        global $EMPTY_FORM;
        return $EMPTY_FORM;
    }
    if(($ID_schedule or $ID_schedule != 'No value provided.') and !$ID_student and !$ID_course and !$sched_yr and !$sched_sem){
        global $SEARCH_BY_SCHEDULE_ID;
        return $SEARCH_BY_SCHEDULE_ID;
    }
    if((!$ID_schedule or $ID_schedule == 'No value provided.') and $ID_student == "*" and !$ID_course and !$sched_yr and !$sched_sem){
        global $SEARCH_ALL_STUDENTS;
        return $SEARCH_ALL_STUDENTS;
    }
    if((!$ID_schedule or $ID_schedule == 'No value provided.') and $ID_student and !$ID_course and !$sched_yr and !$sched_sem){
        global $SEARCH_BY_STUDENT_ID;
        return $SEARCH_BY_STUDENT_ID;
    }
    if((!$ID_schedule or $ID_schedule == 'No value provided.') and !$ID_student and $ID_course and !$sched_yr and !$sched_sem){
        global $SEARCH_BY_COURSE_ID;
        return $SEARCH_BY_COURSE_ID;
    }
    if((!$ID_schedule or $ID_schedule == 'No value provided.') and !$ID_student and !$ID_course and $sched_yr and !$sched_sem){
        global $SEARCH_COURSES_BY_YEAR;
        return $SEARCH_COURSES_BY_YEAR;
    }
    if((!$ID_schedule or $ID_schedule == 'No value provided.') and $ID_student and !$ID_course and $sched_yr and !$sched_sem){
        global $SEARCH_BY_STUDENT_ID_AND_YEAR;
        return $SEARCH_BY_STUDENT_ID_AND_YEAR;
    }

}

switch ($option) {
    case $SEARCH_SCHEDULE:
        $select_statement_valid = 1;
        $custom_selector = searchSelector($ID_schedule, $ID_student, $ID_course, $sched_yr, $sched_sem);
        switch ($custom_selector){
            case $EMPTY_FORM:
                echo "<p>Must include course information to search</p>";
                echo "<form action='./student_gpa.html' method='get'><input type='submit' value='Go Back to Manage Student GPA'/></form>";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
                $select_statement_valid = 0;
                break;
            case $SEARCH_BY_SCHEDULE_ID:
                echo "<p>Searching for <b>Schedule ID:</b> $ID_schedule</p>";
                $SELECT = "SELECT * FROM t_schedules WHERE t_schedules.ID_schedule='$ID_schedule'";
                break;
            case $SEARCH_BY_STUDENT_ID:
                echo "<p>Searching for <b>Student ID:</b> $ID_student</p>";
                $SELECT = "SELECT `t_schedules`.*, `t_students`.`fname`, `t_students`.`lname`, `t_courses`.`course_desc` FROM `t_schedules` LEFT JOIN `t_students` ON `t_schedules`.`ID_student` = `t_students`.`ID_student` LEFT JOIN `t_courses` ON `t_schedules`.`ID_course` = `t_courses`.`ID_course` WHERE t_schedules.ID_student='$ID_student'";
                $GPASELECT = "SELECT (SUM(IF(t_schedules.grade_letter='A',4,IF(t_schedules.grade_letter='B',3,IF(t_schedules.grade_letter='C',2,IF(t_schedules.grade_letter='D',1,0))))) / (COUNT(t_schedules.grade_letter) + 0.00000000001)) AS gpa FROM t_students LEFT JOIN t_schedules ON t_students.ID_student = t_schedules.ID_student WHERE t_students.ID_student='$ID_student'";
                break;
            case $SEARCH_ALL_STUDENTS:
                $SELECT="SELECT t_students.ID_student, t_students.fname, t_students.lname, (SUM(IF(t_schedules.grade_letter='A',4,IF(t_schedules.grade_letter='B',3,IF(t_schedules.grade_letter='C',2,IF(t_schedules.grade_letter='D',1,0))))) / (COUNT(t_schedules.grade_letter) + 0.00000000001)) AS GPA FROM t_students LEFT JOIN t_schedules ON t_students.ID_student = t_schedules.ID_student LEFT JOIN `t_courses` ON `t_schedules`.`ID_course` = `t_courses`.`ID_course` GROUP BY t_students.lname ASC;";
                break;
            case $SEARCH_BY_COURSE_ID:
                echo "<p>Searching for <b>Course ID:</b> $ID_course</p>";
                $SELECT = "SELECT * FROM `t_courses` WHERE `t_courses`.`ID_course`=$ID_course";
                break;
            case $SEARCH_COURSES_BY_YEAR:
                echo "<p>Searching courses for year $sched_yr</p>";
                $SELECT = "SELECT `t_courses`.`course_code`, `t_courses`.`course_desc`, `t_schedules`.`sched_yr` FROM `t_courses` LEFT JOIN `t_schedules` ON `t_schedules`.`ID_course` = `t_courses`.`ID_course` WHERE `t_schedules`.`sched_yr`='$sched_yr'";
                break;
            case $SEARCH_BY_STUDENT_ID_AND_YEAR:
                echo "<p>Searching for <b>Student ID:</b> $ID_student and <b>year: </b>$sched_yr</p>";
                $SELECT = "SELECT `t_schedules`.*, `t_students`.`fname`, `t_students`.`lname`, `t_courses`.`course_desc` FROM `t_schedules` LEFT JOIN `t_students` ON `t_schedules`.`ID_student` = `t_students`.`ID_student` LEFT JOIN `t_courses` ON `t_schedules`.`ID_course` = `t_courses`.`ID_course` WHERE t_schedules.ID_student='$ID_student' AND t_schedules.sched_yr='$sched_yr'";
                break;
            default:
                echo "<p>An error constructing SELECT statement.</p>";
                $select_statement_valid = 0;
        }
        if ($select_statement_valid) {
            $resultSet = $conn->query($SELECT);
            if($GPASELECT){
                $GPAResultSet = $conn->query($GPASELECT);
                $student_gpa = current($GPAResultSet->fetch_assoc());
            }
            if ($resultSet->num_rows) {
                echo "<p>Search Results Found Records Listed.</p>";
                echo "<div class='table-container'>";
                echo "<table id='student-gpa'>";
                if($custom_selector == $SEARCH_ALL_STUDENTS){
                    echo "
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Student Last Name</th>
                                    <th>GPA</th>
                                </tr>
                            </thead>
                ";
                }
                if($custom_selector == $SEARCH_BY_STUDENT_ID or $custom_selector == $SEARCH_BY_STUDENT_ID_AND_YEAR){
                    echo "
                            <thead>
                                <tr>
                                    <th>Schedule ID</th>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Student Last Name</th>
                                    <th>Course ID</th>
                                    <th>Course Description</th>
                                    <th>Course Grade</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                ";
                }
                if($custom_selector == $SEARCH_BY_SCHEDULE_ID){
                    echo "
                            <thead>
                                <tr>
                                    <th>Schedule ID</th>
                                    <th>Student ID</th>
                                    <th>Course ID</th>
                                    <th>Schedule Year</th>
                                    <th>Schedule Semester</th>
                                    <th>Grade Letter</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                ";
                }
                if($custom_selector == $SEARCH_BY_COURSE_ID){
                    echo "
                            <thead>
                                <tr>
                                    <th>Course ID</th>
                                    <th>Course Code</th>
                                    <th>Course Description</th>
                                </tr>
                            </thead>
                ";
                }
                if($custom_selector == $SEARCH_COURSES_BY_YEAR){
                    echo "
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Description</th>
                                    <th>Course Year</th>
                                </tr>
                            </thead>
                ";
                }

                echo "<tbody>";
                while ($rows = $resultSet->fetch_assoc()) {
                    $ID_schedule = $rows['ID_schedule'];
                    $ID_student = $rows['ID_student'];
                    $ID_course = $rows['ID_course'];
                    $sched_yr = $rows["sched_yr"];
                    $sched_sem = $rows['sched_sem'];
                    $grade_letter = $rows['grade_letter'];
                    $first_name = $rows['fname'];
                    $last_name = $rows['lname'];
                    $course_desc = $rows['course_desc'];
                    $course_code = $rows['course_code'];
                    $course_gpa = $rows['GPA'];

                    $post_string = $ID_schedule;
                    $post_string = $post_string . "&" . "ID_student=" . $ID_student . "&" . "ID_course=" . $ID_course;
                    $post_string = $post_string . "&" . "sched_yr=" . $sched_yr;
                    $post_string = $post_string . "&" . "sched_sem=" . $sched_sem;
                    $post_string = $post_string . "&" . "grade_letter=" . $grade_letter;
                    if($custom_selector == $SEARCH_ALL_STUDENTS){
                        echo "
                            <tr>
                                <td>$ID_student</td>
                                <td>$first_name</td>
                                <td>$last_name</td>
                                <td>$course_gpa</td>
                            </tr>                       
                        ";
                    }
                    if($custom_selector == $SEARCH_BY_STUDENT_ID or $custom_selector == $SEARCH_BY_STUDENT_ID_AND_YEAR){
                        echo "
                            <tr>
                                <td>$ID_schedule</td>
                                <td>$ID_student</td>
                                <td>$first_name</td>
                                <td>$last_name</td>
                                <td>$ID_course</td>
                                <td>$course_desc</td>
                                <td>$grade_letter</td>
                                <td>
                                    <form class='table-form' action='./student_gpa.html' method='GET'>
                                        <button class='btn-secondary table-button' type='submit' name='ID_schedule' id='ID_schedule' value='$post_string'>
                                            Pre-fill Form
                                        </button>
                                    </form>
                                </td>
                            </tr>                       
                        ";
                    }
                    if($custom_selector == $SEARCH_BY_SCHEDULE_ID){
                        echo "
                            <tr>
                                <td>$ID_schedule</td>
                                <td>$ID_student</td>
                                <td>$ID_course</td>
                                <td>$sched_yr</td>
                                <td>$sched_sem</td>
                                <td>$grade_letter</td>
                                <td>
                                    <form class='table-form' action='./student_gpa.html' method='GET'>
                                        <button class='btn-secondary table-button' type='submit' name='ID_schedule' id='ID_schedule' value='$post_string'>
                                            Pre-fill Form
                                        </button>
                                    </form>
                                </td>
                            </tr>                       
                        ";
                    }
                    if($custom_selector == $SEARCH_BY_COURSE_ID){
                        echo "
                            <tr>
                                <td>$ID_course</td>
                                <td>$course_code</td>
                                <td>$course_desc</td>
                            </tr>                       
                        ";
                    }
                    if($custom_selector == $SEARCH_COURSES_BY_YEAR){
                        echo "
                            <tr>
                                <td>$course_code</td>
                                <td>$course_desc</td>
                                <td>$sched_yr</td>
                            </tr>                       
                        ";
                    }
                }
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
                if ($ID_schedule) {
                    echo "<p>Click course to pre-fill information form.</p>";
                }
                if($student_gpa){
                    $gpa = number_format(floatval($student_gpa), 2);
                    echo "<p><b>Student's GPA: </b>$gpa</p>";
                }
                echo "<br/><br/><form action='./student_gpa.html' method='get'><input type='submit' value='Go Back to Manage Schedule'/></form>";

            } else {
                echo "Error in searching for schedule record(s).";
                echo "<form action='./student_gpa.html' method='get'><input type='submit' value='Go Back to Manage Student GPA'/></form>";
            }
        }
        break;
    case $ADD_SCHEDULE:
        if ($ID_course and $sched_yr and $sched_sem and $grade_letter) {
            $INSERT = "INSERT INTO t_schedules (ID_student, ID_course, sched_yr, sched_sem, grade_letter) VALUES ('$ID_student', '$ID_course', '$sched_yr', '$sched_sem', '$grade_letter')";
            $stmt = $conn->prepare($INSERT);
            $stmt->execute();
            $rnum = $stmt->affected_rows;
            printf("Number of rows effected: %d and %d.\n", $stmt->affected_rows, $rnum);
            if ($rnum) {
                echo "New record inserted successfully";
                echo "<form action='./student_gpa.html' method='get'><input type='submit' value='Go Back to Manage Student GPA'/></form>";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            } else {
                echo "Failure to Insert record.";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            }
            mysqli_close($conn);
        } else {
            echo "<p>An error constructing INSERT statement.</p>";
            echo "<form action='./student_gpa.html' method='get'><input type='submit' value='Go Back to Manage Student GPA'/></form>";
            die();
        }
        break;
    case $EDIT_SCHEDULE:
        if ($ID_schedule) {
            $UPDATE = "UPDATE t_schedules SET ID_student='$ID_student', ID_course='$ID_course', sched_yr='$sched_yr', sched_sem='$sched_sem', grade_letter='$grade_letter' WHERE ID_schedule='$ID_schedule'";
            $stmt = $conn->prepare($UPDATE);
            $stmt->execute();
            $rnum = $stmt->affected_rows;
            printf("Number of rows effected: %d and %d.\n", $stmt->affected_rows, $rnum);
            if ($rnum) {
                echo "Update courses record executed, changes were successfully made to database.";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            } else {
                echo "Update courses record executed, changes were not made to database.";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            }
            mysqli_close($conn);
        } else {
            echo "Error in updating schedule must include Course ID to edit record.";
            echo "<form action='./student_gpa.html' method='get'><input type='submit' value='Go Back to Manage Student GPA'/></form>";
            die();
        }
        break;
    case $DELETE_SCHEDULE:
        if ($ID_schedule) {
            $DELETE = "DELETE FROM t_schedules WHERE ID_schedule='$ID_schedule'";
            $stmt = $conn->prepare($DELETE);
            $stmt->execute();
            $rnum = $stmt->affected_rows;
            printf("Number of rows effected: %d and %d.\n", $stmt->affected_rows, $rnum);
            if ($rnum) {
                echo "Deleted schedule successfully.";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            } else {
                echo "Failure to Delete record.";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            }
            mysqli_close($conn);
        } else {
            echo "Error in deleting schedule must include schedule ID to delete record.";
            echo "<form action='./student_gpa.html' method='get'><input type='submit' value='Go Back to Manage Student GPA'/></form>";
            die();
        }
        break;
    default:
        echo "<p>Error: No action selected.</p>";
        echo "<form action='./student_gpa.html' method='get'><input type='submit' value='Go Back to Manage Student GPA'/></form>";
        return null;
}
