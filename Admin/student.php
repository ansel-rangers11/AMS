<p><font size="20">Student Database</p>

<form method="POST" action="studentView.php">
   <p><input type="submit" value="Initialize" name="reset"></p>
</form>

<form method="POST" action="studentView.php">
   <p><input type="text" placeholder="StudentID" name="insStudentID1" size="8"><input type="submit" value="Please identify with your StudentID" name="identification"></p>
</form>

<a href="student.php"><font size= "3">Click Here to Enable Admin View (ADMINS ONLY!)</a><br/>
<a href="main.php"><font size= "1.5">Back to Main Menu</a>

<!-- Create a form to pass the values.
     See below for how to get the values. -->

<p><font size="3"> Update address by inserting your studentID and the desired new address below: </p>
<p><font size="2"> Student ID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   New Address&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   New Major&nbsp;&nbsp;&nbsp;&nbsp;
                   New Postal Code&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</font></p>

<form method="POST" action="studentView.php">
<!-- refreshes page when submitted -->

   <p><input type="text" name="studentID" size="6">
     <input type="text" name="newAddress" size="18">
     <input type="text" name="newMajor" size="6">
     <input type="text" name="newPostalCode" size="10">

<!-- Define two variables to pass values. -->

<input type="submit" value="update" name="updatesubmit"></p>
</form>

<html>
<style>
    table {
        width: 20%;
        border: 1px solid black;
    }

    th {
        font-family: Arial, Helvetica, sans-serif;
        font-size: .7em;
        background: #666;
        color: #FFF;
        padding: 2px 6px;
        border-collapse: separate;
        border: 1px solid #000;
    }

    td {
        font-family: Arial, Helvetica, sans-serif;
        font-size: .7em;
        border: 1px solid #DDD;
        color: black;
    }

    ::placeholder {
    color: gray;
    opacity: 0.65; /* Firefox */
    }

    :-ms-input-placeholder { /* Internet Explorer 10-11 */
    color: red;
    }

    ::-ms-input-placeholder { /* Microsoft Edge */
    color: red;
    }
</style>
</html>



<?php

/* This tells the system that it's no longer just parsing
   HTML; it's now parsing PHP. */

// keep track of errors so it redirects the page only if
// there are no errors


$localvarrr = 3;
$success = True;
$db_conn = OCILogon("ora_ansel", "a15984164", 
                    "dbhost.students.cs.ubc.ca:1522/stu");

function executePlainSQL($cmdstr) {
     // Take a plain (no bound variables) SQL command and execute it.
	//echo "<br>running ".$cmdstr."<br>";
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr);
     // There is a set of comments at the end of the file that
     // describes some of the OCI specific functions and how they work.

	if (!$statement) {
		echo "<br>Cannot parse this command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn);
           // For OCIParse errors, pass the connection handle.
		echo htmlentities($e['message']);
		$success = False;
	}

	$r = OCIExecute($statement, OCI_DEFAULT);
	if (!$r) {
		echo "<br>Cannot execute this command: " . $cmdstr . "<br>";
		$e = oci_error($statement);
           // For OCIExecute errors, pass the statement handle.
		echo htmlentities($e['message']);
		$success = False;
	} else {

	}
	return $statement;

}

function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}

function executeBoundSQL($cmdstr, $list) {
	/* Sometimes the same statement will be executed several times.
        Only the value of variables need to be changed.
	   In this case, you don't need to create the statement several
        times.  Using bind variables can make the statement be shared
        and just parsed once.
        This is also very useful in protecting against SQL injection
        attacks.  See the sample code below for how this function is
        used. */

	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr);

	if (!$statement) {
		echo "<br>Cannot parse this command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn);
		echo htmlentities($e['message']);
		$success = False;
	}

	foreach ($list as $tuple) {
		foreach ($tuple as $bind => $val) {
			//echo $val;
			//echo "<br>".$bind."<br>";
			OCIBindByName($statement, $bind, $val);
			unset ($val); // Make sure you do not remove this.
                              // Otherwise, $val will remain in an
                              // array object wrapper which will not
                              // be recognized by Oracle as a proper
                              // datatype.
		}
		$r = OCIExecute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute this command: " . $cmdstr . "<br>";
			$e = OCI_Error($statement);
                // For OCIExecute errors pass the statement handle
			echo htmlentities($e['message']);
			echo "<br>";
			$success = False;
		}
	}

}

function printTable($resultFromSQL, $namesOfColumnsArray)
{
        echo "<table>";
        echo "<tr>";
        // iterate through the array and print the string contents
        foreach ($namesOfColumnsArray as $name) {
            echo "<th>$name</th>";
        }
        echo "</tr>";

        while ($row = OCI_Fetch_Array($resultFromSQL, OCI_BOTH)) {
            echo "<tr>";
            $string = "";

            // iterates through the results returned from SQL query and
            // creates the contents of the table
            for ($i = 0; $i < sizeof($namesOfColumnsArray); $i++) {
                $string .= "<td>" . $row["$i"] . "</td>";
            }
            echo $string;
            echo "</tr>";
        }
        echo "</table>";
}



// Connect Oracle...
if ($db_conn) {
    global $localvarrr;
			if (array_key_exists('updatesubmit', $_POST)) {
				// Update tuple using data from user
                if ($_POST['newAddress'])
                executePlainSQL("update student set address = '" . $_POST['newAddress'] . "' where studentID = '" . $_POST['studentID'] . "'");
                if ($_POST['newMajor'])
                executePlainSQL("update student set major = '" . $_POST['newMajor'] . "' where studentID = '" . $_POST['studentID'] . "'");
                if ($_POST['newPostalCode'])
                executePlainSQL("update student set postalCode = '" . $_POST['newPostalCode'] . "' where studentID = '" . $_POST['studentID'] . "'");
				OCICommit($db_conn);
                }


    $lol = array_key_exists('identification', $_POST);
    $lol = !$lol;
	if ($_POST && $success && $lol) {
        //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
        header("location: studentView.php");
	} else {
        // Select data...
        if (array_key_exists('identification', $_POST)) {
            $idsearched = $_POST['insStudentID1'];
            $result = executePlainSQL("select student.studentID, student.name, student.major, student.address, student.postalCode, postalCode.city, postalCode.province from student, postalCode where studentID='" . $idsearched . "'AND student.postalCode=postalCode.postalCode");
            $columnNames = array("StudentID", "Student Name", "Major", "Address", "Postal Code", "City", "Province");
            printTable($result, $columnNames);
        }
	}

  // Show clubs selected student is a member of
  $result2 = executePlainSQL("select clubName from memberOf where studentID = '" . $idsearched . "'");
  $columnNames2 = array("Clubs You Are a Member Of");
  printTable($result2, $columnNames2);

  // Show total number of registered students
  $result1 = executePlainSQL("select COUNT(*) from STUDENT");
  $columnNames1 = array("Total Number of Registered Students");
  printTable($result1, $columnNames1);

  // Show clubs selected student is a member of
  $result3 = executePlainSQL("SELECT DISTINCT studentID FROM memberOf WHERE studentID not in (SELECT studentID FROM ((SELECT studentID, clubName FROM (select clubName from club) cross join (select distinct studentID from memberOf)) MINUS (SELECT studentID, clubName FROM memberOf)))");
  $columnNames3 = array("Student IDs of Students in EVERY Club");
  printTable($result3, $columnNames3);

  	//Commit to save changes...
  OCILogoff($db_conn);

} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}
