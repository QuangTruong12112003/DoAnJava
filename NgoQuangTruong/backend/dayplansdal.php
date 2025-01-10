<?php
require_once 'dbConnection.php';

class DayPlanDAL {
    public function getAll($GoalID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'SELECT * FROM dayplans WHERE GoalID = ?';
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

    public function insert($GoalID, $Date, $Notes, $Status, $Progress) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
    
        $query = 'INSERT INTO dayplans (DayPlanID, GoalID, Date, Notes, Status, Progress) VALUES (NULL, ?, ?, ?, ?, ?)';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssi", $GoalID, $Date, $Notes, $Status, $Progress);
    
        if ($stmt->execute()) {
            $lastId = $conn->insert_id;  
            return $lastId;
        } else {
            return false; 
        }
    }
    

    public function delete($DayPlanID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'DELETE FROM dayplans WHERE DayPlanID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $DayPlanID);
        return $stmt->execute();
    }


    public function deletebyGoalID($GoalID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'DELETE FROM dayplans WHERE GoalID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $GoalID);
        return $stmt->execute();
    }

    public function update($DayPlanID, $GoalID, $Date, $Notes, $Status, $Progress) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'UPDATE dayplans SET GoalID = ?, Date = ?, Notes = ?, Status = ?, Progress = ? WHERE DayPlanID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssii", $GoalID, $Date, $Notes, $Status, $Progress, $DayPlanID);
        return $stmt->execute();
    }
    public function updateProgress($DayPlanID,$Progress) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'UPDATE dayplans SET Progress = ? WHERE DayPlanID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $Progress, $DayPlanID);
        return $stmt->execute();
    }
    public function getAverageProgressByGoalID($GoalID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        
        if (!$conn) {
            return json_encode(['message' => 'Database connection failed']);
        }
    
        $query = 'SELECT SUM(Progress) AS totalProgress, COUNT(*) AS taskCount FROM dayplans WHERE GoalID = ?';
        
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $GoalID);
            $stmt->execute();
            $stmt->bind_result($totalProgress, $taskCount);
            
            $response = array();
            if ($stmt->fetch()) {
                if ($taskCount > 0) {
                    $averageProgress = $totalProgress / $taskCount;
                    $response['message'] = round($averageProgress, 2); 
                } else {
                    $response['message'] = 0; 
                }
            } else {
                $response['message'] = 'No data found for the given DayPlanID';  
            }
        
        } else {
            $response = ['message' => 'Failed to prepare SQL query']; 
        }
    
        return  round($averageProgress, 2);
    }
    public function getDayplan($DayPlanID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();

    // Kiểm tra kết nối
    if (!$conn) {
        return json_encode(['message' => 'Database connection failed']);
    }

    $query = 'SELECT Progress FROM dayplans WHERE DayPlanID = ?';

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $DayPlanID);
        $stmt->execute();
        $stmt->bind_result($totalProgress);

        if ($stmt->fetch()) {
            $stmt->close();
            return  round($totalProgress, 2);
        } else {
            $stmt->close();
            return'No data found for the given GoalID';
        }
    } else {
        return  'Failed to prepare SQL query';
    }
    }
}
?>
