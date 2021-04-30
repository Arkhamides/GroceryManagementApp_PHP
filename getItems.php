<?php
require "DataBase.php";
$db = new DataBase();

    if ($db->dbConnect()) {

        echo $db->getItems();

    } else echo "Error: Database connection";

?>
