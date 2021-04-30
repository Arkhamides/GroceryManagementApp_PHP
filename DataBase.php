<?php
require "DataBaseConfig.php";

class DataBase
{
    public $connect;
    public $data;
    private $sql;
    protected $servername;
    protected $username;
    protected $password;
    protected $databasename;

    public function __construct()
    {
        $this->connect = null;
        $this->data = null;
        $this->sql = null;
        $dbc = new DataBaseConfig();
        $this->servername = $dbc->servername;
        $this->username = $dbc->username;
        $this->password = $dbc->password;
        $this->databasename = $dbc->databasename;
    }

    function dbConnect()
    {
        $this->connect = mysqli_connect($this->servername, $this->username, $this->password, $this->databasename);
        return $this->connect;
    }

    function prepareData($data)
    {
        return mysqli_real_escape_string($this->connect, stripslashes(htmlspecialchars($data)));
    }

    function logIn($table, $username, $password)
    {
        $username = $this->prepareData($username);
        $password = $this->prepareData($password);

        $this->sql = "select * from " . $table . " where username = '" . $username . "'";

        $result = mysqli_query($this->connect, $this->sql);
        $row = mysqli_fetch_assoc($result);
        if (mysqli_num_rows($result) != 0) {
            $dbusername = $row['username'];
            $dbpassword = $row['password'];
            if ($dbusername == $username && password_verify($password, $dbpassword)) {
                $login = true;
            } else $login = false;
        } else $login = false;

        return $login;
    }

    function signUp($table, $fullname, $email, $username, $password)
    {
        $fullname = $this->prepareData($fullname);
        $username = $this->prepareData($username);
        $password = $this->prepareData($password);
        $email = $this->prepareData($email);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $this->sql =
            "INSERT INTO " . $table . " (fullname, username, password, email) VALUES ('" . $fullname . "','" . $username . "','" . $password . "','" . $email . "')";
        if (mysqli_query($this->connect, $this->sql)) {
            return true;
        } else return false;
    }





