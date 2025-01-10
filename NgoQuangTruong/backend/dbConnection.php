<?php
class DBConnection{
    private $host = "localhost";
    private $db_name = "quanlymuctieu";
    private $username = "root";
    private $password = "";
    private $conn;

    /**Khởi tạo và mở kết nối */
    public function __construct()
    {
        $this->conn = new mysqli($this->host,$this->username,$this->password,$this->db_name);
    }

    /**Đóng kết nói CSDL */
    function __destruct()
    {
        $this->conn->close();
    }

    public function getConnection()
    {
        return $this->conn;
    }
}