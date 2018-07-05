<?php 
    
    require_once '../common/dbconnection.php';
    $dbconnection = new DBConnection;

    // Purchase the items in the shopping basket
    if(isset($_POST['action']) && $_POST['action']=='buy'){
        session_start();
        $shoppingBasketModel = new ShoppingBasketModel;
        echo json_encode($shoppingBasketModel->buy($_SESSION['basket']));
    }
    
    // Add the items to the basket in the session
    else if(isset($_POST['action']) && $_POST['action']=='addtocart'){
        // Create a new item
        $item = array();
        $item['isbn'] = $_POST['isbn'];
        $item['basketId'] = $_POST['basketId'];
        $item['number'] = intval($_POST['qty']);
        $item['book'] = $_POST['bookTitle'];
        $item['stockcount'] = $_POST['stockcount'];
        $item['price'] = 0.00;
        $item['subtotal'] = 0.00;
        // Update the number if this item already exists otherwise add it to the basket 
        session_start();
        $items_session = $_SESSION['basket']['items'];
        $updateItem = false;
        $i=0;
        // Iterate the items in basket to find out if it exists
        foreach($items_session as $item_session){
            // Match found
            if($item_session['isbn'] == $item['isbn']){
                // Update the number for this item
                $updateItem = true;
                $_SESSION['basket']['items'][$i]['number'] = $item['number'];
                // Update the shopping basket counter based on this updated quantity for this item
                if($item_session['number'] < $item['number']){
                    $_SESSION['basket']['counter'] += ($item['number'] - $item_session['number']);
                } else if($item_session['number'] > $item['number']){
                    $_SESSION['basket']['counter'] -= ($item_session['number'] - $item['number']);
                }
                break;
            }
            $i++;
        }
        // No match found
        if(!$updateItem){
             // Add the warehouses to be updated for this item
            $shoppingBasketModel = new ShoppingBasketModel;
            $item['warehouses'] = $shoppingBasketModel->getWarehouses($item);
            // Add this item to the items array in shopping basket 
            array_push($_SESSION['basket']['items'], $item);
            // Increment the counter for the basket after adding this item
            $_SESSION['basket']['counter'] += $item['number'];
        }
        // Send a response back to the ajax call
        $data = array();
        $data['success'] = true;
        $data['newcount'] = $_SESSION['basket']['counter'];
        echo json_encode($data);
    }

    class ShoppingBasketModel {
        private $pdo = null;
        /* Constructor which opens a connection to the database */
        public function __construct(){
            global $dbconnection;
            $this->pdo = $dbconnection->open(); 
        }
        /* Calculates the price and subtotal (qty * price) for each item */
        public function calculateSubtotal($items){
            $isbns = array();
            foreach($items as $item){
                array_push($isbns,$item['isbn']);
            }
            $isbns = join("','",$isbns);   
            $sql = "SELECT price FROM book WHERE ISBN IN ('$isbns')";
            $ps = $this->pdo->prepare($sql);
            $ps->execute();
            $ps->setFetchMode(PDO::FETCH_ASSOC);
            $i=0;
            while ($row = $ps->fetch()) {
                $items[$i]['price'] = $row['price'];
                $items[$i]['subtotal'] = $row['price'] * $items[$i]['number']; 
                $i++;
            }
            return $items;
        }
        /* Returns the total price of the shopping cart (Sum of subtotal (qty * price) of all items) */
        public function calculateTotalPrice($items){
            $total = 0;
            foreach($items as $item){
                $total += $item['subtotal'];
            }
            return $total;
        }
        /* Implements the Buy functionality (Save the shopping basket in session to database)
            1. Create a new basket with current basketId and username in ShoppingBasket table
            2. Add all the items for this basket to the Contains table
            3. Make an entry with warehouse code (take the closest warehouse to the user location, in case of multiple warehouses available) in the ShippingOrder table
            4. Update the Stocks table, decrease the count from the corresponding warehouse for each item 
            5. w100 - ArlingtonWarehouse is the default from where all books are delivered till it go out of stock
        */
        public function buy($basket){
            $data = array();
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "basketid = ".$basket['basketID'];
            try{
            $this->pdo->beginTransaction();
            $this->pdo->exec("insert into ShoppingBasket(basketID,username) values('".$basket['basketID']."','".$basket['username']."')");
            foreach($basket['items'] as $item){
                $this->pdo->exec("insert into Contains(ISBN,basketID,number) values('".$item['isbn']."','".$basket['basketID']."','".$item['number']."')");
                $this->pdo->exec("insert into ShippingOrder(ISBN,warehouseCode,username,number) values('".$item['isbn']."','w100','".$basket['username']."','".$item['number']."') on duplicate key update number=".$item['number']);
                if($item['isMultiWarehouseUpdate']){
                    $counter = $item['number'];
                    foreach($item['warehouseUpdate'] as $warehouseUpdate){
                        $warehouseCode = $warehouseUpdate['warehouseCode'];
                        $counter -= $warehouseUpdate['stock'];
                        if($counter>=0){
                            //echo "updating ".$warehouseCode." with counter = ".$counter;
                            $this->pdo->exec("update stocks set number=0 where ISBN=".$item['isbn']." and warehouseCode='".$warehouseCode."'");
                        }else{
                            /*10 6 4
                            7  1 -3
                            8  2 -2
                            9  3 -1
                            10 4 0*/
                            $newstock = $warehouseUpdate['stock']+$counter; // counter is negative here
                            //echo "updating ".$warehouseCode." with new stock = ".$newstock;
                            $this->pdo->exec("update stocks set number=".$newstock." where ISBN=".$item['isbn']." and warehouseCode='".$warehouseCode."'");
                        }
                            
                    }
                }else{
                    $warehouseCode = $item['warehouseUpdate']['warehouseCode'];
                    //echo "updating ".$warehouseCode;
                    $this->pdo->exec("update stocks set number=number-".$item['number']." where ISBN=".$item['isbn']." and warehouseCode='".$warehouseCode."'");
                }
                
            }
            $this->pdo->commit();
            $data['success']=true;
            }catch(PDOException $pe){
                $this->pdo->rollback();
                $data['success']=false;
                $data['errmsg']=$pe->getMessage();
            }
            return $data;
        }
        /* Get the stocks from multiple warehouses for the given item */
        public function getWarehouses($item){
            $warehouses = array();
            $sql = "select stocks.warehouseCode,name,number from stocks join warehouse WHERE isbn='".$item['isbn']."' and stocks.warehouseCode=warehouse.warehouseCode";
            $ps = $this->pdo->prepare($sql);
            $ps->execute();
            $ps->setFetchMode(PDO::FETCH_ASSOC);
            while ($row = $ps->fetch()) {
                    $warehouses[] = $row;
            }
            return $warehouses;
        }
        /* >> IGNORE : NOT IN USE << */
        public function addToCart($item){
            $data = array();
            try{
                $sql = "insert into contains values(:isbn,:basketId,:number) on duplicate key update number=".$item['number'];
                $ps = $this->pdo->prepare($sql);
                $ps->execute($item);
                $data['success'] = true;
            } catch(Exception $e){
                $data['success'] = false;
                $data['errors'] = $e; 
            }
            return $data;            
        }
        /* >> IGNORE : NOT IN USE << */
        public function existsBasket($basket){
            $sql = "select count(*) from shoppingbasket where basketID='".$basket['basketID']."'";
            $ps = $this->pdo->prepare($sql);
            $ps->execute();
            $row = $ps->fetch();
            if(intval($row[0])<=0){
                return false;
            } else{
                return true;
            }
        }
        /* >> IGNORE : NOT IN USE << */
        public function createShoppingBasket($basket){
            if(!$this->existsBasket($basket)){
                // Create a new shopping basket for this user
                $sql = "insert into shoppingbasket values(:basketID,:username)";
                $ps = $this->pdo->prepare($sql);
                $ps->execute($basket);
            }
        }
    }
?>