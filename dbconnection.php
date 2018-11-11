<?php
include_once 'debug.php';
require_once "functions.php";
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 //***********************************
 // Database settings
 //***********************************
        $db_username="root";
        $db_password="adminH2o";
        //$db_password="";
        if($DEBUG)
           {
        	$hostname = "localhost";
        	}
        else
           {
            $hostname = '/cloudsql/awos-beta:awos';
            }
            
        $dbName = "awos";
       

/* connect to database using mysqli */
   debugStr("Connecting to database");
   if($DEBUG)
   		$mysqli = new mysqli($hostname, $db_username, $db_password, $dbName, null, null);
   	else
    	$mysqli = new mysqli(null, $db_username, $db_password, $dbName,null, $hostname);
    
    if ($mysqli->connect_error) {
        die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }
   debugStr("Connected to database");
   
   ?>