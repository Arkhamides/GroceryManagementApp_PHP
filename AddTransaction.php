<?php
require "DataBase.php";
$db = new DataBase();
if (  isset($_POST['itemName']) &&  isset($_POST['brandName'])  &&  isset($_POST['userID'])   ) 
{
    if ($db->dbConnect()) {
        
        echo $db->addTransaction($_POST['itemName'] , $_POST['brandName'], $_POST['userID'], $_POST['quantity'] );


    } else echo "Error: Database connection";
} else echo "All fields are required";
?>
