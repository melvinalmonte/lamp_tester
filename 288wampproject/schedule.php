<?php
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

switch ($option) {
    case $SEARCH_SCHEDULE:
        $select_statement_valid = 1;
        echo "Searching for <b>Schedule ID:</b> $ID_schedule";
        if ((!$ID_schedule and !$ID_student and !$sched_yr) or ($ID_schedule == 'No value provided.' and !$ID_student and !$sched_yr)) {
            echo "<p>Must include course information to search</p>";
            echo "<form action='./schedule.html' method='get'><input type='submit' value='Go Back to Manage Schedule'/></form>";
            echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            $select_statement_valid = 0;
        } elseif ($ID_schedule and $ID_schedule != 'No value provided.' and !$ID_student) {
            $SELECT = "SELECT * FROM t_schedules WHERE t_schedules.ID_schedule='$ID_schedule'";
        } elseif (($ID_student and !$sched_yr) and (!$ID_schedule or $ID_schedule == 'No value provided.')) {
            $SELECT = "SELECT `t_schedules`.*, `t_students`.`fname`, `t_students`.`lname`, `t_courses`.`course_desc` FROM `t_schedules` LEFT JOIN `t_students` ON `t_schedules`.`ID_student` = `t_students`.`ID_student` LEFT JOIN `t_courses` ON `t_schedules`.`ID_course` = `t_courses`.`ID_course` WHERE t_schedules.ID_student='$ID_student';";
        } elseif (($ID_student and $sched_yr) and (!$ID_schedule or $ID_schedule == 'No value provided.')) {
            $SELECT = "SELECT `t_schedules`.*, `t_students`.`fname`, `t_students`.`lname`, `t_courses`.`course_desc` FROM `t_schedules` LEFT JOIN `t_students` ON `t_schedules`.`ID_student` = `t_students`.`ID_student` LEFT JOIN `t_courses` ON `t_schedules`.`ID_course` = `t_courses`.`ID_course` WHERE t_schedules.ID_student='$ID_student' AND t_schedules.sched_yr='$sched_yr';";
        } elseif ($sched_yr and (!$ID_schedule and !$ID_student) or ($ID_schedule == 'No value provided.' and !$ID_student)) {
            $SELECT = "SELECT `t_courses`.`course_code`, `t_courses`.`course_desc`, `t_schedules`.`sched_yr` FROM `t_courses` LEFT JOIN `t_schedules` ON `t_schedules`.`ID_course` = `t_courses`.`ID_course` WHERE `t_schedules`.`sched_yr`='$sched_yr';";
        } else {
            echo "<p>An error constructing SELECT statement.</p>";
            $select_statement_valid = 0;
        }
        if ($select_statement_valid) {
            $resultSet = $conn->query($SELECT);
            if ($resultSet->num_rows) {
                echo "<p>Search Results Found Records Listed.</p>";
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

                    $post_string = $ID_schedule;
                    $post_string = $post_string . "&" . "ID_student=" . $ID_student . "&" . "ID_course=" . $ID_course;
                    $post_string = $post_string . "&" . "sched_yr=" . $sched_yr;
                    $post_string = $post_string . "&" . "sched_sem=" . $sched_sem;
                    $post_string = $post_string . "&" . "grade_letter=" . $grade_letter;
                    if ($ID_schedule) {
                        echo "<br/br/><form action='./schedule.html' method='GET'><button type='submit' name='ID_schedule' id='ID_schedule' value='$post_string'>Schedule ID: $ID_schedule, Student ID: $ID_student, $first_name $last_name, Course ID: $ID_course $course_desc, Letter Grade: $grade_letter  </button></form>";
                    } elseif ($course_code) {
                        echo "<p>Course Code: $course_code -- Course Description: $course_desc -- Year: $sched_yr</p>";
                    } else {
                        echo '<p>Error in fetching data...</p>';
                    }
                }
                if ($ID_schedule) {
                    echo "<p>Click course to pre-fill information form.</p>";
                }
                echo "<br/><br/><form action='./schedule.html' method='get'><input type='submit' value='Go Back to Manage Schedule'/></form>";
            } else {
                echo "Error in searching for schedule record(s).";
                echo "<form action='./schedule.html' method='get'><input type='submit' value='Go Back to Manage Schedule'/></form>";
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
                echo "<form action='./schedule.html' method='get'><input type='submit' value='Go Back to Manage Schedule'/></form>";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            } else {
                echo "Failure to Insert record.";
                echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
            }
            mysqli_close($conn);
        } else {
            echo "<p>An error constructing INSERT statement.</p>";
            echo "<form action='./schedule.html' method='get'><input type='submit' value='Go Back to Manage Schedule'/></form>";
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
            echo "<form action='./schedule.html' method='get'><input type='submit' value='Go Back to Manage Schedule'/></form>";
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
            echo "<form action='./schedule.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
            die();
        }
        break;
    default:
        return null;
}
