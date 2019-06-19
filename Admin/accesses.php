


<p><font size="20">AMS Visit and Access Database</p>
<a href="main.php"><font size= "1.5">Back to Main Menu</a>

<form method="POST" action="accesses.php">
   <p><input type="submit" value="Initialize" name="reset"></p>
</form>

<font size="3">WARNING: Deleting a visit is an irreversible action.
<form method="POST" action="accesses.php">
   <p>
   <input type="text" placeholder="Enter visit ID" name="visitsearchDelete" size="30">
   <input type="submit" value="Delete visit" name="deleteVisit"></p>
</form>

<form id="s" method="post" action="accesses.php">
   <select name="updateValue">
   <option value="visitID">Visit ID</option>
    <option value="accessDate">Access Date</option>
    <option value="timeIn">Time In</option>
    <option value="timeOut">Time Out</option>
  </select>
<input type="text" placeholder="type new value here.." name="updateValueData" size="18">
<p><font size="3">Identify the visit ID of which you want to change the above value for :</p>
<input type="text" placeholder="type visit ID here.." name="updateValueDataName" size="18">
<input type="submit" name="updateValueAction" value="Update Visit">

</form>



<p><font size="3">Insert a new club info into our Club database table below:</p>

<form method="POST" action="accesses.php">
<!-- refreshes page when submitted -->

   <p>
    <input type="text" placeholder="Visit ID" name="insVisitID" size="18">
    <input type="date" placeholder="Access Date" name="insAccessDate" size="18">
    <input type="timestamp" placeholder="Time In" name="insTimeIn" size="18">
    <input type="timestamp" placeholder="Time Out"name="insTimeOut" size="18">
<!-- Define two variables to pass values. -->
<input type="submit" value="Insert Visit Data" name="insertsubmit"></p>
<input type="submit" value="Clear Visit Database" name="deleteAll"></p>
<input type="submit" value="See All Records" name="seeAll">
</form>

<!-- Create a form to pass the values.
     See below for how to get the values. -->


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

//See all club listings
//Search clubs by name, input textbox

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

function printTable($resultFromSQL, $namesOfColumnsArray)
{
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
    if (array_key_exists('deleteVisit', $_POST)) {
        executePlainSQL("delete from visit
        where visitID like '%" . $_POST['visitSearchDelete'] . "%' ");
        OCICommit($db_conn);
    } elseif (array_key_exists('reset', $_POST)) {
		// // Drop old table...
		// echo "<br> dropping table <br>";
		// executePlainSQL("Drop table club");

		// // Create new table...
		// echo "<br> creating new table <br>";
		// executePlainSQL("create table club (clubName varchar2(30), description varchar2(30), contact varchar2(30), officeNumber varchar(8), primary key (clubName))");
        // OCICommit($db_conn);

	} else {
		if (array_key_exists('insertsubmit', $_POST)) {
            $localvarrr = 6;
			// Get values from the user and insert data into
                // the table.
			$tuple = array (
				        ":bind1" => $_POST['insVisitID'],
                ":bind2" => $_POST['insAccessDate'],
                ":bind3" => $_POST['insTimeIn'],
                ":bind4" => $_POST['insTimeOut']

			);
			$alltuples = array (
				$tuple
			);
			executeBoundSQL("insert into visit values (:bind1, :bind2, :bind3, :bind4)", $alltuples);
			OCICommit($db_conn);

        }
        else {
            if (array_key_exists('deleteAll', $_POST)) {
                executePlainSQL("delete from visit");
                OCICommit($db_conn);
            }
            else {
                if (array_key_exists('updateValueAction', $_POST) || array_key_exists('updateValue', $_POST)) {
                    $tuple = array (
                        ":bind1" => $_POST['updateValueData'],
                        ":bind2" => $_POST['updateValue'],
                        ":bind3" => $_POST['updateValueDataName']
                    );
                    $alltuples = array (
                        $tuple
                    );
                    executeBoundSQL("update visit set " . $_POST['updateValue'] . "='" . $_POST['updateValueData'] ."' where visitID='" . $_POST['updateValueDataName'] . "'", $alltuples);
                    OCICommit($db_conn);
                }
            }
        }
    }
    $lol = array_key_exists('clubMemberList', $_POST);
    $lol = !$lol;
	if ($_POST && $success && $lol) {
        //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
        header("location: accesses.php");
	} else {
        // Select data...
        $columnNames = array("Resource Name", "Visit ID", "Access Date", "Time In", "Time Out");
        $result = executePlainSQL("select * from accesses join visit on accesses.visitID=visit.visitID");
        printTable($result, $columnNames);
	}

	//Commit to save changes...
    OCILogoff($db_conn);


} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}
