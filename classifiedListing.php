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
   $jsonData     = addslashes($jsonData);
 
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<html >
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <body>
        <H1>SMR Acropolis Classified Listing (Beta)</h1>
        <div >
            <a href="./classifiedForm.php" target="_blank"><h2>+ Add a Classified</h2></a><br><br>
        </div>
        <div ng-app="app" ng-controller = "classifiedListCtrl">
            <table ng-table="classifiedList" class="table"  border="1">
                <tr><th ng-click="columnFilter='category'"><a href="">Category </a></th><th ng-click="columnFilter='title'"><a href="">Title</a></th><th>Description</th><th>Image Link</th><th>Contact</th><th>Date Posted</th></tr>
                <tr ng-repeat="x in $classifiedItems | orderBy:columnFilter:true| orderBy:createdDate:true">
                    <td>{{x.category}}</td>
                    <td>{{x.title}}</td>
                    <td>{{x.description}}</td>
                    <td><a href="{{x.imageLink}}">{{x.imageLink}}</a></td>
                    <td>{{x.contact}}</td>
                     <td>{{x.createdDate}}</td>
                </tr>
                         
                
              
                 
                
                
            </table>
            
            
            
            
            
        </div> 
        
        <!--****************************************************
         Controller for the above view
        *****************************************************-->
        <script> 
            var $app = angular.module("app", []);
            $app.controller("classifiedListCtrl",['$scope', function($scope)
            {
              
                $scope.$jsonListing ="<?php echo $jsonData ?>";
                $scope.$classifiedItems = JSON.parse($scope.$jsonListing);
                console.log($scope.$classifiedItems);
                
                $scope.sortByColumn = function(x)
                {
                   $scope.columnFilter = x;
                   console.log($scope.columnFilter);
                    
                }
               
                
            }]
            
            
            
            
            );
            
            
          
                
                
                
                
                
             
            
            
            
        </script>
        
    </body>
</html>