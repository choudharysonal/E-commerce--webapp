<?php
    $page = 'Shopping Basket';
    require_once '../common/header.php';
    require_once '../model/shoppingbasket-model.php';
    // Get all the items in the session 
    session_start();
    $items_session = $_SESSION['basket']['items'];
    // Get the price for all items in the cart from the database
    $shoppingBasketModel = new ShoppingBasketModel;
    $_SESSION['basket']['items'] = $shoppingBasketModel->calculateSubtotal($items_session);
    // Calculate the total price for the cart
    $_SESSION['basket']['totalPrice'] = $shoppingBasketModel->calculateTotalPrice($_SESSION['basket']['items']);
?>

<div class="container-fluid">
    <div class="jumbotron">
        <div class="ml30">
            <h1>Welcome <?php echo $_SESSION['username']?>,</h1>
            <p><a href='search.php'>Search</a> our latest collection of books from various categories. You may add your favorite books to <a href='shoppingbasket.php'>shopping basket</a> and proceed to purchase.</p>
            <p>Don't forget to <a href='logout.php'><em>logout</em></a> when you leave. Have fun!</p>
        </div>
    </div>
    <div class="row ml30" id="shoppingBasket">
        <h3>Your Shopping Basket (Click Buy to finish shopping)</h3><br>
        <table class="table">
            <tr>
                <th>Book</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
            <?php
                $j=0;
                foreach($_SESSION['basket']['items'] as $item){
                    $displayString = "";
                    $warehouseSelectionString = "<span class='wareshouseStatus'>Shipping from ";
                    $warehouses = array();
                    $warehouseNames = array();
                    $isWarehouseDecided = false;
                    $i=0;
                    foreach($item['warehouses'] as $warehouse){
                        $displayString = $displayString.$warehouse['name'] . " : " . $warehouse['number'] . "<br>";
                        if($item['number']>0){
                            if($item['number'] <= $warehouse['number'] && !$isWarehouseDecided){
                                $isWarehouseDecided = true;
                                $warehouseSelectionString = $warehouseSelectionString.$warehouse['name']."</span>";
                                $_SESSION['basket']['items'][$j]['warehouseUpdate'] = array();
                                $_SESSION['basket']['items'][$j]['warehouseUpdate']['name'] = $warehouse['name'];
                                $_SESSION['basket']['items'][$j]['warehouseUpdate']['warehouseCode'] = $warehouse['warehouseCode'];
                                $_SESSION['basket']['items'][$j]['warehouseUpdate']['stock'] = $item['warehouses'][$i]['number'];
                                $_SESSION['basket']['items'][$j]['isMultiWarehouseUpdate'] = false;
                            } else{
                                $warehouses[$i]['name'] = $warehouse['name'];
                                $warehouses[$i]['warehouseCode'] = $warehouse['warehouseCode'];
                                $warehouses[$i]['stock'] = $warehouse['number'];
                                array_push($warehouseNames,$warehouses[$i]['name']);
                            }
                        }
                        $i++;
                    }
                    if(!$isWarehouseDecided){
                        $dispWarehouses = join(" & ",$warehouseNames);
                        $warehouseSelectionString = $warehouseSelectionString." ".$dispWarehouses."</span>";
                        $_SESSION['basket']['items'][$j]['warehouseUpdate'] = $warehouses;
                        $_SESSION['basket']['items'][$j]['isMultiWarehouseUpdate'] = true;  
                    }
                    echo "<tr><td style='width: 45%'>".$item['book']." <br> ISBN : ".$item['isbn']."
                         <br>".$displayString."<br>".$warehouseSelectionString."
                         </td><td>$".$item['price']."</td>
                         <td>".$item['number']."</td><td>$".number_format($item['subtotal'],2)."</td></tr>";
                    $j++;
                }
            ?>
            <tr>
                <td></td><td></td><td><b>Total Price</b></td><td>$<?=number_format($_SESSION['basket']['totalPrice'],2)?></td>
            </tr>
            <tr><td style="border:none"></td><td style="border:none"><button id="buyBtn" data-url="<?=$buyUrl?>" class="btn btn-primary">Buy</button></td><td></td><td></td></tr>
        </table>
    </div>
    <div class='row ml30'><p style="text-align:center" class='text-danger'>Note: Your order will be placed and we will terminate your shopping session!</p></div>
</div>
</body>
</html>