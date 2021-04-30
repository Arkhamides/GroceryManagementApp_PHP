<?php
require "DataBase.php";
$db = new DataBase();
if (   isset($_POST['userID']) && isset($_POST['itemName'])  && isset($_POST['brandName']) && isset($_POST['year'])  ) {
    
    
    
    if ($db->dbConnect()) {

        echo $db->editExpDate($_POST['userID'], $_POST['itemName'] , $_POST['brandName'], $_POST['year'],$_POST['month'],$_POST['dayOfMonth'] );
        echo "done";

    } else echo "Error: Database connection";
} else echo "All fields are required";
?>