    function getTable($table) {

        $sql = "SELECT * FROM $table";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
        // output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
                }
            } else {
            echo "0 results";
        }

    }

    function getBrandID($table, $BrandName){
        
        $BrandName = $this->prepareData($BrandName);

        $this->sql = "select * from " . $table . " where brand_name = '" . $BrandName . "'";
        $result = mysqli_query($this->connect, $this->sql);
        $row = mysqli_fetch_assoc($result);
        
         if (mysqli_num_rows($result) != 0) {
            return $row['id'];
            } else return "-1";
        
    }

    function getItemID($itemName, $brandName) {

        $itemName = $this->prepareData($itemName);
        $brandName = $this->prepareData($brandName);

        $brandID = $this->getBrandID('brands', $brandName);
        if($brandID == "-1") {
            return "-1";
        }

        $this->sql = "SELECT id FROM items 
                        WHERE item_name = '" . $itemName . "'" .
                        " AND brandID = '" . $brandID . "'" ;
        $result = mysqli_query($this->connect, $this->sql);
        $row = mysqli_fetch_assoc($result);

        
         if (mysqli_num_rows($result) != 0) {
           
            return $row['id'];
            } else return "-1";

    }

    function getInventoryItemID($userID, $itemName, $brandName){

        $userID = $this->prepareData($userID);
        $itemName = $this->prepareData($itemName);
        $brandName = $this->prepareData($brandName);

        $itemID = $this->getItemID($itemName , $brandName);

         $this->sql = "SELECT * FROM inventory_items 
                        WHERE userID = '" . $userID . "'" .
                        " AND itemID = '" . $itemID . "'" ;

        
        $result = mysqli_query($this->connect, $this->sql);
        
        

        if (mysqli_num_rows($result) != 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['id'];
        } else return "-1";


    }

    function getUser($username) {

        $username = $this->prepareData($username);



        $this->sql = "SELECT * FROM users 
                        WHERE username = '" . $username . "'";
        $result = mysqli_query($this->connect, $this->sql);
        $row = mysqli_fetch_assoc($result);
        
         if (mysqli_num_rows($result) != 0) {
            return $row['id'] ."," . 
            $row['username'] . ',' .
            $row['email'];
        } else return "-1";

    }

    function getItems() {

        $this->sql = "SELECT * FROM items JOIN brands ON items.brandID = brands.id";
        $result = mysqli_query($this->connect, $this->sql);
        
         if (mysqli_num_rows($result) != 0) {
            
            $count = 1;

            while($row = $result->fetch_assoc()) {

                if($count == 1) {
                    $count--;
                }else {
                    echo ",";
                }

                echo  $row['item_name'] ."," . 
                $row['brand_name'] ;
            }

        } else echo "-1";

    }

    function getInventoryTable($userID) {


        $this->sql = "SELECT *" .

                " FROM inventory_items" .
                " JOIN items" .
                " ON inventory_items.itemID = items.id" .
                " JOIN brands" .
                " ON items.brandID = brands.id" .
                " WHERE inventory_items.userID = '" . $userID . "'" .
                " ORDER BY items.item_name";
        $result = mysqli_query($this->connect, $this->sql);
        
         if (mysqli_num_rows($result) != 0) {
            
            $count = 1;

            while($row = $result->fetch_assoc()) {

                if($count == 1) {
                    $count--;
                }else {
                    echo ",";
                }

                echo  $row['item_name'] ."," . 
                $row['brand_name'] ."," .
                $row['quantity'] ."," .
                $row['price'] ."," .
                $row['calories'] ."," .
                $row['min_quantity'] 
                ;
            }

        } else echo "-1";
        
    }

    function getInventoryTableSearch($userID, $search, $order) {

        if($order=="Name") {
            $order = "items.item_name";
        }
        if($order=="Brand") {
            $order = "brands.brand_name";
        }
        if($order=="Quantity") {
            $order = "inventory_items.quantity";
        }


        $this->sql = "SELECT *" .

                " FROM inventory_items" .
                " JOIN items" .
                " ON inventory_items.itemID = items.id" .
                " JOIN brands" .
                " ON items.brandID = brands.id" .
                " WHERE inventory_items.userID = '" . $userID . "'" .
                " AND items.item_name LIKE '%".$search."%'".
                " ORDER BY $order";

        $result = mysqli_query($this->connect, $this->sql);
        
         if (mysqli_num_rows($result) != 0) {
            
            $count = 1;

            while($row = $result->fetch_assoc()) {

                if($count == 1) {
                    $count--;
                }else {
                    echo ",";
                }

                echo  $row['item_name'] ."," . 
                $row['brand_name'] ."," .
                $row['quantity'] ."," .
                $row['price'] ."," .
                $row['calories'] ."," .
                $row['min_quantity'] 
                ;
            }

        } else echo "-1";
        
    }

    function getInventoryItem($userID, $itemName, $brandName ) {

        $userID = $this->prepareData($userID);
        $itemName = $this->prepareData($itemName);
        $brandName = $this->prepareData($brandName);

        $inventoryItemID = $this->getInventoryItemID($userID, $itemName, $brandName);
        $itemID = $this->getItemID($itemName, $brandName);
        $brandID = $this->getBrandID("brands", $brandName);

            $this->sql = "SELECT *" .

                " FROM inventory_items" .
                " JOIN items" .
                " ON inventory_items.itemID = items.id" .
                " JOIN brands" .
                " ON items.brandID = brands.id" .
                " WHERE inventory_items.id = '" . $inventoryItemID . "'";

            $result = mysqli_query($this->connect, $this->sql);
            $row = mysqli_fetch_assoc($result);

        if (mysqli_num_rows($result) != 0) {
            return $row['item_name'] . "," .
            $row['brand_name'] . "," .
            $row['quantity'] . "," .
            $row['price'] . "," .
            $row['calories'] . "," .
            $row['min_quantity'] . "," .
            $row['measurement_label_ID'] . "," .
            $row['expiry_date'];
        } else return "-1";

    }

    function getGroceryList($userID) {

        $this->sql = "SELECT *" .

                " FROM inventory_items" .
                " JOIN items" .
                " ON inventory_items.itemID = items.id" .
                " JOIN brands" .
                " ON items.brandID = brands.id" .
                " WHERE inventory_items.userID = '" . $userID . "'" .
                " AND(" . 
                " expiry_date between '2000/02/25' AND NOW()" .
                " OR quantity < min_quantity". ")" .
                " ORDER BY items.item_name";
                
        $result = mysqli_query($this->connect, $this->sql);
        
         if (mysqli_num_rows($result) != 0) {
            
            $count = 1;

            while($row = $result->fetch_assoc()) {

                if($count == 1) {
                    $count--;
                }else {
                    echo ",";
                }

                echo  $row['item_name'] .", " . 
                $row['brand_name'] .", " .
                $row['quantity'] .", " .
                $row['price'] .", " .
                $row['calories'] .", " .
                $row['min_quantity'] . ", " .
                $row['expiry_date'] ;
            }

        } else echo "-1";
        
    }

    function getHistoryItems($userID) {

        $this->sql = "SELECT item_name, brand_name, transaction_history.quantity, date" .

                " FROM transaction_history" .
                " JOIN inventory_items" .
                " ON transaction_history.inventory_item_id = inventory_items.id" .
                " JOIN items" .
                " ON inventory_items.itemID = items.id" .
                " JOIN brands" .
                " ON items.brandID = brands.id" .
                " WHERE inventory_items.userID = '" . $userID . "'" .
                " ORDER BY date DESC";


        $result = mysqli_query($this->connect, $this->sql);
        
         if (mysqli_num_rows($result) != 0) {
            
            $count = 1;

            while($row = $result->fetch_assoc()) {

                if($count == 1) {
                    $count--;
                }else {
                    echo ",";
                }

                echo  $row['item_name'] ."," . 
                $row['brand_name'] ."," .
                $row['quantity']  . "," .
                $row['date']
                ;
            }

        } else echo "-1";



    }

    

    function addBrand($table, $brandName ) {
        $brandName = $this->prepareData($brandName);
        $table = $this->prepareData($table);
         $this->sql =
            "INSERT INTO " . $table . " (brand_name) VALUES ('" . $brandName . "')";

            if (mysqli_query($this->connect, $this->sql)) {
            return true;
        } else return false;
    }

    function addItem($itemName, $brandName) {

        $itemName = $this->prepareData($itemName);
        $brandName = $this->prepareData($brandName);

        $brandID = $this->getBrandID('brands', $brandName);
        
        if($brandID == "-1") {
            $this->addBrand('brands', $brandName);
        }
        $brandID = $this->getBrandID('brands', $brandName);
        
        $this->sql =
            "INSERT INTO " . "items" . " (item_name, brandID) VALUES ('" . $itemName . "','" . $brandID . "')";

        if (mysqli_query($this->connect, $this->sql)) {
            echo true;
        } else echo false;
        
    }

    function addInventoryItem($userID, $itemName,  $brandName,  $measurementLabel,  $price,  $calories, $quantity,  $min_quantity){

        $inventoryItemID = $this->getInventoryItemID($userID, $itemName, $brandName);
        
        if($inventoryItemID > 0) {
            return "Item Exists in your inventory";
        }


        $itemID = $this->getItemID($itemName, $brandName);

        if($itemID == "-1"){
            $this->addItem($itemName, $brandName);
        }

        $itemID = $this->getItemID($itemName, $brandName);

        $this->sql =
            "INSERT INTO inventory_items (userID, itemID, quantity, price, calories, measurement_label_ID, min_quantity)" . 
            "VALUES (" . 
            "'" . $userID . "'," .
            "'" . $itemID . "'," .
            "'" . $quantity . "'," .
            "'" . $price . "'," .
            "'" . $calories . "'," .
            "'" . $measurementLabel . "'," .
            "'" . $min_quantity . "')";

        if (mysqli_query($this->connect, $this->sql)) {
            echo "true";
        } else echo "false";

    }

    function addTransaction($itemName, $brandName, $userID, $quantity) {

        $itemName = $this->prepareData($itemName);
        $brandName = $this->prepareData($brandName);
        $userID = $this->prepareData($userID);
        $quantity = $this->prepareData($quantity);

        echo "qty: $quantity ,";

        $inventoryItemID = $this->getInventoryItemID($userID, $itemName, $brandName);
        
        
        if($inventoryItemID == "-1") {
            echo false;
        }
        
        $this->sql =
            "INSERT INTO transaction_history(inventory_item_id, quantity, date) VALUES ($inventoryItemID, $quantity, now())";

        if (mysqli_query($this->connect, $this->sql)) {
            echo true;
        } else echo false;

    }



    function editInventoryItem($userID, $oldItemName,  $oldBrandName, $newItemName,  $newBrandName,  $measurementLabel,  $price,  $calories,  $min_quantity) {

        
        $itemID = $this->getItemID($oldItemName, $oldBrandName);
        $brandID = $this->getBrandID('brands', $oldBrandName);
        $inventoryItemID = $this->getInventoryItemID($userID, $oldItemName,  $oldBrandName);
        
        $itemID = $this->getItemID($oldItemName,  $oldBrandName);
        
        $this->editItemName($itemID, $newItemName);
        $this->editBrandName($brandID, $newBrandName);
        $this->editInventoryItemRows($inventoryItemID, $measurementLabel,  $price,  $calories,  $min_quantity);

    }


    function editItemName($itemID, $itemName) {

        $itemID = $this->prepareData($itemID);
        $itemName = $this->prepareData($itemName);

        $sql = "UPDATE items SET item_name='$itemName' WHERE id='$itemID'";

        if (mysqli_query($this->connect, $sql)) {
            echo true;
        } else echo false;

        
    }

    function editBrandName($brandID, $brandName) {

        $brandID = $this->prepareData($brandID);
        $brandName = $this->prepareData($brandName);

        $sql = "UPDATE brands SET brand_name='$brandName' WHERE id='$brandID'";

        if (mysqli_query($this->connect, $sql)) {
            echo true;
        } else echo false;

    }

    function editInventoryItemRows($InventoryItemID, $measurementLabel,  $price,  $calories,  $min_quantity) {

        $InventoryItemID = $this->prepareData($InventoryItemID);
        $measurementLabel = $this->prepareData($measurementLabel);
        $price = $this->prepareData($price);
        $calories = $this->prepareData($calories);
        $min_quantity = $this->prepareData($min_quantity);

        $sql = "UPDATE inventory_items SET 
        measurement_label_ID ='$measurementLabel' ,
        price='$price' ,
        calories='$calories' ,
        min_quantity='$min_quantity'
        WHERE id='$InventoryItemID'";

        if (mysqli_query($this->connect, $sql)) {
            echo true;
        } else echo false;



    }

    function editExpDate($userID, $itemName, $brandName, $year, $month, $day) {
        
        $userID = $this->prepareData($userID);
        $itemName = $this->prepareData($itemName);
        $brandName = $this->prepareData($brandName);
        $year = $this->prepareData($year);
        $month = $this->prepareData($month);
        $day = $this->prepareData($day);

        $InventoryItemID = $this->getInventoryItemID($userID, $itemName, $brandName);

        $sql = "UPDATE inventory_items 
        SET expiry_date = CAST('$year-$month-$day' AS DATETIME) 
        WHERE id='$InventoryItemID'";

        if (mysqli_query($this->connect, $sql)) {
            echo true;
        } else echo false;

    }


    function editQuantity($userID, $itemName, $brandName, $quantity) {

        $inventoryItemID = $this->getInventoryItemID($userID, $itemName, $brandName);

        $sql = "UPDATE inventory_items SET 
        quantity ='$quantity'
        WHERE id='$inventoryItemID'";

        if (mysqli_query($this->connect, $sql)) {
            echo true;
        } else echo false;
        

    }


    function deleteInvItemHistory($inventoryItemID) {
        $inventoryItemID = $this->prepareData($inventoryItemID);

        $this->sql =
            " DELETE FROM transaction_history 
            WHERE inventory_item_id = $inventoryItemID";

        if (mysqli_query($this->connect, $this->sql)) {
            echo true;
        } else echo false;

    }


    function deleteInvItem($userID, $itemName, $brandName) {

        $inventoryItemID = $this->getInventoryItemID($userID, $itemName, $brandName);
        $this->deleteInvItemHistory($inventoryItemID);


        $this->sql =
            " DELETE FROM inventory_items
            WHERE id = $inventoryItemID";


        if (mysqli_query($this->connect, $this->sql)) {
            echo true;
        } else echo false;

    }






}

?>
