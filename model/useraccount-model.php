<?php 
    require_once '../common/dbconnection.php';
    $dbconnection = new DBConnection;
    class UserAccountModel {
        private $pdo = null;
        public function __construct(){
            global $dbconnection;
            $this->pdo = $dbconnection->open(); 
        }

       public function login($user){
        $sql = "select count(*) from customers where username='".$user['username']."' and password='".$user['password']."'";
        $ps = $this->pdo->prepare($sql);
        $ps->execute();
        $row = $ps->fetch();
        if(intval($row[0])<=0){
            return false;
        } else{
            return true;
        }
    }

    public function register($user){
        $sql = "insert into Customers values(:username,:password,:address,:mobile,:email)";
        $ps = $this->pdo->prepare($sql);
        $ps->execute($user);
    }

    }
?>