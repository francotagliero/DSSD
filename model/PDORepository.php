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

    public function query($query){
        $connection = $this->getConnection();
        

        $stmt = $connection->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    protected function queryList($sql, $args, $mapper)
    {
        $connection = $this->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->execute($args);
        $list = [];
        while($element = $stmt->fetch()){
            $list[] = $mapper($element);
        }
        return $list;
    }

    
}

 ?>
