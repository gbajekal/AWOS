

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
<?php

include_once 'header.php';
include_once 'debug.php';


?>



<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<html ng-app = "classified">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <body>
        <H1>Please enter Classified Details</h1>
        <form ng-submit="submit()" name="form" ng-controller="MyCtrl">
  <div>
      <table>
          <tr>
              <td>Category:</td>
              <td><select name="category" ng-model="classified.category"  ng-change="categoryChange()"  required >
                      <option value="0"/> Please select a Category ....</option>
                      <option value="Apartment for Rent"/>Apartment for Rent</option>
                      <option value="Apartment for Sale"/>Apartment for Sale</option>
                      <option value="Clothing and Apparel"/>Clothing & Apparel</option>
                      <option value="Automobile"/>Automobile</option>
                      <option value="Books"/>Books</option>
                      <option value="Electronics"/>Electronics</option>
                      <option value="Furniture"/>Furniture</option>                  
                      <option value="Other"/>Other</option>
                  </select>

              </td>
              <td><span ng-show="(form.category.$dirty || submitted) && form.category.$error.required">
                      *
                  </span></td>
          </tr>
          
          <tr>
              <td>Title:</td>
              <td><input name="title" ng-model="classified.title" required size="50" /></td>
              <td><span ng-show="(form.title.$dirty || submitted) && form.title.$error.required">
                      *
                  </span></td>
          </tr>
          
          <tr>
              <td>Description:</td>
              <td><textarea name="description" ng-model="classified.description" rows="4" cols="50" required></textarea></td>
              <td><span ng-show="(form.description.$dirty || submitted) && form.description.$error.required">
                      *
                  </span></td>
          </tr>


          <tr>
              <td>Image Link ( If there is one ):</td>
              <td><input name="imageURL" type="url" ng-model="classified.imageURL" size="50"  /></td>
              <td></td>
          </tr>

          <tr>
              <td>Contact Phone:</td>
              <td><input name="contactPhone" type="number" ng-model="classified.contact" size="40" required /></td>
              <td><span ng-show="(form.contactPhone.$dirty || submitted) && form.contactPhone.$error.required">
                      *
                  </span></td>
          </tr>
          <tr>
              <td></td>
              <td><div><button type="submit">Submit</button></div></td>                                                                                                            
              <td></td>
          </tr>
      </table>
      <br>{{PostDataResponse}}<br>
     
      <br>{{ResponseDetails}}<br>
      <a href="classifiedListing.php">Back</a>
  </div>
  </div>

            <script>
                // Controllers for handling this form
                var $app = angular.module("classified", []);

               $app.controller('MyCtrl', function($scope, $http)
                {
                   
                     $scope.submit = function(){
                       
                  
                    $scope.submitted = true;
                    
                    // Send the form to server
                    // $http.post ...
                    var $classifiedData = {
                                 'classifiedCategory':$scope.selectedCategory,
                                 'classifiedTitle':$scope.classified.title,
                                 'classifiedDescription':$scope.classified.description,
                                 'classifiedImageURL':$scope.classified.imageURL,
                                 'classifiedContact':$scope.classified.contact
        }
                    
                    var $config = 'Content-Type:application/x-www-form-urlencoded';
                     $http.post('./index.php', $classifiedData, $config).then
                     (   function($response)
                            {
                              //alert("Post success");
                             $scope.PostDataResponse = 'Your classified has been submitted for review.';
                             $scope.backLink = "Classified.php";
                             
                            },
                          
                         function($response)
                            {
                              console.log($response);
                              alert("Post failure");
                              $scope.ResponseDetails = "Data: " + $response.data +
                                "<hr />status: " + $response.status +
                                "<hr />headers: " + $response.header +
                                "<hr />config: " + $response.config;
                             $scope.backLink = "Classified.php";
                           
                            }
                      );       
                      
               } // end submit function
                    
            //*****************************************
            // Select Category change listener
            //******************************************
            $scope.categoryChange = function()
            {  //alert("In category Change" + $scope.classified.category);
                $scope.selectedCategory = $scope.classified.category;
            } // end categoryChange
                     
            

  
                }) // end Controller

              
                
                
            </script>
           
</form>


    </body>
</html>
