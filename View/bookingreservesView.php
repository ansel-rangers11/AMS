<p><font size="20">AMS Booking Reservations Database</p>
<a href="bookingreserves.php"><font size= "1.5">Click Here to Enable Admin View (ADMINS ONLY!)</a><br/>
<a href="main.php"><font size= "1.5">Back to Main Menu</a>

<form method="POST" action="bookingreservesView.php"> 
   <p><input type="submit" value="Initialize" name="reset"></p>
</form>

<form method="POST" action="bookingreservesView.php"> 
   <p><input type="text" placeholder="type Booking ID here.." name="BookingSearchString" size="18">
   <input type="submit" value="Search for a Booking by its ID here" name="BookingSearch"></p>
</form>


<form method="POST" action="bookingreservesView.php"> 
   <p><input type="text" placeholder="type Student ID here.." name="StudentSearchString" size="18">
   <input type="submit" value="Search for a Booking by Student ID here" name="StudentSearch"></p>
</form>

<form method="POST" action="bookingreservesView.php"> 
   <p><input type="date" placeholder="type start date here.." name="startDateTime" size="18">
   <input type="date" placeholder="type end date here.." name="endDateTime" size="18">
   <input type="submit" value="Search for Booking Reservations between given start and end dates" name="BookingDateSearch"></p>
</form>



<p><font size="3">Search for information about a specific booked Location by its Booking ID: </p>
<form method="POST" action="bookingreservesView.php"> 
   <p><input type="text" placeholder="type Booking ID here.." name="SpecificLocationBookingSearchString" size="18">
    <input type="submit" value="Search" name="SpecificLocationBookingSearch"></p>
</form>

<p><font size="2">Choose the Booking Reservation column you wish to update, if choosing event date, please enter the info in the format of YYYY-MM-DD:</p>
<form id="s" method="post" action="bookingreservesView.php">
   <select name="updateValue">
   <option value="BookingID">Booking ID</option>
    <option value="StudentID">Student ID</option>
    <option value="startDateTime">Booking Start Date Time</option>
    <option value="status">Booking Status</option>
    <option value="LocationID">Location ID</option>
  </select> 
<input type="text" placeholder="type new value here.." name="updateValueData" size="18">
<p><font size="3">Identify the Booking ID and Start Date Time of the Booking Reservation you want to change the above value for :</p>
<input type="text" placeholder="type Booking ID here.." name="updateValueDataName" size="18">
<input type="date" name="updateValueDataDate" size="18">
<input type="submit" name="Submit" value="updateValueAction">


<p><font size="3">Insert new Booking information into our Booking Reservation database table below:</p>

<form method="POST" action="bookingreservesView.php">
<!-- refreshes page when submitted -->

   <p>
    <input type="text" placeholder="BookingID" name="insBookingID" size="18">
    <input type="text" placeholder="StudentID" name="insStudentID" size="18">
    <input type="date" placeholder="StartDateTime" name="insStartDateTime" size="18">
    <input type="date" placeholder="EndDateTime"name="insEndDateTime" size="18">
    <input type="text" placeholder="Status" name="insStatus" size="18">
    <input type="text" placeholder="LocationID" name="insLocationID" size="18">
<!-- Define two variables to pass values. -->    
<input type="submit" value="Insert Booking Data" name="insertsubmit"></p>

</form>


<p><font size="2">To delete your reservation, please type your Student ID here:</p>
<form method="POST" action="bookingreservesView.php"> 
   <p><input type="text" placeholder="type Student ID here.." name="deleteStudentBookingIDRow" size="18">
   <input type="submit" value="StudentDelete Row" name="deleteStudentCondition"></p>
</form>

<!-- Create a form to pass the values.  
     See below for how to get the values. --> 



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

