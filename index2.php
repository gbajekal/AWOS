<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        // put your code here
        session_start();
        $DEBUG = TRUE;
        
      
       
        
        function debugStr($message)
        {
            global $DEBUG;
            if($DEBUG)
                echo $message.'<br>';
        }
  
        use google\appengine\api\users\User;
        use google\appengine\api\users\UserService;
        
         
        
        
        //***********************************
        // Database settings
        //***********************************
        $db_username="root";
        $db_password="";
        $hostname = "localhost";
        $dbName = "awos";
        $usr = UserService::getCurrentUser();
       
        
        
        
 
         //******************************
         // Check if a user is logged in
         // or else take the user to login
        //********************************
        if( $usr )
        {
            echo 'Hello, ' .htmlspecialchars($usr->getNickName());
            $logout = UserService::createLogoutURL($_SERVER['REQUEST_URI']);
            echo "<a href=\"".$logout."\">Logout</a>";
        }
        else
        {
            header('Location: ' . UserService::createLoginURL($_SERVER['REQUEST_URI']));
        }
        
        
        //*****************************
        // This function logs out from
        // a google session
        //******************************
        function MyLogout()
        {
        
        exit();
     
        };

//$google_oauthV2 = new Google_Oauth2Service($gClient);
    //debug
  
  
 

//If user wish to log out, we just unset Session variable
if (isset($_GET['reset'])) 
{
  MyLogout();
}





/*
if( !$gClient->isAccessTokenExpired())
{
if ($gClient->getAccessToken()) 
{
    try {
        
     
    debugStr("Requesting user Info");
     
    $user = $gplus->people->get("me");
      debugStr("Got user Info");
      
   
      $user_name = filter_var($user->displayName, FILTER_SANITIZE_SPECIAL_CHARS);
      debugStr("User Name=" .$user_name .'<br>');
      
      foreach($user->emails as $em) {
        if($em->type == "account") {
        $email = $em->value;
        }
      }
      
      $email  = filter_var($email, FILTER_SANITIZE_EMAIL);
      debugStr("User email=" .$email .'<br>');
      
      
      $user_id = filter_var($user['id'], FILTER_SANITIZE_SPECIAL_CHARS);
      debugStr("User ID=" .$user_id .'<br>');
      
      $_SESSION['token']    = $gClient->getAccessToken();
    }
    catch(Exception $e)
    {
        MyLogout();
    }
       
}
}
else 
{
    //For Guest user, get google login url
    $authUrl = $gClient->createAuthUrl();
}
*/
//HTML page start
echo '<!DOCTYPE HTML><html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<title>Login with Google</title>';
echo '</head>';
echo '<body>';

        
// echo '<h1>Login with Google</h1>';

