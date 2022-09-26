<?PHP
require_once('./configure.php');

$ID_student = $_POST['ID_student'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$status = $_POST['status'];
$start_dte = $_POST['start_dte'];;
$end_dte = $_POST['end_dte'];

if ($phone == "") {
	$phone = "(000) 000-0000";
}
if ($email == "") {
	$email = "none given";
}
if ($status != "1") {
	$status = "0";
}
if ($start_dte == "") {
	$start_dte = date("Y/m/d");
}
if ($end_dte == "") {
	$end_dte = date("Y/m/d");
}

$option = $_POST["option"];
if ($option == "Search Student") {
	$select_statement_valid = 1;
	/*search for the student*/
	echo "Searching for <b>Student ID:</b> $ID_student <b>Last Name:</b> $lname <b>First Name:</b> $fname<br />";
	if ($ID_student == NULL and $lname == NULL and $fname == NULL) {
		echo "Must include student information to search<br />";
		echo "<form action='./students.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
		echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
		$select_statement_valid = 0;
	} elseif ($ID_student != NULL) {
		$SELECT = "SELECT * FROM t_students WHERE t_students.ID_student='$ID_student'";
	} elseif ($lname != NULL and $fname != NULL) {
		$SELECT = "SELECT * FROM t_students WHERE t_students.lname LIKE '%$lname%' AND t_students.fname LIKE '%$fname%'";
	} elseif ($lname != NULL and $fname == NULL) {
		$SELECT = "SELECT * FROM t_students WHERE t_students.lname LIKE '%$lname%'";
	} elseif ($lname == NULL and $fname != NULL) {
		$SELECT = "SELECT * FROM t_students WHERE t_students.fname LIKE '%$fname%'";
	} else {
		echo "An error constructing SELECT statement.";
		$select_statement_valid = 0;
	}
	if ($select_statement_valid == 1) {
		$resultSet = $conn->query($SELECT);
		if ($resultSet->num_rows > 0) {
			echo "Search Results Found Records Listed. <br>Click student to pre-fill information form.<br />";
			while ($rows = $resultSet->fetch_assoc()) {
				$ID_student = $rows['ID_student'];
				$fname = $rows['fname'];
				$lname = $rows['lname'];
				$phone = $rows['phone'];
				$email = $rows['email'];
				$status = $rows['status'];
				$start_dte = $rows['start_dte'];
				$end_dte = $rows['end_dte'];

				$post_string = $ID_student;
				$post_string = $post_string . "&" . "fname=" . $fname . "&" . "lname=" . $lname;
				$post_string = $post_string . "&" . "phone=" . $phone;
				$post_string = $post_string . "&" . "email=" . $email;
				$post_string = $post_string . "&" . "status=" . $status;
				$post_string = $post_string . "&" . "start_dte=" . $start_dte;
				$post_string = $post_string . "&" . "end_dte=" . $end_dte;

				/*value='$ID_student +'*/
				echo "<br/br/><form action='./students.html' method='GET'><button type='submit' name='ID_student' id='ID_student' value='$post_string'>Student ID: $ID_student, Name: $fname $lname</button></form>";
			}
			echo "<br/><br/><form action='./students.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
		} else {
			echo "Error in searching for student record(s).";
			echo "<form action='./students.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
		}
	}

	mysqli_close($conn);
} else if ($option == "Add Student") {
	/* For inserting a student record */
	if ($fname != "" && $lname != "") {
		$INSERT = "INSERT INTO t_students (fname, lname, phone, email, status, start_dte, end_dte) VALUES ('$fname', '$lname', '$phone', '$email', '$status', '$start_dte', '$end_dte')";
		$stmt = $conn->prepare($INSERT);
		//$stmt->bind_param('ssssiss', $fname, $lname, $phone, $email, "0", "2019-11-02", "2019-11-04");
		$stmt->execute();
		$rnum = $stmt->affected_rows;
		printf("Number of rows effected: %d and %d.\n", $stmt->affected_rows, $rnum);
		if ($rnum == 1) {
			echo "New record inserted successfully";
			echo "<form action='./students.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
			echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
		} else {
			echo "Failure to Insert record.";
			echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
		}

		mysqli_close($conn);
	} else {
		echo "All fields (except student ID) are required";
		echo "<form action='./students.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
		die();
	}
} else if ($option == "Edit Student") {
	/*Update Editing a student*/
	if ($ID_student != "") {
		$UPDATE = "UPDATE t_students SET fname='$fname', lname='$lname', phone='$phone', email='$email', status='$status', start_dte='$start_dte', end_dte='$end_dte' WHERE ID_student='$ID_student'";

		//$UPDATE = "UPDATE t_students SET fname='$fname', lname='$lname', ";
		//$UPDATE = $UPDATE + "phone='$phone', email='$email', status='$status', ";
		//$UPDATE = $UPDATE + "start_dte='$start_dte', end_dte='$end_dte' WHERE ID_student='$ID_student'";

		$stmt = $conn->prepare($UPDATE);
		$stmt->execute();
		$rnum = $stmt->affected_rows;
		printf("Number of rows effected: %d and %d.\n", $stmt->affected_rows, $rnum);
		if ($rnum == 1) {
			echo "Update student record executed, changes were successfully made to database.";
			echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
		} else {
			echo "Update student record executed, changes were not made to database.";
			echo "<form action='./index.html' method='get'><input type='submit' value='Go Back to Main Menu'/></form>";
		}

		mysqli_close($conn);
	} else {
		echo "Error in updating student must include student ID to edit record.";
		echo "<form action='./students.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
		die();
	}
} else if ($option == "Delete Student") {
	/*Deleting a student*/
	if ($ID_student != "") {

		$DELETE = "DELETE FROM t_students WHERE ID_student='$ID_student'";
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
		echo "<form action='./students.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
		die();
	}
} else {
	echo "Error: Option not found.";
	echo "<form action='./students.html' method='get'><input type='submit' value='Go Back to Manage Students'/></form>";
}
