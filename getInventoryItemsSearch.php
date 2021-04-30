<?php
require "DataBase.php";
$db = new DataBase();
if (   isset($_POST['userID'])  && isset($_POST['str_search'])   && isset($_POST['str_filter'])     ) {
    if ($db->dbConnect()) {

        echo $db->getInventoryTableSearch($_POST['userID'], $_POST['str_search'], $_POST['str_filter']);

    } else echo "Error: Database connection";
} else echo "All fields are required";
?>
