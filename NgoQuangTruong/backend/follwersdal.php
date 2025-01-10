<?php
require_once 'dbConnection.php';

class FollowerDAL {
    public function getAll($GoalID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'SELECT * FROM followers WHERE GoalID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $GoalID);
        $stmt->execute();
        $result = $stmt->get_result();
        $list = array();
        while($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        $stmt->close();
        return $list;
    }
    public function getAllUser($UserID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'SELECT * FROM followers WHERE UserID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $UserID);
        $stmt->execute();
        $result = $stmt->get_result();
        $list = array();
        while($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        $stmt->close();
        return $list;
    }

    public function insert($GoalID, $UserID, $FollowedAt) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'INSERT INTO followers (FollowerID, GoalID, UserID, FollowedAt) VALUES (NULL, ?, ?, ?)';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $GoalID, $UserID, $FollowedAt);
        return $stmt->execute();
    }
    public function checkGoalAndUserExist($GoalID, $UserID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
    
        $query = 'SELECT 1 FROM followers WHERE GoalID = ? AND UserID = ? LIMIT 1';
    
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $GoalID, $UserID);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return true; 
        } else {
            return false; 
        }
    }

    public function delete($FollowerID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'DELETE FROM followers WHERE FollowerID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $FollowerID);
        return $stmt->execute();
    }
}
?>
