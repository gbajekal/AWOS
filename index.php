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
include_once 'dbconnection.php';


# Looks for current Google account session
$user = UserService::getCurrentUser();
  
        
       

if ($user) {
  
  $logout = UserService::createLogoutURL($_SERVER['REQUEST_URI']);
   echo "<a href=\"".$logout."\">Logout</a><br>";
  
   displayWelcomeMessage($user->getNickname());
   
  
  //******************************************
  // Check if the user has already registered
  // into awos. If not register the user and
  // return to the page
  //*****************************************
  
   //************************
   // Store the connection
   // in session
   //*************************
   
  // $_SESSION['DB_CONNECT'] = $mysqli;
   
   $userRegistered = isUserRegistered($mysqli);
 
   
   //***********************************************
   // If the user has not registered, then show
   // the user a registration page to update their
   // Profile. 
   //************************************************
   if( !$userRegistered)
   { 
       // If Callback then add the user
            if( isset($_POST['block']))
          {
              debugStr("handling Post");
              handleRegistration($mysqli);
             
          }
       else
       {
        // display registration page
       displayRegistrationPage();
       }
       
       
   }
 else {
       // display the Ticket Form for user
     
       if(isset($_POST['complaint']))
       {
           debugStr("Saving the complaint");
           saveComplaint($mysqli);
       }
     
       if(isset($_GET['reference']))
       {
          $ref = htmlspecialchars($_GET['reference']);
           echo 'Thank you for submission. Your reference number is '.$ref;
           sendMail($ref);
       }
       
       displayComplaintForm($mysqli);
       displayWorkOrderHistory($mysqli);
       
      
   }
   
 
  
  
}
else {
  header('Location: ' . UserService::createLoginURL($_SERVER['REQUEST_URI']));
}

ob_end_flush();
?>