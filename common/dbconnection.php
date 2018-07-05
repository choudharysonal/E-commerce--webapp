<?php
require_once 'dbconfig.php';

/*
    Connects to the Database using PDO
    @author Sonal Choudhary
*/
class DBConnection{
    private $pdo = null;
    public function __construct(){
        global $dsn, $username, $password, $conn_failure_msg;
        try {
            $this->pdo = new PDO($dsn, $username, $password);
            //echo "Connected.";
        } catch (PDOException $pe) {
            die("$conn_failure_msg : " . $pe->getMessage());
        }
    }
    public function open(){
        return $this->pdo;
    }
    public function close(){
        $this->pdo = null;
    }
}
?>
