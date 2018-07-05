<?php
    require_once '../common/dbconnection.php';
    /*
        Provides data for Search Page. 
        @author Sonal Choudhary
    */
    $dbconnection = new DBConnection;
    class SearchModel{
        private $pdo = null;
        public function __construct(){
            global $dbconnection;
            $this->pdo = $dbconnection->open(); 
        }
        public function search($keyword='', $category=''){
            $books = array();
            if($category == "title"){
                $sql = "SELECT SUM(stocks.number) as stockcount, book.ISBN, book.title FROM book join stocks where book.ISBN=stocks.ISBN and title Like '%".$keyword."%' group by book.ISBN";
            } else if($category == "author"){
                $sql = "SELECT author.name,book.ISBN,book.title, sum(stocks.number) as stockcount FROM author join book join writtenby join stocks where author.ssn=writtenby.ssn and book.ISBN=writtenby.ISBN and book.ISBN=stocks.ISBN and author.name like '%".$keyword."%' group by book.ISBN";
            }
            $ps = $this->pdo->prepare($sql);
            $ps->execute();
            $ps->setFetchMode(PDO::FETCH_ASSOC);
            while ($row = $ps->fetch()) {
                // Don't show the book if there are no stocks available
                if(intval($row['stockcount']) > 0)
                    $books[] = $row; 
            }
            return $books;
        }

    }
?>