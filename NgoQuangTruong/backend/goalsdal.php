<?php
require_once 'dbConnection.php';

class GoalDAL {
    public function getAll($UserID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'SELECT * FROM goals WHERE UserID = ?';
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

    public function insert($UserID, $GoalTitle, $Description, $TargetDate, $Visibility, $Status, $Progress) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();

        $query = 'INSERT INTO goals (GoalID, UserID, GoalTitle, Description, TargetDate, Visibility, Status, Progress) 
                  VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = $conn->prepare($query);
    
        $stmt->bind_param("isssssi", $UserID, $GoalTitle, $Description, $TargetDate, $Visibility, $Status, $Progress);
    
        if ($stmt->execute()) {
            $goalId = $conn->insert_id;
            return $goalId; 
        }
        return false;
    }
    

    public function delete($GoalID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        
        $sql1 = 'DELETE FROM tasks WHERE DayPlanID IN (SELECT DayPlanID FROM dayplans WHERE GoalID = ?)';
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("i", $GoalID);
        $stmt1->execute();
        
        $sql2 = 'DELETE FROM dayplans WHERE GoalID = ?';
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $GoalID);
        $stmt2->execute();
        
        $sql3 = 'DELETE FROM goals WHERE GoalID = ?';
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("i", $GoalID);
        
        return $stmt3->execute();
    }
    

    public function update($GoalID, $UserID, $GoalTitle, $Description, $TargetDate, $Visibility, $Status, $Progress) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'UPDATE goals SET UserID = ?, GoalTitle = ?, Description = ?, TargetDate = ?, Visibility = ?, Status = ?, Progress = ? WHERE GoalID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssssii", $UserID, $GoalTitle, $Description, $TargetDate, $Visibility, $Status, $Progress, $GoalID);
        return $stmt->execute();
    }
    public function updateProgress($GoalID,$Progress) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = 'UPDATE goals SET  Progress = ? WHERE GoalID = ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii",$Progress, $GoalID);
        return $stmt->execute();
    }
    public function getGoal($GoalID) {
        $dbConnection = new DBConnection();
    $conn = $dbConnection->getConnection();

    // Kiểm tra kết nối
    if (!$conn) {
        return json_encode(['message' => 'Database connection failed']);
    }

    $query = 'SELECT Progress FROM goals WHERE GoalID = ?';

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $GoalID);
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
    public function getgoalPublic()
    {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = "SELECT goals.*,   users.Name 
        FROM  goals INNER JOIN users ON goals.UserID = users.UserID WHERE  goals.Visibility = 'public'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $list = array();
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
        return $list;
    }
    public function copyGoalDayPlanTask($OldGoalID, $NewUserID) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();

        $conn->begin_transaction();

        try {
            $queryGoal = 'SELECT * FROM goals WHERE GoalID = ?';
            $stmtGoal = $conn->prepare($queryGoal);
            $stmtGoal->bind_param("i", $OldGoalID);
            $stmtGoal->execute();
            $resultGoal = $stmtGoal->get_result();
            $goal = $resultGoal->fetch_assoc();
            $queryInsertGoal = 'INSERT INTO goals (UserID, GoalTitle, Description, TargetDate, Visibility, Status, Progress) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)';
            $stmtInsertGoal = $conn->prepare($queryInsertGoal);
            $stmtInsertGoal->bind_param("isssssi", $NewUserID, $goal['GoalTitle'], $goal['Description'], $goal['TargetDate'], $goal['Visibility'], $goal['Status'], $goal['Progress']);
            $stmtInsertGoal->execute();
            $NewGoalID = $conn->insert_id;  

            $queryDayPlans = 'SELECT * FROM dayplans WHERE GoalID = ?';
            $stmtDayPlans = $conn->prepare($queryDayPlans);
            $stmtDayPlans->bind_param("i", $OldGoalID);
            $stmtDayPlans->execute();
            $resultDayPlans = $stmtDayPlans->get_result();

            while ($dayPlan = $resultDayPlans->fetch_assoc()) {
                $queryInsertDayPlan = 'INSERT INTO dayplans (GoalID, Date, Notes, Status, Progress) 
                                       VALUES (?, ?, ?, ?, ?)';
                $stmtInsertDayPlan = $conn->prepare($queryInsertDayPlan);
                $stmtInsertDayPlan->bind_param("isssi", $NewGoalID, $dayPlan['Date'], $dayPlan['Notes'], $dayPlan['Status'], $dayPlan['Progress']);
                $stmtInsertDayPlan->execute();
                $NewDayPlanID = $conn->insert_id;  
                $queryTasks = 'SELECT * FROM tasks WHERE DayPlanID = ?';
                $stmtTasks = $conn->prepare($queryTasks);
                $stmtTasks->bind_param("i", $dayPlan['DayPlanID']);
                $stmtTasks->execute();
                $resultTasks = $stmtTasks->get_result();

                while ($task = $resultTasks->fetch_assoc()) {
                    $queryInsertTask = 'INSERT INTO tasks (DayPlanID, Title, Description, Priority, Status, Progress, StartTime, EndTime) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
                    $stmtInsertTask = $conn->prepare($queryInsertTask);
                    $stmtInsertTask->bind_param("issssiss", $NewDayPlanID, $task['Title'], $task['Description'], $task['Priority'], $task['Status'], $task['Progress'], $task['StartTime'], $task['EndTime']);
                    $stmtInsertTask->execute();
                }
            }

            $conn->commit();
            return true; 
        } catch (Exception $e) {
            $conn->rollback();
            return false;  
        }
    }
    public function getGoalsOverview($user_id) {
        $dbConnection = new DBConnection();
        $conn = $dbConnection->getConnection();
        $query = "
            SELECT 
                COUNT(CASE WHEN Status = 'achieved' THEN 1 END) AS completed_goals,
                COUNT(CASE WHEN Status = 'in-progress' THEN 1 END) AS in_progress_goals,
                COUNT(CASE WHEN Status = 'not-started' THEN 1 END) AS not_started_goals,
                COUNT(*) AS total_goals
            FROM Goals
            WHERE UserID = ?
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
