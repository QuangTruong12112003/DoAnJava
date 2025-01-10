<?php
require_once 'dbConnection.php';

class UserDAL{
    public function getall()
    {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'SELECT * FROM users';
        $list = array();
        $result = $conn->query($query);
        while($row = $result->fetch_assoc())
        {
            $list[] = $row;
        }
        return $list;
    }

    public function getUser($Email) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $Email = trim($Email);
    
        $query = 'SELECT * FROM users WHERE Email = ?';
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error);
        }
    
        $stmt->bind_param("s", $Email);
    
        if (!$stmt->execute()) {
            die('Execute error: ' . $stmt->error);
        }
    
        $result = $stmt->get_result();
        $list = array();
    
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
    
        $stmt->close();
        return $list;
    }
    
    
    

    function insert($Name, $Email,$Password,$Role)
    {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'INSERT INTO users (UserID, Name, Email, Password, Role) VALUES (NULL, ?, ?, ?, ?)';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $Name,$Email,$Password,$Role);
        return $stmt->execute();
    }

    function delete($UserID)
    {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'DELETE FROM users WHERE UserID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $UserID);
        return $stmt->execute();
    }

    function update($UserID, $Name, $Email, $Password, $Role)
    {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'UPDATE users SET Name = ? , Email = ?, Password = ?, Role = ? WHERE UserID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi",$Name,$Email,$Password,$Role,$UserID);
        return $stmt->execute();
    }
   
    
       

    

}