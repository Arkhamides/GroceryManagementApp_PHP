<?php
require "DataBase.php";
$db = new DataBase();
if (   isset($_POST['userID'])     ) {
    if ($db->dbConnect()) {

        echo $db->getInventoryTable($_POST['userID']);

    } else echo "Error: Database connection";
} else echo "All fields are required";
?>
