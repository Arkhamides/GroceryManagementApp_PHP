<?php
require "DataBase.php";
$db = new DataBase();
if (    isset($_POST['userID'] ) && isset($_POST['itemName']) && isset($_POST['brandName']) && isset( $_POST['brandName'] )    ) 
{
    if ($db->dbConnect()) {
        
        echo $db->addInventoryItem($_POST['userID'] , 
                                $_POST['itemName'] , 
                                $_POST['brandName'] ,
                                $_POST['measurementLabel'],
                                $_POST['price'],
                                $_POST['calories'],
                                $_POST['quantity'],
                                $_POST['min_quantity']
                            );

    } else echo "Error: Database connection";
} else echo "All fields are required";
?>
