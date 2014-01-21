<?php
// Initial insert of all employees into the employees table of the timeclock database
// Author: John Patrick McCarthy
// Date: 8th January, 2014
// Version 1.0
//
// To God only wise, be glory through Jesus Christ forever. Amen.
//
// Romans 16:27 ; I Corinthians 15:1-4
//----------------------------------------------------------------
//
// Error reporting and database credential file
error_reporting(0);
require 'connect.php';
////////////////////////
//
// EMPLOYEE LIST ARRAY
//
////////////////////////
$employees =array ( array ( empfullname		=> "admin",
                            displayname		=> "admin",
                            employee_passwd	=> "xyAjYtmfRYx/.",
                            email		=> "admin@example.com",
                            groups		=> "Deptartment",
                            office		=> "Company",
                            admin		=> "1",
                            reports		=> "1",
                            time_admin		=> "1",
                            disabled		=> "0"
                          ),
);

echo '<pre>';
print_r($employees);
echo '</pre>';
echo '<br />';

///////////////////////////////////////////////////
//
// FOR LOOP TO INSERT EMPLOYEES INTO THE DATABASE
//
///////////////////////////////////////////////////
for ($row = 0; $row < count($employees); $row++)
{
// Sets a variable for each value in the array
$empfullname=$employees[$row]['empfullname'];
$tstamp=strtotime(date("Y/m/d, g:i:s a"));
$displayname=$employees[$row]['displayname'];
$employee_passwd=$employees[$row]['employee_passwd'];
$email=$employees[$row]['email'];
$groups=$employees[$row]['groups'];
$office=$employees[$row]['office'];
$admin=$employees[$row]['admin'];
$reports=$employees[$row]['reports'];
$time_admin=$employees[$row]['time_admin'];
$disabled=$employees[$row]['disabled'];

// Variable used to insert the records into the employees table
$insert_emp="INSERT INTO employees (empfullname, tstamp, displayname, employee_passwd, email, groups, office, admin, reports, time_admin, disabled)
VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
// Variable used to check for existing employees in order to prevent mulitple records
$check_emp_dup="SELECT * FROM employees WHERE empfullname = '".$empfullname."'";
// Database query to check for multiple records
$check_emp_dup_query=$db->query($check_emp_dup);
// Variable to check for an existance of rows, therefore knowing if an employee exists
$check_emp_dup_query_row=$check_emp_dup_query->num_rows;
// Prints the number of records found in database
echo "$empfullname has $check_emp_dup_query_row records found in the employees table.";
///////////
//
// *NOTE*
//
///////////
// This if statement will only run if the employee is not already in the database
    if (!$check_emp_dup_query_row > 0)
    {
        echo "You are here"; echo '<br />'; echo '<br />';
        // Checks for errors before pushing the query
        if (!($statement = $db->prepare($insert_emp)))
        {
            echo "Prepare failed: (" . $db->errno . ") " . $db->error;
        }
    // Binds the variable parameters
    $statement->bind_param('sssssssssss', $empfullname, $tstamp, $displayname, $employee_passwd, $email, $groups, $office, $admin, $reports, $time_admin, $disabled);
    // Executes the query
    $statement->execute();
    // Prints the number of affected rows that were affected by the query
    printf("%d Row inserted.\n", $statement->affected_rows); echo '<br />';
    }
    else
    {
        echo ": FAILURE"; echo '<br />';
    }

}

?>
