<?php
require "DataBase.php";
$db = new DataBase();
if (   isset($_POST['userID'])  && isset($_POST['oldName'])  && isset($_POST['oldBrand']) 
&& isset($_POST['newName'])  && isset($_POST['newBrand']) 
&& isset($_POST['newMeasurement'])  && isset($_POST['newPrice']) 
&& isset($_POST['newCalories'])  ) {
    
    
    
    if ($db->dbConnect()) {

        echo $db->editInventoryItem($_POST['userID'],$_POST['oldName'], $_POST['oldBrand'], 
        $_POST['newName'], $_POST['newBrand'],  $_POST['newMeasurement'], $_POST['newPrice'], $_POST['newCalories'],  $_POST['newMinQty'] );

    } else echo "Error: Database connection";
} else echo "All fields are required";
?>
