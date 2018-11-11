<?php
session_start();

include 'header.php';
include_once 'dbconnection.php';
require_once 'functions.php';

$google_redirect_url = $_SERVER['PHP_SELF'];
$loggedUser = $_SESSION['userid'];
$dbConnect = $mysqli;


/*
if($DEBUG)
	$dbConnect = new mysqli(null, $db_username, $db_password, $dbName,null, '/cloudsql/awos-beta:awos');
else
	$dbConnect = new mysqli(null, $db_username, $db_password, $dbName,null, '/cloudsql/awos-beta:awos');

 if ($dbConnect->connect_error) {
        die('Error : ('. $dbConnect->connect_errno .') '. $dbConnect->connect_error);
    }

if($dbConnect == null)
	debugStr("Database Connection in cache is invalid");
else
    debugStr("Database Connection in cache is valid");
*/

$fname;
$lname;
$block;
$apt;
$mobile;

debugStr($loggedUser);


// Check if this is a GET or a POST

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    //*****************************
    // Read the user set values
    // & store them in the database
    //******************************
    $fname = htmlspecialchars($_POST["fname"]);
    $lname = htmlspecialchars($_POST["lname"]);
    $block = htmlspecialchars($_POST["block"]);
    $apt   = htmlspecialchars($_POST["apt"]);
    $mobile= htmlspecialchars($_POST["mobile"]);
    
    // Check if Mobile number is prepended with +91. If not prepend it
    
    $findPlus = strpos($mobile, '+');
    debugStr("Mobile prepended by + = " .$findPlus);
    if($findPlus === FALSE)
    {
        $mobile = "+91".$mobile;
    }
    
    $updateCmd = 'Update users set Fname ="'.$fname. '", Lname ="'.$lname. '",mobile="'.$mobile.'", block="'. $block . '", apt="'.$apt.'" Where userid ="' . $loggedUser.'"';
    debugStr($updateCmd);
    if($dbConnect->query($updateCmd) == TRUE)
    {
       debugStr("Profile Updated");
       $dbConnect->close();
    }
    else
    {
      die('Error : ('. $dbConnect->connect_errno .') '. $dbConnect->connect_error);
    }
    
    
    
}
else
{
   // Read values from the database
   
   $savedProfile = getUserFromEmail($dbConnect, $loggedUser);
   $fname = $savedProfile['Fname'];
   $lname = $savedProfile['Lname'];
   $block = $savedProfile['block'];
   $apt = $savedProfile['apt'];
   $mobile = $savedProfile['mobile'];
   
   // Close the database connection
   $dbConnect->close();

}





?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Please Update your profile</title>
    </head>
    <body>
        <?php
               
        echo '<form method="post" action='. $google_redirect_url.'>';
        echo '<table>';
         echo '<tr><td><b>First Name</b></td><td><b>Last Name</b></td></tr>';
         echo '<tr><td><input name="fname" value="'.$fname.'"/></td><td><input name="lname" value="'.$lname.'"/></td></tr>';
        echo '<tr><td><b>Block #</b></td><td><b>Apt #</b></td></tr>';
        echo '<tr><tr><td><input name="block" value="'.$block.'"/></td><td><input name="apt" value="'.$apt.'"/></td></tr>';
        echo '<tr><td><b>Mobile # prefixed with +91</b></td></tr>';
        echo '<tr><td><input name="mobile" value="'.$mobile.'"/></td></tr>';
        echo '<tr><td></td></tr>';
        echo '<tr><td></td></tr>';
        echo '<tr><td></td></tr>';
        echo '<tr><td><input type="submit" value="Update"></td></tr>';
        echo '</table>';
        echo "<br>";
        
        
        echo '</form>';
        ?>
    </body>
</html>