/*
if(isset($authUrl)) //user is not logged in, show login button
{
   echo '<h1>Welcome to Acropolis Work Order System (AWOS) </H1>';
   echo 'AWOS is a work order system to log in your grievances <br>'; 
   echo '<a class="login" href="'.$authUrl.'"><img src="../gplus-quickstart-php/signin_button.png" /></a>';
} 
else // user logged in 
{
   //connect to database using mysqli 
   debugStr("Connecting to database");
    $mysqli = new mysqli($hostname, $db_username, $db_password, $dbName);
    
    if ($mysqli->connect_error) {
        die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }
    
    //compare user id in our database
    $user_exist = $mysqli->query("SELECT COUNT(userid) as usercount FROM users WHERE userid=('$email')")->fetch_object()->usercount; 
    if($user_exist)
    {
        debugStr("User exists in Database");
        $catQuery = 'select Name from categories';
        $catResult = $mysqli->query($catQuery);
        $historyQuery = "Select * from grievance where Submitter='$email'";
        $historyResult = $mysqli->query($historyQuery);
        
        
       
       
        echo '<br><a class="logout" href="?reset=1">Logout</a><br>';
        echo 'Welcome back '.$user_name.'! Please enter your Work Order<br><br>';
       if( isset($_GET['reference']))
        {
            echo 'Your complaint is registered.Reference ID='.htmlspecialchars($_GET['reference']);
        }
        echo '<form method="post" action='.$google_redirect_url.'>';
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
        echo '<h1>Your Work Order History</h1>';
        
        echo '<table name="history" border="3">';
        echo "<tr><th>ID</th><th>Date Submitted</th><th>Complaint</th><th>Category</th><th>Status</th><th>Date Resolved</th><th>Comments</th></tr>";
        while($historyrow = $historyResult->fetch_assoc())
        {
            echo "<tr>";
            echo "<td>".$historyrow['id']."</td>";
            echo "<td>".$historyrow['Date_Created']."</td>";
            echo "<td>".$historyrow['Complaint']."</td>";
            echo "<td>".$historyrow['Category']."</td>";
            echo "<td>".$historyrow['Status']."</td>";
            echo "<td>".$historyrow['Date_Resolved']."</td>";
            echo "<td>".$historyrow['Comments']."</td>";
        }   echo "</tr>";
        echo "</table>";

      //*************************************************
     // If user has submitted a complaint store it 
     // and refresh the page
     //***************************************************
     if(isset ($_POST['complaint']))
     {
         $complaint = htmlspecialchars($_POST['complaint']);
         $compCategory = $_POST['category'];
         $status = 'Pending';
         $createdDate = date("d-m-Y h:i:s", time());
         $complaintResult;
         try {
             $complaintResult= $mysqli->query("INSERT INTO `grievance`(`Complaint`, `Date_Created`, `Status`,  `Category`, `Submitter`)
         VALUES ('$complaint',now(),'$status','$compCategory', '$email')");
         
         } catch (Exception $ex) {
          
          echo "Error in inserting = ". $ex->getMessage();
         }
         debugstr("email=".$email);
         debugstr("Complaint Result=".$complaintResult);
         if($complaintResult === TRUE)
         {
             
             header('Location: ' . filter_var($google_redirect_url.'?reference='.$mysqli->insert_id, FILTER_SANITIZE_URL));
             return; // refresh page

         }
         else
        
              echo 'Failed to record Complaint. Contact Manager. Error = '.$mysqli->error;
         }
         
         
     }
        
        
        
        
        
        
        
    else{ 
        //user is new
        if(isset($_POST['block']))
        {
            debugStr("Reading form Info");
          $block = htmlspecialchars($_POST['block']);
          debugStr('block='.$block);
          debugStr('userid='.$user_id);
          $apt = htmlspecialchars($_POST['apt']);
          $mobile = htmlspecialchars($_POST['mobile']);
          $role=0;
          debugStr("Adding user to Database with values".$block .$user_id .$apt .$mobile .$email);
          try {
               $insertQuery = "INSERT INTO users (userid, block, apt, mobile, role) VALUES('$email', '$block', '$apt', '$mobile', '$role')";
            $InsertResult =  $mysqli->query($insertQuery);
            
          
          if($InsertResult === TRUE)
          {
              debugStr("User Added");
             
          }
          
       else {
               echo "Error: " . $insertQuery . "<br>" . $mysqli->error;
             
              return;
            }
          
          
          } catch (Exception $ex) {
          
          echo "Error adding user ". $ex->getMessage();
          }
          
          
          header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
        
        
        
            
           
        }
        
       else
       {
        echo '<H1>Hi '.$user_name.', You need to Update your Profile!</H1>';
        
        echo '<form method="post" action='. $google_redirect_url.'>';
        echo '<b>Block #</b><br>';
        echo '<input name="block"/><br>';
        echo '<b>Apt #</b> <br>';
        echo '<input name="apt"/><br>';
        echo '<b>Mobile #</b> <br>';
        echo '<input name="mobile"/><br><br>';
        echo '<input type="submit" value="Update">';
        echo '</form>';
        }
       
    }

    
   // echo '<br /><a href="'.$profile_url.'" target="_blank"><img src="'.$profile_image_url.'?sz=100" /></a>';
   
    
    //list all user details
    //echo '<pre>'; 
    //print_r($user);
   //echo '</pre>';  
}
*/
 
echo '</body></html>';


?>
        
       
        
        
        
        
        
        
        
