<?php

use google\appengine\api\users\User;
use google\appengine\api\users\UserService;
use \google\appengine\api\mail\Message;
$user = UserService::getCurrentUser();
if($user == null)
 { 
   
   header('Location: ' . UserService::createLoginURL($_SERVER['REQUEST_URI']));
   ob_end_flush();
   exit;
   
   
 }
?>

<!DOCTYPE html>
<html>
<head>
<style>
ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

li {
    display: inline;
}

.row1 { color: red }
.row2 { color: blue }
body {
    background-color:beige;
}

img {
    position: absolute;
    left: 50%;
    margin-left: -25px;
    }
</style>
</head>
<body>

<ul>
  <li><a href="index.php">Home</a></li>
  <li><a href="profile.php">My Profile</a></li>
  <li><a href="classifiedListing.php">Classifieds</a></li>
  <li><a href="pdf/ratecard.pdf" target="_blank">Work Ratecard</a></li>
  <li><a href="about.php">About</a></li>
  <li>
   <?php
   
 
   if($user)
   {
   $logout = UserService::createLogoutURL($_SERVER['REQUEST_URI']);
   echo "<a href=\"".$logout."\">Logout</a>";
   }
   else {
   header('Location: ' . UserService::createLoginURL($_SERVER['REQUEST_URI']));
   }
  
  ?>
  </li>
  
 </ul>
 <br>