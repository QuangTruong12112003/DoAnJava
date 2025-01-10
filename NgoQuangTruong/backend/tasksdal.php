<?php
require_once 'dbConnection.php';

class TaskDAL {
    public function getAll($DayPlanID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'SELECT * FROM tasks WHERE DayPlanID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $DayPlanID);
        $stmt->execute();
        $result = $stmt->get_result();
        $list = array();
        while($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        $stmt->close();
        return $list;
    }

    public function insert($DayPlanID, $Title, $Description, $Priority, $Status, $Progress, $StartTime, $EndTime) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'INSERT INTO tasks (TaskID, DayPlanID, Title, Description, Priority, Status, Progress, StartTime, EndTime) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issssiss", $DayPlanID, $Title, $Description, $Priority, $Status, $Progress, $StartTime, $EndTime);
        return $stmt->execute();
    }

    public function delete($TaskID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'DELETE FROM tasks WHERE TaskID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $TaskID);
        return $stmt->execute();
    }

    public function deletebyDayplanID($DayPlanID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'DELETE FROM tasks WHERE DayPlanID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $DayPlanID);
        return $stmt->execute();
    }

    public function update($TaskID, $DayPlanID, $Title, $Description, $Priority, $Status, $Progress, $StartTime, $EndTime) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'UPDATE tasks SET DayPlanID = ?, Title = ?, Description = ?, Priority = ?, Status = ?, Progress = ?, StartTime = ?, EndTime = ? WHERE TaskID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issssissi", $DayPlanID, $Title, $Description, $Priority, $Status, $Progress, $StartTime, $EndTime, $TaskID);
        return $stmt->execute();
    }
    public function getAverageProgressByDayPlanID($DayPlanID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        
        if (!$conn) {
            return json_encode(['message' => 'Database connection failed']);
        }
    
        $query = 'SELECT SUM(Progress) AS totalProgress, COUNT(*) AS taskCount FROM tasks WHERE DayPlanID = ?';
        
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $DayPlanID);
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
    public function getTasksOverview($user_id) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = "
            SELECT 
                COUNT(CASE WHEN Status = 'pending' THEN 1 END) AS pending_tasks,
                COUNT(CASE WHEN Status = 'in-progress' THEN 1 END) AS in_progress_tasks,
                COUNT(CASE WHEN Status = 'completed' THEN 1 END) AS completed_tasks,
                COUNT(CASE WHEN Status = 'overdue' THEN 1 END) AS overdue_tasks,
                COUNT(*) AS total_tasks
            FROM Tasks
            WHERE DayPlanID IN (
                SELECT DayPlanID 
                FROM DayPlans 
                WHERE GoalID IN (
                    SELECT GoalID 
                    FROM Goals 
                    WHERE UserID = ?
                )
            )
        ";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        
        if (!$stmt->execute()) {
            die('Execute error: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $overview = $result->fetch_assoc();
        $stmt->close();
        
        return $overview;
    }
    
    
    
}
?>
