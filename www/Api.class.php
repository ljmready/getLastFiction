<?php
class Api {
    protected $connection = null;
    public function __construct($connection)
    {
        $this->connection = $connection;
    }
    public function fictions() 
    {
        $sql = 'SELECT id, title FROM story ORDER BY id DESC LIMIT 15';
        $result = $this->connection->query($sql);
        $array = [];
        while($row = $result->fetch_assoc()) {
            $array[] = $row; 
        }
        echo json_encode($array);
        //var_dump($array);
    }
    public function fiction()
    {
        $id = intval($_GET['id']);
        if(empty($id)) {
            return;
        }
        $sql = "SELECT * FROM story WHERE id = {$id} LIMIT 1";
        $row = $this->connection->query($sql)->fetch_assoc();
        echo json_encode($row, JSON_HEX_TAG);
    }
}
