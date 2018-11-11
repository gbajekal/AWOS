<?php
include_once 'debug.php';

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require './twilio-php-master/Services/Twilio.php';
use \google\appengine\api\mail\Message;

 function debugStr($message)
        {
            global $DEBUG;
            if($DEBUG)
              {
                echo $message.'<br>';
                syslog(LOG_INFO, $message);
                }
        };
        
 //************************************
 // check if user exists in database
 // if he/she does, return true else
 // false
 //*************************************
 function isUserRegistered($dbConnection)
 {
     session_start(); // start session if not already started
     debugStr("Entered isUserRegistered");
     $result = false;
     $userid = $_SERVER['USER_EMAIL'];
     debugStr("User email=". $userid);
     $user_exist = $dbConnection->query("SELECT COUNT(userid) as usercount FROM users WHERE userid=('$userid')")->fetch_object()->usercount; 
     if($user_exist)
     {
         debugStr("User has already registered");
         $_SESSION['userid'] = $userid;
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
 function displayWelcomeMessage($userName)
 {
     echo '<h1>Hello '.$userName.', welcome to Acropolis Work Order System! </h1>';
 }
       
 //********************************************
 // Display Registration Page
 //********************************************
 function displayRegistrationPage($dbConnection)
 {
     $catResult = getProperties($dbConnection);
     echo '<H1>Hi '.$_SERVER['USER_NICKNAME'].', You need to Update your Profile!</H1>';
        
        $redirect_url = $_SERVER['PHP_SELF'];
        debugStr("redirectURI = " .$redirect_url);
        echo '<form method="post" action="index.php">';
        echo '<table>';
        echo '<tr><td><b>Select Property</b></td></tr>';
        echo '<tr><td><select name="property">';
        while( $row = $catResult->fetch_assoc())
        {
                echo "<option value='".$row['id']."'>".$row['name']."</option>";
        }
        echo '</td></tr>';
        echo '<tr></tr>';
        echo '<tr></tr>';
        echo '<tr></tr>';
        echo '<tr><td><b>First Name</b></td><b><td><b>Last Name</b></td></tr>';
        echo '<tr><td><input name="fname"/></td><td><input name="lname"/></td></tr>';
        echo '<tr><td><b>Block #</b></td></tr>';
        echo '<tr><td><input name="block"/></td></tr>';
        echo '<tr><td><b>Apt #</b></td></tr>';
        echo '<tr><td><input name="apt"/></td></tr>';
        echo '<tr><td><b>Mobile # preceding with +91</b></td></tr>';
        echo '<tr><td><input name="mobile"/></td></tr>';
        echo '<tr><td></td></tr>';
        echo '<tr><td><input type="submit" value="Update"></td></tr>';
        echo '</table>';
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
          
          $fname = htmlspecialchars($_POST['fname']);
          $lname = htmlspecialchars($_POST['lname']);
          $block = htmlspecialchars($_POST['block']);
          debugStr('block='.$block);
          $user_id = $_SERVER['USER_EMAIL'];
          debugStr('userid='.$user_id);
          $apt = htmlspecialchars($_POST['apt']);
          $mobile = htmlspecialchars($_POST['mobile']);
          $findPlus = strpos($mobile, '+');
          debugStr("Mobile prepended by + = " . $findPlus);
            if ($findPlus === FALSE) {
                    $mobile = "+91" . $mobile;
             }


    $role=0;
          $propertyId = htmlspecialchars($_POST['property']);
          debugStr("Adding user to Database with values".$block .$user_id .$apt .$mobile .$email);
          try {
            $insertQuery = "INSERT INTO users (userid,Fname, LName, block, apt, mobile, role, propertyID) VALUES('$user_id', '$fname', '$lname','$block', '$apt', '$mobile', '$role', '$propertyId')";
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
 
 
 //***************************************************
 // getProperties - This method 
 function getProperties($dbConnection)
 {
      $catQuery = 'select id,name from property';
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
        echo '<tr><td><b>Category</b></td>';
        echo '<tr><td><select name="category">';
        echo ' <option value = "-1">Select a Category ....</option>';
        while( $row = $catResult->fetch_assoc())
        {
                echo "<option value='".$row['Name']."'>".$row['Name']."</option>";
        }
        echo "</select></td><tr>";
        echo "<tr><td></td></tr>";
        echo '<tr><td><b>Description - 120 characters max(<span class="error">* required field.</span>)</b></td></tr>';
        echo '<tr><td><textarea name="complaint" rows="5" cols="100">';
        echo '</textarea></td></tr>';
        echo '</td></tr>';
        
        echo  "<tr><td></td></tr>";
        echo  "<tr><td></td></tr>";
        echo '<tr><td><input type="submit" value="Register Complaint" style="height:25px; width:150px"></td></tr>';
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
     $complaintErr;
     $categoryErr;
     $complaint = htmlspecialchars($_POST['complaint']);
      if(empty($complaint))
      {
              $complaintErr = "Complaint Description is required";
      }
      else
      {
          // Limit complaint to 140 chars limit
          $complaint = substr($complaint,0, 120);
      }
      
      
      $compCategory = $_POST['category'];
      if($compCategory == -1)
      {
          $categoryErr = "Please select a valid Category";
      }
    if(empty($complaintErr) && empty($categoryErr))
    {   
      $status = 'Pending';
      $createdDate = date("d-m-Y h:i:s", time());
      $complaintResult;
      $email = $_SERVER['USER_EMAIL'];
         try {
             $complaintResult= $dbConnection->query("INSERT INTO `grievance`(`Complaint`, `Date_Created`, `Status`,  `Category`, `Submitter`)
         VALUES ('$complaint',now(),'$status','$compCategory', '$email')");
         
         } catch (Exception $ex) {
          
          echo "Error in inserting = ". $ex->getMessage();
          debugStr("Error in inserting = ". $ex->getMessage());
         }
         debugstr("email=".$email);
         debugstr("Complaint Result=".$complaintResult);
         if($complaintResult == TRUE)
         {
             //$redirectURL = filter_var($_SERVER['PHP_SELF'].'?reference='.$dbConnection->insert_id, FILTER_SANITIZE_URL);
             $redirectURL = filter_var("index.php".'?reference='.$dbConnection->insert_id, FILTER_SANITIZE_URL);
             debugStr("Redirecting Page to ".$redirectURL);
             debugStr('Exited saveComplaint()');
             header('Location: ' . $redirectURL);
             exit();
             

         }
    
         else
         {
              echo 'Failed to record Complaint. Contact Manager. Error = '.$mysqli->error;
         }
    }
 else {
            echo '<span class="error">Could not register complaint due to the following errors:-</span> <br>';
          if( !empty($complaintErr))  
            echo  '<span class="error"> - '.$complaintErr.'</span><br>';
          
          if(!empty($categoryErr))
            echo   '<span class="error"> - '.$categoryErr.'</span><br>'; 
    }
     debugStr('Exited saveComplaint()');
 }
 
 //****************************************************************
 // saveClassified - Reads the values from Classified form and 
 // stores them in the database. Returns success or error code
 //*****************************************************************
 function saveClassified($dbConnection)
 {
     debugStr("Entered saveClassified()");
     
     $saveStatus = "";
     $category  = htmlspecialchars($_POST['classifiedCategory']);
     $title = htmlspecialchars($_POST['classifiedTitle']);
     $desc = htmlspecialchars($_POST['classifiedDescription']);
     $imageURL = htmlspecialchars($_POST['classifiedImageURL']);
     $contact = htmlspecialchars($_POST['classifiedContact']);
     $email = $_SESSION['userid'];
     //$userId = getUserFromEmail($dbConnection, $user_email);
     //debugStr('userId='.$userId);
     $status = 0; // Inactive - We gate the submissions 
     
     
     //*****************************************
     // Save the classified in the Database
     //*****************************************
     
     try {
           $q ="INSERT INTO `awos`.`classified` (id, owner,description, price, imageLink, status, contact, createdDate, category, title) VALUES (NULL,'$email','$desc', NULL, '$imageURL', '$status', '$contact', now(), '$category', '$title')";
           $classificationResult = $dbConnection->query($q);
           $statusMsg = "Saved Classified. You will be informed when it is activated";
           $data = "";
           deliver_response('200', $statusMsg, $statusMsg);
           debugStr("Saved data with result".$classificationResult);
           debugStr("Exiting saveClassified()");
     
         
         } catch (Exception $ex) {
           debugStr($ex->getMessage());
           $statusMsg = "Error in saving the classified" . $ex->getMessage();
           $data = $statusMsg;
           deliver_response('400', $statusMsg, $statusMsg);
           debugStr("Exiting saveClassified() with error" . $ex->getMessage());
     
         }
     
 }
 
 //**************************************************************
 // displayWorkOrder History - This function displays the history
 // of work orders raised by this user
 //***************************************************************
 function displayWorkOrderHistory($mysqli)
 {
     $flagAllSelected = "";
     $flagPendingSelected = "";
     $flagResolvedSelected = "";
     
      debugStr("Entered displayWorkOrderHistory()");
      $email = $_SERVER['USER_EMAIL'];
      
      //***************************
      // Check if the logged in user
      // is an Admin. In which case
      // show all the tickets
      //****************************
      $isAdmin = $_SERVER['USER_IS_ADMIN'];
      
      //************************************
      //* Check filter choice. Default is
      //* pending
      //*************************************
      $filterChoice = null;
      if( isset($_POST['filter']))
        $filterChoice = htmlspecialchars ($_POST['filter']);
      
      
      if($filterChoice == null)
          $filterChoice = 'Pending';
      
      if($filterChoice == 'All')
      {
          $flagAllSelected = "selected";
      }
      if($filterChoice == 'Pending')
      {
          $flagPendingSelected = "selected";
      }
       if($filterChoice == 'Resolved')
      {
          $flagResolvedSelected = "selected";
      }
      
       debugStr("filter = $filterChoice");
      
      
      if($isAdmin == 1)
      {
          if($filterChoice == 'All')
                    $historyQuery = "Select a.*, b.block, b.apt, b.Mobile from grievance a, users b 
                          where a.Submitter=b.userid";;
          if($filterChoice == 'Pending')
                $historyQuery = "Select a.*, b.block, b.apt, b.Mobile from grievance a, users b 
                          where a.Submitter=b.userid and a.Status='Pending'";
          if($filterChoice == 'Resolved')
                $historyQuery = "Select a.*, b.block, b.apt, b.Mobile from grievance a, users b 
                          where a.Submitter=b.userid and a.Status='Resolved'";
                                                                                
      }
 else {
       if($filterChoice == 'All')
          $historyQuery = "Select * from grievance where Submitter='$email'";
         if($filterChoice == 'Pending') 
             $historyQuery = "Select * from grievance where Submitter='$email'and Status='Pending'";
         if($filterChoice == 'Resolved') 
             $historyQuery = "Select * from grievance where Submitter='$email'and Status='Resolved'";
      }
      
      $historyResult = $mysqli->query($historyQuery); 
    echo '<form method=post action=index.php>';
     echo '<table>';  
     echo '<tr>';
     echo '<td><h1>Your Work Order History</h1></td>';
     echo'</tr>';
     
     
     
     
     echo '<tr>';
     echo '<td><b>Complaint Filter</b>';
     
    
     
     
     echo '<Select name ="filter" onChange = "form.submit();"><option value="All" '.$flagAllSelected.'>All</option><option value="Pending" ' .$flagPendingSelected .'>Pending</option>'
     . '   <option value="Resolved" '.$flagResolvedSelected.'>Resolved</option></select></td>';
   
     echo'</tr>';
   
      echo '</form>';
      echo '<tr><td></td></tr>';
      echo '<tr><td></td></tr>';
      echo '<tr><td></td></tr>';
     echo '</table>';
     
        
        echo '<table name="history" border="3">';
        if($isAdmin == 1)
        {
             echo '<tr class="row2"><th>ID</th><th>Date Submitted</th><th>Complaint</th><th>Category</th><th>Block</th><th>Apt</th><th>Mobile</th><th>Status</th><th>Date Resolved</th><th>Comments</th><th>Rating</th></tr>';
        }
    else {
        echo "<tr><th>ID</th><th>Date Submitted</th><th>Complaint</th><th>Category</th><th>Status</th><th>Date Resolved</th><th>Comments</th><th>Rating</th></tr>";
         }
        while($historyrow = $historyResult->fetch_assoc())
        {
            echo "<tr>";
            echo "<td><a href=case.php?case=".$historyrow['id']. ">".$historyrow['id']."</td>";
            echo "<td>".$historyrow['Date_Created']."</td>";
            echo "<td>".$historyrow['Complaint']."</td>";
            echo "<td>".$historyrow['Category']."</td>";
              if($isAdmin == 1)
              {
                  echo "<td>".$historyrow['block']."</td>";
                  echo "<td>".$historyrow['apt']."</td>";
                  echo "<td>".$historyrow['Mobile']."</td>";
                  
              }
            echo "<td>".$historyrow['Status']."</td>";
            echo "<td>".$historyrow['Date_Resolved']."</td>";
            echo "<td>".$historyrow['Comments']."</td>";
            if($historyrow['rating'] == 0)
            	echo "<td>Not Rated</td>";
            else
            	echo "<td>".$historyrow['rating']."</td>";
            
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
     $case = $caseResult->fetch_assoc() or die("Cannot fetch case");    
     debugStr("Exiting  getCase()");
     return $case;
     
 }
 
 //***********************************
 // getUser() - Fetches the user having
 // a certain email
 //***************************************
 function getUser($dbConnection, $case)
 {
      debugStr("Entering getUser");
      $email = $case["Submitter"];
      $userQuery = "Select * from users where userid='$email'";
      $userResult = $dbConnection->query($userQuery);
      $user = $userResult->fetch_assoc(); 
      
    
      
      if($user == null)
          debugStr("User with email".$email ."not found!!!");
      else {
            debugStr("User with id=".$user["id"]. "retrieved"); 
            
            // Check Role and if user is a super Admin
            
      }
      
      
       debugStr("Exiting getUser");
      return $user;
      
      
     
 }
 
 /******************************************************
 * getUserFromEmail() - Gets user from Email ID
 *
 *
 ********************************************************/
 
 function getUserFromEmail($mysqli, $email)
 {
      debugStr("Entering getUser with email =" .$email);
     
     
     $userQuery = "Select * from users where userid='$email'";
   
     
      if ($mysqli->connect_error) {
  			 echo "Not connected, error: " . $mysqli->connect_error;
  			 }

      $userResult = $mysqli->query( $userQuery);
      if(!$userResult)
      {
        die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
      
      }
      else
      {
      
      $user = $userResult->fetch_assoc(); 
      }
      
    
      
      if($user == null)
          debugStr("User with email".$email ."not found!!!");
      else {
            debugStr("User with id=".$user["id"]. "retrieved"); 
            
            // Check Role and if user is a super Admin
            
      }
      
      
       debugStr("Exiting getUser");
      return $user;
      
      
     
 }
 
 
 
 //***********************************
 // getUser() - Fetches the user having
 // a certain email
 //***************************************
 function getCategory($dbConnection, $case)
 {
      debugStr("Entering getCategory");
      $category = $case["Category"];
      debugStr("Category=".$category);
      $categoryQuery = "Select * from categories where Name='$category'";
      $categoryResult = $dbConnection->query($categoryQuery);
      $categoryObj = $categoryResult->fetch_assoc();
      debugStr("Exiting getCategory");
      return $categoryObj;
      
      
     
 }
 
 //****************************************************
 // getClassifiedListing() - Gets the Classified Listing
 // from the database and converts to JSON before
 // sending as response
 //******************************************************
   function getClassifiedListing($dbConnection)
   {
       debugStr("Entered getClassifiedListing");
       
     $queryResult = $dbConnection->query("SELECT category, title, description, imageLink, contact, createdDate FROM classified") ;
        if(!$queryResult)
        {
            debugStr("Could not run query ");
        }
        
        $result = null; 
        
        if($queryResult)
        {
             $result = array();
         
         while($item = $queryResult->fetch_assoc())
         {
             $result[] = $item;
         }
        }
       debugStr("Exited getClassifiedListing");
       
        return json_encode($result);
       
       
       
                    
       
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
     
     $displayRating = 0;
     
         
             
     echo '<br>';
     echo '<b>Status</b><br>';
     echo '<select name=status>';
     if($status == "Pending")
     {
        echo '<option value="Pending" selected>Pending</option>';
        echo '<option value="Resolved">Resolved</option>';
        $displayRating = 1;
     }
 else {
        echo '<option value="Pending">Pending</option>';
        echo '<option value="Resolved" selected>Resolved</option>';
          
      
     }
     echo '</select>';
     echo '<br>';
     echo '<br>';
     
     if($displayRating == 1)
     {
      echo "Please rate your service according to the following scale";
      echo '<br>';
      echo '<br>';
    
      echo '<input type="radio" name="rating" value="5"><b>5-Exceptional</b>';
      echo '<input type="radio" name="rating" value="4"><b>4-Good</b>';
      echo '<input type="radio" name="rating" value="3"><b>3-Average</b>';
      echo '<input type="radio" name="rating" value="2"><b>2-Bad</b>';
      echo '<input type="radio" name="rating" value="1"><b>1-Terrible</b>';
       
      echo '<br>';
      echo '<br>';
     
     }
     
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
 
 //*************************************
 // function: Send SMS will make use of
 // Twilio libraries to send an SMS to 
 // the incident owner and the category
 // contact
 //***************************************
 
 function sendSMS($case, $category, $user)
 {
      
     debugStr("Entering SendSMS");
    
      $complaint = $case["Complaint"];
       //echo "Complaint=".$complaint;
       
       $id = $case["id"];
      
      $block = $user["block"];
      $apt   = $user["apt"];
      $mobile = $user["mobile"];
      $contact = $category["Contact"];
      $categoryName = $category["Name"];
      debugStr("Block= ".$block . "Apt=".$apt);
      debugStr( "Complaint=".$complaint);
      debugStr( "Contact=". $contact);
      
      $msg = "Complaint with id = ".$id . 
              " registered for Block ".$block." Apt ". $apt."Mobile: " .$mobile ."\n Category: ".$categoryName
              ." Details: ".$complaint;
     
       // Step 2: set our AccountSid and AuthToken from www.twilio.com/user/account
         $AccountSid = "AC3f17844783ac7a0ffa84195f15269f8f";
         $AuthToken = "7e575660c721dd599ef3116baa6ed9b0";
 
    // Step 3: instantiate a new Twilio Rest Client
    $client = new Services_Twilio($AccountSid, $AuthToken);
 
    // Step 4: make an array of people we know, to send them a message. 
    // Feel free to change/add your own phone number and name here.
    $people = array(
        $contact => "Handyman",
        $mobile => "Resident",
        "+919676182176" => "Manager"
           
    );
    
    
 
    // Step 5: Loop over all our friends. $number is a phone number above, and 
    // $name is the name next to it

    try {
        foreach ($people as $number => $name) {
 
        $sms = $client->account->messages->sendMessage(
 
        // Step 6: Change the 'From' number below to be a valid Twilio number 
        // that you've purchased, or the (deprecated) Sandbox number
            "+15125807238", 
 
            // the number we are sending to - Any phone number
            $number,
 
            // the sms body
            $msg
        );
        }
 
    } catch (Exception $ex) {
        //Do nothing on exeption
        die("Error! Cannot send SMS to number" .$number); 
        debugStr("Problems sending SMS");
    }
    
        // Display a confirmation message on the screen
        debugStr("Sent message to $name");
    
      
      
      
      
     debugStr("Exiting SendSMS");
     
     
     
     
 }
 //***************************************************
 // This function delivers a JSON response to the 
 // HTTP Request
 // It expects the status, message and data to be
 // returned to the user
 //****************************************************
 
 function deliver_response($status, $statusMsg, $data)
{
    debugStr("Entered deliverResponse()");
    header("HTTP/1/1 $status $statusMsg");
    header("Content-Type:application/json");
    $_RESPONSE["status"] = $status;
    $_RESPONSE["statusMsg"] = $statusMsg;
    $_RESPONSE["data"] = $data;
    $json_response = json_encode($_RESPONSE);
    echo $json_response;
    debugStr("Exited deliverResponse()");
}