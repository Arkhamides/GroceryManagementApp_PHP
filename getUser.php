<?php
require "DataBase.php";
$db = new DataBase();
if (   isset($_POST['username'])     ) {
    if ($db->dbConnect()) {

        echo $db->getUser($_POST['username']);


    } else echo "Error: Database connection";
} else echo "All fields are required";
?>
