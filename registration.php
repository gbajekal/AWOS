<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Please Register your apartment</title>
    </head>
    <body>
        <?php
        echo '<H1>Hi '.$_SERVER['USER_NICKNAME'].', You need to Update your Profile!</H1>';
        
        echo '<form method="post" action='. $google_redirect_url.'>';
        echo '<b>Block #</b><br>';
        echo '<input name="block"/><br>';
        echo '<b>Apt #</b> <br>';
        echo '<input name="apt"/><br>';
        echo '<b>Mobile #</b> <br>';
        echo '<input name="mobile"/><br><br>';
        echo '<input type="submit" value="Update">';
        echo '</form>';
        ?>
    </body>
</html>
