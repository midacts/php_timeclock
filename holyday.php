<?php
// Insert Holidays into the info table of the timeclock database
// Author: John Patrick McCarthy
// Date: 8th January, 2014
// Version 1.0
//
// To God only wise, be glory through Jesus Christ forever. Amen.
//
// Romans 16:27 ; I Corinthians 15:1-4
//----------------------------------------------------------------
//
// #
// # Please change the dates in the first array, $holyday_date, to the correct dates
// #
//
// Error reporting and database credential file
error_reporting(0);
require 'connect.php';
////////////
// ARRAYS //
////////////
//
// HOLYDAY_DATE ARRAY
//
// List of holydays with their dates
$holyday_date = array(  array("New Year's Day", "01/01/2014"),
                        array("Memorial Day", "05/26/2014"),
                        array("Independence Day", "07/04/2014"),
                        array("Labor Day", "09/01/2014"),
                        array("Thanksgiving Day", "11/27/2014"),
                        array("Black Friday", "11/28/2014"),
                        array("Christmas Day", "12/25/2014"),
                        array("The Day After Christmas", "12/26/2014"),
                     );
//
// HOLYDAYS ARRAY - Main Holyday array
//
// holyday array that gets populated with epoch time in and out
$holydays = array ( array ( holyday         => "New Year's Day",
                            holyday_in      => "",
                            holyday_out     => "",
                            notes           => "New Year's Day"
                          ),
                    array ( holyday         => "Memorial Day",
                            holyday_in      => "",
                            holyday_out     => "",
                            notes           => "Memorial Day"
                          ),
                    array ( holyday         => "Independence Day",
                            holyday_in      => "",
                            holyday_out     => "",
                            notes           => "Indepenedence Day"
                          ),
                    array ( holyday         => "Labor Day",
                            holyday_in      => "",
                            holyday_out     => "",
                            notes           => "Labor Day"
                          ),
                    array ( holyday         => "Thanksgiving Day",
                            holyday_in      => "",
                            holyday_out     => "",
                            notes           => "Thanksgiving Day"
                          ),
                    array ( holyday         => "Black Friday",
                            holyday_in      => "",
                            holyday_out     => "",
                            notes           => "Black Friday"
                          ),
                    array ( holyday         => "Christmas Day",
                            holyday_in      => "",
                            holyday_out     => "",
                            notes           => "Christmas Day"
                          ),
                    array ( holyday         => "Day After Christmas",
                            holyday_in      => "",
                            holyday_out     => "",
                            notes           => "Day After Christmas"
                          ),
                  );
//
// TIMECLOCK EMPLOYEE LIST ARRAY
//
// Get Array of all Current Employees
$query = "SELECT * FROM employees";
if(!$result = $db->query($query)) {
    die('There was an error running the query [' . $db->error . ']');
}
// Makes an array of every user in the employees database
while($row = $result->fetch_assoc()){
    $results[] = $row;
}
// Remove admin user
unset($results[0]);

//////////////////////////////////////////////////////////////
// POPULATES MAIN HOLYDAYS ARRAY WITH CURRENT HOLYDAY DATES //
//////////////////////////////////////////////////////////////
// Gets the clock in and clock out epoch time stamps for each holyday
for ($row = 0; $row < count($holydays); $row++)
{
// Sets the set_date variable to the date of a holyday in the holyday_date array
$set_date=$holyday_date[$row][1];
// Get's the holyday's clock in epoch time
$date_in = new DateTime("$set_date 08:00:00"); // format: MM/DD/YYYY 00:00:00
// Stores the holyday's clock in time in epoch form
$holyday_in=$date_in->format('U');
// Get's the holyday's clock out epoch time
$date_out = new DateTime("$set_date 16:00:00"); // format: MM/DD/YYYY 00:00:00;
// Stores the holyday's clock out time in epoch form
$holyday_out=$date_out->format('U');
// Sets the holyday_in value in the mail holydays array
$holydays[$row]["holyday_in"]=$holyday_in;
// Sets the holyday_out value in the mail holydays array
$holydays[$row]["holyday_out"]=$holyday_out;
}

////////////////////////////////////////
// INSERT HOLYDAY TIMES FOR EACH USER //
////////////////////////////////////////
// For loop that loops through every holyday
for ($matrix = 0; $matrix < count($holydays); $matrix++)
{
$set_holyday_in=$holydays[$matrix]["holyday_in"];
$set_holyday_out=$holydays[$matrix]["holyday_out"];
$set_holyday_notes=$holydays[$matrix]["notes"];
    // For loop that loops through every employee
    for ($row = 1; $row < count($results) + 1; $row++)
    {
	$emp_name=$results[$row]['empfullname'];
	/////////////////////////////
	// ADDS THE CLOCK IN TIMES //
	/////////////////////////////
	// Creates a variable for the query we are going to use for punch in times
	$insert_holyday_in="INSERT INTO info (fullname, `inout`, timestamp, notes) VALUES (?, 'in', ?, ?)";
	// Query variable used to check for existing records
	$check_dup_in="SELECT * FROM info WHERE fullname = '".$emp_name."' AND timestamp = '".$set_holyday_in."'";
	// Variable used to run the query
	$dup_query_in=$db->query($check_dup_in);
	// Variable that stores the number of rows with existing records
	$check_row_in=$dup_query_in->num_rows;
	// Checks if there is NOT any number of rows greater than 0 with existing records in the database
	//
	// ** NOTE **
	//
	// This if statement only runs if a record does not already exist
	if (!$check_row_in > 0)
	{
		// Checks to make sure the query doesn't have any bugs
		if (!($statement_in = $db->prepare($insert_holyday_in)))
		{
		    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	$statement_in->bind_param('sis', $emp_name, $set_holyday_in, $set_holyday_notes);
	$statement_in->execute();
	}
	else
	{
		echo "Record for $emp_name on the $set_holyday_notes holyday already exist!";
		echo '<br />';
	}

	//////////////////////////////
	// ADDS THE CLOCK OUT TIMES //
	//////////////////////////////
	// Creates a variable for the query we are going to use for punch in times
	$insert_holyday_out="INSERT INTO info (fullname, `inout`, timestamp, notes) VALUES (?, 'out', ?, ?)";
	// Checks for already existing records. Used to prevent duplicate entries
	$check_dup_out="SELECT * FROM info WHERE (fullname = '$emp_name' AND timestamp = '$set_holyday_out')";
	// Variable used to run the query
	$dup_query_out=$db->query($check_dup_out);
	// Variable that stores the number of rows with existing records
	$check_row_out=$dup_query_out->num_rows;
	// Checks if there is NOT any number of rows greater than 0 with existing records in the database
	//
	// ** NOTE **
	//
	// This if statement only runs if a record does not already exist
	if (!$check_row_out > 0)
	{
	// Checks to make sure the query doesn't have any bugs
	if (!($statement_out = $db->prepare($insert_holyday_out)))
	{
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$statement_out->bind_param('sis', $emp_name, $set_holyday_out, $set_holyday_notes);
	$statement_out->execute();
	}
	else
	{
		echo "Record for $emp_name on the $set_holyday_notes holyday already exist!";
		echo '<br />';
	}
    }
}
echo "Holydays added successfully!";
?>
