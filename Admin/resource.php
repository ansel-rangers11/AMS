

<p><font size="20">AMS Resource Database</p>
<a href="resourceView.php"><font size= "1.5">Click Here to see student's view</a><br/>
<a href="accesses.php"><font size= "1.5">Look at visit and accesses information</a>
<a href="main.php"><font size= "1.5">Back to Main Menu</a>

<form method="POST" action="resource.php">
   <p><input type="submit" value="Initialize" name="reset"></p>
</form>

<form method="POST" action="resource.php">
   <p><input type="text" placeholder="type resource name here.." name="resourceSearchString" size="30">
   <input type="submit" value="Search for a resource by its resource name here" name="resourceSearch"></p>
</form>

<form method="POST" action="resource.php">
   <p><input type="text" placeholder="type location building code (ie. SUB)" name="resourceLoc" size="30">
   <input type="submit" value="Look at all resources available at location" name="resourceLocSearch"></p>
</form>

<font size="3">WARNING: Deleting a resource is an irreversible action.
<form method="POST" action="resource.php">
   <p>
   <input type="text" placeholder="Enter the resource name" name="ResourceSearchDelete" size="30">
   <input type="submit" value="Delete resource" name="deleteResource"></p>
</form>

<form id="s" method="post" action="resource.php">
   <select name="updateValue">
   <option value="resourceName">Resource Name</option>
    <option value="description">Resource Description</option>
    <option value="contact">Resource Contact</option>
    <option value="hours">Hours</option>
    <option value="locationID">Location ID</option>
  </select>
<input type="text" placeholder="type new value here.." name="updateValueData" size="18">
<p><font size="3">Identify the resource name of which you want to change the above value for :</p>
<input type="text" placeholder="type resource name here.." name="updateValueDataName" size="30">
<input type="submit" name="Submit" value="updateResource">

</form>

<!-- <p><font size="3">Search for a resource with at least the indicated hours of operation :</p>
<form method="POST" action="resource.php">
    <select name="updateValueHours">
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="40">40</option>
    </select>
   <p><input type="submit" value="Search" name="resourceHoursSearch"></p>
</form> -->



<p><font size="3">Insert a new resource info into our Resource database table below:</p>

<form method="POST" action="resource.php">
<!-- refreshes page when submitted -->

   <p>
    <input type="text" placeholder="Resource Name" name="insresourceName" size="18">
    <input type="text" placeholder="Resource Description" name="insDescription" size="18">
    <input type="text" placeholder="Resource Contact" name="insContact" size="18">
    <input type="text" placeholder="Hours"name="insHours" size="18">
    <input type="text" placeholder="Location ID"name="insLocationID" size="18">
<!-- Define two variables to pass values. -->
<input type="submit" value="Insert Resource Data" name="insertsubmit"></p>
<input type="submit" value="Clear Resource Database" name="deleteAll"></p>
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

//See all resource listings
//Search resources by name, input textbox

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
    if (array_key_exists('deleteResource', $_POST)) {
        executePlainSQL("delete from resourcebasedat
        where resourceName like '%" . $_POST['resourceSearchDelete'] . "%' ");
        OCICommit($db_conn);
    }	elseif (array_key_exists('reset', $_POST)) {
		// // Drop old table...
		// echo "<br> dropping table <br>";
		// executePlainSQL("Drop table resourcebasedat");

		// // Create new table...
		// echo "<br> creating new table <br>";
		// executePlainSQL("create table resourcebasedat (resourceName varchar2(30), description varchar2(30), contact varchar2(30), hours varchar(8), locationID varchar(30), primary key (resourceName))");
        // OCICommit($db_conn);

	} else {
		if (array_key_exists('insertsubmit', $_POST)) {
            $localvarrr = 6;
			// Get values from the user and insert data into
                // the table.
			$tuple = array (
				":bind1" => $_POST['insresourceName'],
                ":bind2" => $_POST['insDescription'],
                ":bind3" => $_POST['insContact'],
                ":bind4" => $_POST['insHours'],
                ":bind5" => $_POST['insLocationID']

			);
			$alltuples = array (
				$tuple
			);
			executeBoundSQL("insert into resourcebasedat values (:bind1, :bind2, :bind3, :bind4, :bind5)", $alltuples);
			OCICommit($db_conn);

        }
        else {
            if (array_key_exists('deleteAll', $_POST)) {
                executePlainSQL("delete from resourcebasedat where");
                OCICommit($db_conn);
            }
            else {
                if (array_key_exists('updateResource', $_POST) || array_key_exists('updateValue', $_POST)) {
                    $tuple = array (
                        ":bind1" => $_POST['updateValueData'],
                        ":bind2" => $_POST['updateValue'],
                        ":bind3" => $_POST['updateValueDataName']
                    );
                    $alltuples = array (
                        $tuple
                    );
                    executeBoundSQL("update resourcebasedat set " . $_POST['updateValue'] . "='" . $_POST['updateValueData'] ."' where resourceName='" . $_POST['updateValueDataName'] . "'", $alltuples);
                    OCICommit($db_conn);
                }
            }
        }
    }
    $lol = array_key_exists('resourceSearch', $_POST) ||
           array_key_exists('resourceLocSearch', $_POST) ||
           array_key_exists('resourceLocSearch', $_POST);
    $lol = !$lol;
	if ($_POST && $success && $lol) {
        //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
        header("location: resource.php");
	} else {
        // Select data...
        $columnNames = array("Resource Name", "Resource Description", "Resource Contact", "Hours", "Location ID");
        if (array_key_exists('resourceSearch', $_POST)) {
            $eventsearched = $_POST['resourceSearchString'];
            $result = executePlainSQL("select * from resourcebasedat where resourceName like '%" . $eventsearched . "%'");
        }
        // elseif (array_key_exists('resourceHoursSearch', $_POST)) {
        //     $hoursSearched = $_POST['updateValueHours'];
        //     $result = executePlainSQL("select * from resourcebasedat where hours >= " . $hoursSearched . "");
        // }
        elseif (array_key_exists('resourceLocSearch', $_POST)) {
            $locsearched = $_POST['resourceLoc'];
            $columnNames = array("Resource Name", "Resource Contact", "Hours", "Building Code", "Area Code");
            $result = executePlainSQL("select r.resourceName, r.contact, r.hours, l.buildingCode, l.areaCode
                                        from resourcebasedat r, location l
                                        where r.locationID=l.locationID
                                        and l.buildingCode like '%" . $locsearched . "%'");
        }else {
            $result = executePlainSQL("select * from resourcebasedat");
        }
        printTable($result, $columnNames);
        // $nestAgg = executePlainSQL("select MAX(AVG(hours) )
        //                           from resourcebasedat group by locationID");
        // NESTED AGGREGATE
        $row = oci_fetch_array($nestAgg);
        $shoeRating = $row[0];

        // echo "<br>The maximum average of open hours of operation grouped by locationID: " . $shoeRating . "<br>";
	}

	//Commit to save changes...
    OCILogoff($db_conn);


} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}