//See all location listings 
//Search locations by name, input textbox

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
    if (array_key_exists('deleteStudentCondition', $_POST)) {
        $DeleteStudentCertainRow = $_POST['deleteStudentBookingIDRow'];
        executePlainSQL("delete from bookingreserves where StudentID = " . $DeleteStudentCertainRow . " ");
        OCICommit($db_conn);
        

    } else {
        if (array_key_exists('insertsubmit', $_POST)) {
            $localvarrr = 6;
            // Get values from the user and insert data into 
                // the table.
            $tuple = array (
                ":bind1" => $_POST['insBookingID'],
                ":bind2" => $_POST['insStudentID'],
                ":bind3" => $_POST['insStartDateTime'],
                ":bind4" => $_POST['insEndDateTime'],
                ":bind5" => $_POST['insStatus'],
                ":bind6" => $_POST['insLocationID']
                
            );
            $alltuples = array (
                $tuple
            );
            executeBoundSQL("insert into bookingreserves values (:bind1, :bind2, TO_DATE(:bind3,'yyyy/mm/dd'), TO_DATE(:bind4,'yyyy/mm/dd'), :bind5, :bind6)", $alltuples);
            OCICommit($db_conn);

        }
        else {
            if (array_key_exists('deleteAll', $_POST)) {
                executePlainSQL("delete from location");
                OCICommit($db_conn);
            } 
            else {
                if (array_key_exists('updateValueAction', $_POST) || array_key_exists('updateValue', $_POST)) {
                    $updateValueDataGeneric = $_POST['updateValueData'];
                    if ($_POST['updateValue'] === 'startDateTime') {
                        $updateValueDataGeneric = "TO_DATE('" . $updateValueDataGeneric . "', 'yyyy/mm/dd')";

                    } else {
                        $updateValueDataGeneric = "'" . $updateValueDataGeneric . "'";
                    }
                    $tuple = array (
                        ":bind1" => $_POST['updateValueData'],
                        ":bind2" => $_POST['updateValue'],
                        ":bind3" => $_POST['updateValueDataName'],
                        ":bind4" => $_POST['updateValueDataDate']

                    );
                    $alltuples = array (
                        $tuple
                    );
                    executeBoundSQL("update bookingreserves set " . $_POST['updateValue'] . " = " . $updateValueDataGeneric . " where BookingID = ' " . $_POST['updateValueDataName'] . " ' and startDateTime = TO_DATE(:bind4, 'yyyy/mm/dd')",  $alltuples);
                    OCICommit($db_conn);
                }
            }
        } 
    }
    $lol = array_key_exists('BookingSearch', $_POST) || array_key_exists('BookingDateSearch', $_POST) || array_key_exists('StudentSearch', $_POST) || 
           array_key_exists('updateValue', $_POST);
    $lol = !$lol;
    if ($_POST && $success && $lol) {
        //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
        header("location: locationView.php");
    } else {
        // Select data...
        $columnNames = array("Booking ID", "Student ID", "Start Date Time", "End Date Time", "Status", "Location ID");
        if (array_key_exists('BookingSearch', $_POST)) {
            $bookingsearched = $_POST['BookingSearchString'];                             
            $result = executePlainSQL("select * from bookingreserves where BookingID = " . $bookingsearched . " ");
        } elseif (array_key_exists('StudentSearch', $_POST)) {
            $studentsearched = $_POST['StudentSearchString'];
            $result = executePlainSQL("select * from bookingreserves where StudentID = " . $studentsearched . "");

        } elseif (array_key_exists('BookingDateSearch', $_POST)) {
            $startBookingDateSearch = $_POST['startDateTime'];
            $endBookingDateSearch = $_POST['endDateTime'];
            $result = executePlainSQL("select * from bookingreserves where startDateTime = TO_DATE('" . $startBookingDateSearch . "', 'yyyy/mm/dd') AND endDateTime = TO_DATE('" . $endBookingDateSearch . "', 'yyyy/mm/dd') ");
        } else {
            $result = executePlainSQL("select * from bookingreserves");
        }
     //   $columnNames = array("Booking ID", "Student ID", "Start Date Time", "End Date Time", "Status", "Location ID");
        printTable($result, $columnNames);
    }

    //Commit to save changes...
    OCILogoff($db_conn);


} else {
    echo "cannot connect";
    $e = OCI_Error(); // For OCILogon errors pass no handle
    echo htmlentities($e['message']);
}