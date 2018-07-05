<?php
    $page = "Login";
    require_once '../common/header.php';
    require_once '../model/useraccount-model.php';
    require_once '../model/shoppingbasket-model.php';

    if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'logout.php') > 0){
            echo "<p class='alert alert-success'>We'd love to see you again! Bye!</p>";
    }
    if(isset($_POST['username'])){
        print_r($_POST);
        $userAccountModel = new UserAccountModel();
        if($userAccountModel->login($_POST)){
            // Create a new session
            session_start();
            session_regenerate_id(true);
            $_SESSION['username'] = $_POST['username'];
            // Create a new empty shopping basket for this user in session
            $basket = array();
            $basket['basketID'] = session_id();
            $basket['counter'] = 0;
            $basket['totalPrice'] = 0.00;
            $basket['username'] = $_POST['username'];
            $basket['items'] = array();
            $_SESSION['basket'] = $basket;
            // Redirect to the search page
            header("location:search.php");
            exit;
        } else{
            header("location:login.php?success=false");
            exit;
        }
    }
?>
<div class="container loginForm mt30">
<form action="login.php?submit=true" method="post">
  <div class="form-group">
    <?php
        if(isset($_GET['fatal']) && $_GET['fatal']=="true"){
            echo "<p class='alert alert-danger'>You can't enter our website without signing in. If you don't have account, please create a new one by clicking Register button.</p>";
        }else if(isset($_GET['success']) && $_GET['success']=="true"){
            echo "<p class='alert alert-success'>Thank you for creating an account. Please login below to browse our catalog.</p>";
        }else if(isset($_GET['success']) && $_GET['success']=="false"){
            echo "<p class='alert alert-danger'>Invalid Credentials. Please try again.</p>";
        }else if(isset($_SERVER['HTTP_REFERER'])){
            echo "<p class='alert alert-success'>Thank you for shopping with us. We'd love to see you again! Please login below to start new shopping session or to track the previous order.</p>";
        }
    ?>
  </div>  
  <div class="form-group">
    <label for="username">Enter username</label>
    <input type="text" class="form-control" id="username" name="username" placeholder="Username">
  </div>
  <div class="form-group">
    <label for="pwd">Enter password</label>
    <input type="password" class="form-control" id="pwd" name="password" placeholder="Password">
  </div>
  <div class="form-group offset-sm-3">
    <button class="btn btn-primary mr10" type="submit">Login</button>
    <a href="register.php" class="btn btn-primary" type="submit">New users register here</a>
  </div>
</form>
</div>
</body>
</html>

