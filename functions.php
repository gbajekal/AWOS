<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use \google\appengine\api\mail\Message;
$DEBUG = FALSE;
 function debugStr($message)
        {
            global $DEBUG;
            if($DEBUG)
                echo $message.'<br>';
        };
        
 //************************************
 // check if user exists in database
 // if he/she does, return true else
 // false
 //*************************************
 function isUserRegistered($dbConnection)
 {
     debugStr("Entered isUserRegistered");
     $result = false;
     $userid = $_SERVER['USER_EMAIL'];
     debugStr("User email=". $userid);
     $user_exist = $dbConnection->query("SELECT COUNT(userid) as usercount FROM users WHERE userid=('$userid')")->fetch_object()->usercount; 
     if($user_exist)
     {
         debugStr("User has already registered");
         $result = true;
     }
 else {
        debugStr("User has not registered");    
     }
   
      debugStr("Exited isUserRegistered");
        return $result;
     
     
 }
 
 
 //*******************************************
 // Display Welcome Message - Shows the Welcome
 // Message if a User has logged in
 //********************************************
 function displayWelcomeMessage($user)
 {
     echo '<h1>Hello '.$user.', welcome to Acropolis Work Order System! </h1>';
 }
       
 //********************************************
 // Display Registration Page
 //********************************************
 function displayRegistrationPage()
 {
     echo '<H1>Hi '.$_SERVER['USER_NICKNAME'].', You need to Update your Profile!</H1>';
        
        $redirect_url = $_SERVER['PHP_SELF'];
        debugStr("redirectURI = " .$redirect_url);
        echo '<form method="post" action="index.php">';
        echo '<b>Block #</b><br>';
        echo '<input name="block"/><br>';
        echo '<b>Apt #</b> <br>';
        echo '<input name="apt"/><br>';
        echo '<b>Mobile #</b> <br>';
        echo '<input name="mobile"/><br><br>';
        echo '<input type="submit" value="Update">';
        echo '</form>';
 }
     
 //***********************************************************
 // handleRegistration() - Handles the registration by
 // retrieving the Profile values from the Registration form
 // and storing them in the database
 //***********************************************************
 function handleRegistration($dbConnection)
 {
     
          debugStr("Entering handleRegistration()");
          debugStr("Reading form Info");
          $block = htmlspecialchars($_POST['block']);
          debugStr('block='.$block);
          $user_id = $_SERVER['USER_EMAIL'];
          debugStr('userid='.$user_id);
          $apt = htmlspecialchars($_POST['apt']);
          $mobile = htmlspecialchars($_POST['mobile']);
          $role=0;
          debugStr("Adding user to Database with values".$block .$user_id .$apt .$mobile .$email);
          try {
            $insertQuery = "INSERT INTO users (userid, block, apt, mobile, role) VALUES('$user_id', '$block', '$apt', '$mobile', '$role')";
            $InsertResult =  $dbConnection->query($insertQuery);
            
          
          if($InsertResult === TRUE)
          {
              debugStr("User Profile Updated");
             
          }
          
       else {
               echo "Error: " . $insertQuery . "<br>" . $dbConnection->error;
             
              return;
            }
              } catch (Exception $ex) {
          
          echo "Error adding user ". $ex->getMessage();
          }
          debugStr("Exiting handleRegistration()");
          header('Location:'.$_SERVER['PHP_SELF']);
 }
 
 //***************************************************
 // getCategories() - This method 
 function getCategories($dbConnection)
 {
      $catQuery = 'select Name from categories';
      $catResult = $dbConnection->query($catQuery);
      return $catResult;
 }
 
 //*********************************************************************
 // display Complaint Form - Displays the Complaint form used to submit
 // a complaint
 //*********************************************************************
 function displayComplaintForm($dbConnection)
 {
     $catResult = getCategories($dbConnection);
     
        echo '<form method="post" action='.$_SERVER['PHP_SELF'].'>';
        echo '<h3> Please enter your complaint</h3>';
        echo '<table>';
        echo '<tr><b>Description</b></tr>';
        echo '<tr><td><textarea name="complaint" rows="5" cols="100">';
        echo '</textarea></td></tr>';
        echo '</td></tr>';
        echo '<tr><td><b>Category</b></td>';
        echo '<tr><td><select name="category">';
        while( $row = $catResult->fetch_assoc())
        {
                echo "<option value='".$row['Name']."'>".$row['Name']."</option>";
        }
        echo "</select></td><tr>";
        echo  "<tr><td></td></tr>";
        echo  "<tr><td></td></tr>";
        echo "<tr><td><input type='submit' value='Submit'></td></tr>";
        echo '</table>';
        echo '</form>';
 }
 
 //****************************************************************
 // saveComplaint - Reads the complaint form and stores it in the
 // database. It then returns the reference id of the complaint
 //*****************************************************************
 function saveComplaint($dbConnection)
 {
 debugStr('Entered saveComplaint()');
     $complaint = htmlspecialchars($_POST['complaint']);
      $compCategory = $_POST['category'];
      $status = 'Pending';
      $createdDate = date("d-m-Y h:i:s", time());
      $complaintResult;
      $email = $_SERVER['USER_EMAIL'];
         try {
             $complaintResult= $dbConnection->query("INSERT INTO `grievance`(`Complaint`, `Date_Created`, `Status`,  `Category`, `Submitter`)
         VALUES ('$complaint',now(),'$status','$compCategory', '$email')");
         
         } catch (Exception $ex) {
          
          echo "Error in inserting = ". $ex->getMessage();
         }
         debugstr("email=".$email);
         debugstr("Complaint Result=".$complaintResult);
         if($complaintResult === TRUE)
         {
             
             header('Location: ' . filter_var($_SERVER['PHP_SELF'].'?reference='.$dbConnection->insert_id, FILTER_SANITIZE_URL));
             return; // refresh page

         }
         else
         {
              echo 'Failed to record Complaint. Contact Manager. Error = '.$mysqli->error;
         }
     debugStr('Exited saveComplaint()');
 }
 
 //**************************************************************
 // displayWorkOrder History - This function displays the history
 // of work orders raised by this user
 //***************************************************************
 function displayWorkOrderHistory($mysqli)
 {
      debugStr("Entered displayWorkOrderHistory()");
      $email = $_SERVER['USER_EMAIL'];
      
      //***************************
      // Check if the logged in user
      // is an Admin. In which case
      // show all the tickets
      //****************************
      $isAdmin = $_SERVER['USER_IS_ADMIN'];
      
      if($isAdmin == 1)
      {
          $historyQuery = 'Select * from grievance';
      }
 else {
          $historyQuery = "Select * from grievance where Submitter='$email'";
      }
      
      $historyResult = $mysqli->query($historyQuery); 
     echo '<h1>Your Work Order History</h1>';
        
        echo '<table name="history" border="3">';
        echo "<tr><th>ID</th><th>Date Submitted</th><th>Complaint</th><th>Category</th><th>Status</th><th>Date Resolved</th><th>Comments</th></tr>";
        while($historyrow = $historyResult->fetch_assoc())
        {
            echo "<tr>";
            echo "<td><a href=case.php?case=".$historyrow['id']. ">".$historyrow['id']."</td>";
            echo "<td>".$historyrow['Date_Created']."</td>";
            echo "<td>".$historyrow['Complaint']."</td>";
            echo "<td>".$historyrow['Category']."</td>";
            echo "<td>".$historyrow['Status']."</td>";
            echo "<td>".$historyrow['Date_Resolved']."</td>";
            echo "<td>".$historyrow['Comments']."</td>";
        }   echo "</tr>";
        echo "</table>";
        debugStr("Exited displayWorkOrderHistory()");
 }
 
 //********************************************************************
 // Send eMail - Send email to acknowledge the complaint
 //********************************************************************
 function sendMail($refid)
 {
  try
    {
        debugStr("Entering sendMail()");
        $message = new Message();
        $message->setSender("gautam_bajekal@hotmail.com");
        $recipient = $_SERVER['USER_EMAIL'];
        debugStr("Recipient=".$recipient);
        $message->addTo($recipient);
        $message->setSubject("Acropolis Grievance Registered");
        $message->setTextBody('Thank you for registering your grievance. Your grievance id ='.$refid .'\n'.
                              'We will resolve your issue within 24 hours. If the issue is not resolved, please contact the Property Manager'
        
                             );
        //$message->addAttachment('image.jpg', $image_data, $image_content_id);
        $message->send();
    } catch (InvalidArgumentException $e) {
  // ...
    }   
     
     debugStr("Exiting sendMail()");
     
     
 }
 
 //************************************************
 // getCase() - Fetches a particular case from
 // the database
 //************************************************
 function getCase($dbConnection, $caseid)
 {
     debugStr("Entering getCase()");
     
     $caseQuery = "Select * from grievance where id='$caseid'";
     $caseResult = $dbConnection->query($caseQuery);
     $case = $caseResult->fetch_assoc();    
     debugStr("Exiting  getCase()");
     return $case;
 }
 
 //***************************************************
 // displayCase - Displays the case properties
 //***************************************************
 function displayCase($case)
 {
     echo "<form method=\"Post\" action=\"case.php\">";
     echo '<table border="3">';
     echo '<tr><td>Case ID</td><td>'.$case['id'].'</td>';
     echo '<tr><td>Description</td><td>'.$case['Complaint'].'</td>';
     echo '<tr><td>Category</td><td>'.$case['Category'].'</td>';
     echo '<tr><td>Date Submitted</td><td>'.$case['Date_Created'].'</td>';
     echo '</table>';
      echo '<br>';
     echo '<b>Comments</b><br>';
     echo '<br>';
     echo '<input type=textbox name=comments><br>';
     $status = $case['Status'];
     debugStr("Status=". $status);
     
         
             
     echo '<br>';
     echo '<b>Status</b><br>';
     echo '<select name=status>';
     if($status == "Pending")
     {
        echo '<option value="Pending" selected>Pending</option>';
        echo '<option value="Resolved">Resolved</option>';
     }
 else {
        echo '<option value="Pending">Pending</option>';
        echo '<option value="Resolved" selected>Resolved</option>';
      
     }
     echo '</select>';
     echo '<br>';
     echo '<br>';
     echo '<input type="hidden" name="id" value="'.$case['id'].'">';
     echo '<input type="submit" value="Update">';
     echo '<br>';

     
     

 }
 
 function sendUpdateMail($refid, $text, $status)
 {
  try
    {
        debugStr("Entering sendUpdateMail()");
        $message = new Message();
        $message->setSender("gautam_bajekal@hotmail.com");
        $recipient = $_SERVER['USER_EMAIL'];
        debugStr("Recipient=".$recipient);
        $message->addTo($recipient);
        $message->setSubject("Acropolis Grievance Updated");
        $message->setTextBody('Your grievance id ='.$refid .' is updated as follows \n'.
                              'Comments: '. $text.' \n Status='.$status.
                              '\n If you need further assistance, contact property manager'
        
                             );
        //$message->addAttachment('image.jpg', $image_data, $image_content_id);
        $message->send();
    } catch (InvalidArgumentException $e) {
  // ...
    }   
     
     debugStr("Exiting sendUpdateMail()");
     
     
 }