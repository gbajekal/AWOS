<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<?php

include 'header.php';
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Please Register your apartment</title>
    </head>
    <body>
        <?php
        echo '<H1>Hi '.$_SERVER['USER_NICKNAME'].', You need to Update your Profile!</H1>';
        
        echo '<form method="post" action='. $google_redirect_url.'>';
        echo '<table>';
        echo 'echo <tr><td><b>Block #</b></td></tr>';
        echo ''echo <tr><td><input name="block"/></td></tr>';
        echo '<tr><td><b>Apt #</b></td></tr><br>';
        echo '<tr><td><input name="apt"/></td></tr>';
        echo '<tr><td><b>Mobile # prefixed +91</b></td></tr>';
        echo '<tr><td><input name="mobile"/></td></tr>';
        echo '<tr><td></td></tr>';
        echo '<tr><td></td></tr>';
        echo '<tr><td></td></tr>';
        echo '<tr></td><input type="submit" value="Update"></td></tr>';
        echo '</table>';
        echo '</form>';
        ?>
    </body>
</html>
