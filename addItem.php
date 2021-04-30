<?php
require "DataBase.php";
$db = new DataBase();
if ((isset($_POST['itemName']) && isset($_POST['brandName']))) 
{
    if ($db->dbConnect()) {
        if($db->addItem($_POST['itemName'] , $_POST['brandName'])) {
            echo "Success";
        }

    } else echo "Error: Database connection";
} else echo "All fields are required";
?>
