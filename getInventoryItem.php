<?php
require "DataBase.php";
$db = new DataBase();
if (   isset($_POST['userID'])  && isset($_POST['itemName'])  && isset($_POST['brandName']) ) {
    if ($db->dbConnect()) {

        echo $db->getInventoryItem($_POST['userID'], $_POST['itemName'], $_POST['brandName'] );

    } else echo "Error: Database connection";
} else echo "All fields are required";
?>


