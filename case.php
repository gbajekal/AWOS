<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
ob_start();
use google\appengine\api\users\User;
use google\appengine\api\users\UserService;
use \google\appengine\api\mail\Message;

require 'functions.php';
// Retrieve the database connection from the session

include_once 'dbconnection.php';




if($mysqli != null)
 debugStr("Got database connection");
$caseid = $_GET['case'];
debugStr("Case Id =".$caseid);

// Get the case parameteres
$case = getCase($mysqli, $caseid);

// Display the case
displayCase($case);

// Check if this is a callback from the update
if(isset($_POST["comments"]))
{
    $comments = htmlspecialchars($_POST["comments"]);
    debugStr("Comments:".$comments);
    $status   = htmlspecialchars($_POST["status"]);
    $id       = htmlspecialchars($_POST["id"]);
    $resolvedDate = date("Y-m-d H:i:s");
    debugStr("id:".$id);
    
    //$updateQuery = 'Update grievance set Comments ="'.$comments. '", Status="'. $status . '", Date_Resolved="'.now().'" Where id =' . $id;
    
   // debugStr("QS=".$updateQuery);
    
    if( $mysqli->query('Update grievance set Comments ="'.$comments. '", Status="'. $status . '", Date_Resolved="'.$resolvedDate.'" Where id =' . $id) === TRUE)
    {
         echo 'Record updated successfully';
    }    
    else
    {
        echo ' Failed to update record';
    }
        
    sendUpdateMail($id, $comments, $status);
    header('Location: index.php ');
    
    
}





echo '<br><a href=/"'."index.php". '/"> Back';


ob_end_flush();
?>
