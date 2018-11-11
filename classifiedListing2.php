
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
<?php
session_start();
require_once 'functions.php';
include 'header.php';
include 'debug.php';
include_once 'dbconnection.php';

//**************************************
// Get the Classified items from Database
 //****************************************
   debugStr("gettng classified Listing from db");
   $jsonData     = getClassifiedListing($mysqli);
  
 

?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<html ng-app="classifiedListing">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <body ng-ctrl="classifiedListCtrl">
        <H1>SMR Acropolis Classified Listing (Beta)</h1>
        <div>
            <a href="./classifiedForm.php" target="_blank"><h2>+ Add a Classified</h2></a><br><br>
        </div>
        <div>
            <table border="1">
                <tr><th>Category</th><th>Title</th><th>Description</th><th>Image Link</th><th>Contact</th></tr>
              
                <?php
                  if($listModel != NULL)  
                    foreach($listModel as $item )
                    {
                        echo "<tr>";
                             switch($item["category"])
                             {
                                 case "1":
                                     echo "<td>"."Apartment for Rent"."</td>";
                                 break;
                                case "2":
                                     echo "<td>"."Apartment for Sale"."</td>";
                                 break;
                                case "3":
                                     echo "<td>"."Electronics"."</td>";
                                 break;
                                case "4":
                                     echo "<td>"."Furniture"."</td>";
                                 break;
                                 case "5":
                                     echo "<td>"."Books"."</td>";
                                 break;
                                case "6":
                                     echo "<td>"."Apparel"."</td>";
                                 break;
                                 case "7":
                                     echo "<td>"."Automobile"."</td>";
                                 break;
                                 case "8":
                                     echo "<td>"."Other"."</td>";
                                 break;
                                 default:
                                      echo "<td>"."Other"."</td>";
                                     
                             }// end switch
                            
                            
                            echo "<td>".$item["title"]."</td>";
                            echo "<td>".$item["description"]."</td>";
                            echo '<td><a href="'.$item["imageLink"].'">View Image</a></td>';
                            echo "<td>".$item["contact"]."</td>";
                        echo "</tr>";
                    }
                
                else {
                       // Empty array so display message to user
                    echo ("<h2>No Classifieds found</h2>");
                }
                
                ?>
                
              
                 
                
                
            </table>
            
            
            
            
            
        </div> 
        
        <!--****************************************************
         Controller for the above view
        *****************************************************-->
        <script> 
            var $app = angular.module("classifiedListing", []);
            $app.controller("classifiedListCtrl", function($scope)
            {
                $scope.$listData = "<?php echo $jsonData ?>";
                console.log($scope.$listData);
                
                
                
                
                
            }
            
            
            
            
            );
            
            
          
                
                
                
                
                
             
            
            
            
        </script>
        
    </body>
</html>
