<?php
ob_start();
require_once 'functions.php';
include_once 'dbconnection.php';
use google\appengine\api\users\User;
use google\appengine\api\users\UserService;
use \google\appengine\api\mail\Message;
$user = UserService::getCurrentUser();
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



# Looks for current Google account session
$user = UserService::getCurrentUser();
  
 //displayRegistrationPage($mysqli);   
       

if ($user) {

    
    
    
  include 'header.php';
 // $logout = UserService::createLogoutURL($_SERVER['REQUEST_URI']);
  // echo "<a href=\"".$logout."\">Logout</a><br>";
  echo '<img src="img/smr.jpg" align="middle" alt="SMR Logo" style="width:50px;height:50px;">';
  echo "<br>";
  echo "<style>.error {color: #FF0000;}</style>";
  
    displayWelcomeMessage($user->getNickname());
   //displayWelcomeMessage($user->getName());
  
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
       
       displayRegistrationPage($mysqli);
       }
       
       
   }
 else {
       if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST))
            $_POST = json_decode(file_get_contents('php://input'), true);
        
       
       if(isset($_POST['complaint']))
       {
           debugStr("Saving the complaint");
           saveComplaint($mysqli);
       }
     
       //*********************************
       // User has entered a classifieds
       // so save it to the database
       //*********************************
       if(isset($_POST['classifiedTitle']))
       {
           debugStr("Saving classified");
           saveClassified($mysqli);
           header('Location: '."classifiedListing.php");
           exit();
       }
       
     if(isset($_GET['reference']))
       {
          $ref = htmlspecialchars($_GET['reference']);
           echo 'Thank you for submission. Your reference number is '.$ref;
           //sendMail($ref);
           $case = getCase($mysqli,$ref);
           $user = getUser($mysqli, $case);
           $category = getCategory($mysqli, $case);
           sendSMS($case, $category, $user);
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

</body>
</html>
