
<p><font size="20">AMS Executive Database</p>
<a href="executiveView.php"><font size= "1.5">Click Here to see student's view</a><br/>
<a href="main.php"><font size= "1.5">Back to Main Menu</a>

<form method="POST" action="executive.php">
   <p><input type="text" placeholder="Type AMS exec name here.." name="executiveSearchString" size="18">
   <input type="submit" value="Search for an AMS executive by his or her name here" name="executiveSearch"></p>
</form>

<form id="s" method="post" action="executive.php">
   <select name="updateValue">
   <option value="executiveID">Executive ID</option>
    <option value="position">AMS Position</option>
    <option value="seniorID">Senior's ID</option>
  </select>
<input type="text" placeholder="Type new value here.." name="updateValueData" size="18">
<p><font size="3">Identify the executiveID of which you want to change the above value for :</p>
<input type="text" placeholder="Type executive ID here.." name="updateValueDataID" size="18">
<input type="submit" name="updateValueAction" value="Update">
<input type="submit" name="deleteValueAction" value="Delete">
</form>

<p><font size="3">Insert a new AMS Exec info into the database table below:</p>

<form method="POST" action="executive.php">
<!-- refreshes page when submitted -->
   <p>
    <input type="text" placeholder="Executive ID" name="insExecutiveID" size="18">
    <input type="text" placeholder="AMS Position" name="insPosition" size="18">
    <input type="text" placeholder="Senior's ID" name="insSeniorID" size="18">
<!-- Define two variables to pass values. -->
<input type="submit" value="Insert Exec Data" name="insertsubmit"></p>
<input type="submit" value="Clear Exec Database" name="deleteAll"></p>
<input type="submit" value="See All Records" name="seeAll">
</form>

<html>
<style>
    table {
        width: 20%;
        border: 1px solid black;
    }

    form {
        margin-bottom: 60px;
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

$localvarrr = 3;
$success = True;
$db_conn = OCILogon("ora_alewczuk", "a41700162",
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
function debug_to_console($data) {
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
function printTable($resultFromSQL, $namesOfColumnsArray){
        echo "<br>Here is the output, nicely formatted:<br>";
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
		if (array_key_exists('insertsubmit', $_POST)) {
            $localvarrr = 6;
			// Get values from the user and insert data into
                // the table.
			$tuple = array (
				":bind1" => $_POST['insExecutiveID'],
                ":bind2" => $_POST['insPosition'],
                ":bind3" => $_POST['insSeniorID']

			);
			$alltuples = array (
				$tuple
			);
			executeBoundSQL("insert into executiveOversees values (:bind1, :bind2, :bind3)", $alltuples);
			OCICommit($db_conn);

        }
        else {
            if (array_key_exists('deleteAll', $_POST)) {
                executePlainSQL("delete from executiveOversees");
                OCICommit($db_conn);
            }
            else {
                if (array_key_exists('updateValueAction', $_POST) && array_key_exists('updateValue', $_POST)) {
                    executePlainSQL("update executiveOversees set " . $_POST['updateValue'] . " = '" . $_POST['updateValueData'] . "' where executiveID = '" . $_POST['updateValueDataID'] . "'", $alltuples);
                    OCICommit($db_conn);
                }
                else {
                    if (array_key_exists('deleteValueAction', $_POST)) {
                        executePlainSQL("delete from executiveOversees where executiveID = '" . $_POST['updateValueDataID'] . "'", $alltuples);
                        OCICommit($db_conn);
                    }
            }
          }
        }

    $lol = array_key_exists('executiveSearch', $_POST) || array_key_exists('executiveHoursSearch', $_POST);
    $lol = !$lol;
	if ($_POST && $success && $lol) {
        //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
        header("location: executive.php");
	} else {
        // Select data...
        if (array_key_exists('executiveSearch', $_POST)) {
            $eventsearched = $_POST['executiveSearchString'];
            $result = executePlainSQL("select e1.executiveID, s1.name, e1.position, e1.seniorID, s2.name from executiveOversees e1 left outer join student s1 on e1.executiveID=s1.studentID left outer join student s2 on e1.seniorID=s2.studentID where s1.name like '%" . $eventsearched . "%'");
        } else {
          $result = executePlainSQL("select e1.executiveID, s1.name, e1.position, e1.seniorID, s2.name from executiveOversees e1 left outer join student s1 on e1.executiveID=s1.studentID left outer join student s2 on e1.seniorID=s2.studentID");
        }
        $columnNames = array("Exec Student ID", "Exec Name", "AMS Position", "Superior Student ID", "Superior Name");
        printTable($result, $columnNames);
	}

	//Commit to save changes...
    OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}
