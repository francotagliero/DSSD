<?php

abstract class PDORepository {

    const USERNAME = "admin";
    const PASSWORD = "admin"; //NDAwNjQwZTYwMDNm
    const HOST ="localhost";
    const DB = "dssd";

    private function getConnection(){
        $u=self::USERNAME;
        $p=self::PASSWORD;
        $db=self::DB;
        $host=self::HOST;
        $connection = new PDO("mysql:dbname=$db;host=$host;charset=utf8", $u, $p);
        //Codigo para mostrar bien el texto en la pagina!

        return $connection;
    }

    public function query(){
        $connection = $this->getConnection();
        $query = "SELECT * FROM usuario";

        $stmt = $connection->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    
}

 ?>
