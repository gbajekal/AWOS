<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 //***********************************
        // Database settings
        //***********************************
        $db_username="root";
        $db_password="admin";
        //$db_password="";
        $hostname = "localhost";
        $dbName = "awos";
       

/* connect to database using mysqli */
   debugStr("Connecting to database");
   //$mysqli = new mysqli($hostname, $db_username, $db_password, $dbName);
    $mysqli = new mysqli(null, $db_username, $db_password, $dbName,null, '/cloudsql/awos-beta:awos');
    
    if ($mysqli->connect_error) {
        die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }
   debugStr("Connected to database");
   