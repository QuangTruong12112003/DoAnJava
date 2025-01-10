<?php

error_reporting(1);

require_once  'usersdal.php';
require_once 'goalsdal.php';
require_once 'tasksdal.php';
require_once 'follwersdal.php';
require_once 'dayplansdal.php';
$message = array();

$dalUser = new UserDAL();
$dalGoal = new GoalDAL();
$dalTask = new TaskDAL();
$daldayplan = new DayPlanDAL();
$dalfollwer = new FollowerDAL();

switch($_POST["action"])
{
    case 'getallUser':
        $message = $dalUser->getall();
        break;
    case 'insertUser':
        $name = $_POST["Name"];
        $email = $_POST["Email"];
        $password = $_POST["Password"];
        $Role = $_POST["Role"];
        $result = $dalUser->insert($name,$email,$password,$Role);
        $message = ["message" => json_encode($result)];
        break;
    case 'deleteUser':
        $userId = $_POST["UserID"];
        $result = $dalUser->delete($userId);
        $message = ["message" => json_encode($result)];
        break;
    case 'updateUser':
        $userId = $_POST["UserID"];
        $name = $_POST["Name"];
        $email = $_POST["Email"];
        $password = $_POST["Password"];
        $Role = $_POST["Role"];
        $result = $dalUser->update($userId,$name,$email,$password,$Role);
        $message = ["message" => json_encode($result)];    
        break;
    case 'getUser':
        $Name = $_POST["Email"];
        $message = $dalUser->getUser($Name);
        break;

    /**Goals DAL */
    case 'getAllGoals':
        $userId = $_POST["UserID"];
        $message = $dalGoal->getAll($userId);
        break;
    case 'getGoal':
        $goalId = $_POST["GoalId"];
        $result = $dalGoal->getGoal($goalId);
        $message = ["message" => json_encode($result)];
        break;
    case 'insertGoal':
        $userId = $_POST["UserID"];
        $goalTitle = $_POST["GoalTitle"];
        $description = $_POST["Description"];
        $targetDate = $_POST["TargetDate"];
        $visibility = $_POST["Visibility"];
        $status = $_POST["Status"];
        $progress = $_POST["Progress"];
        $goalId = $dalGoal->insert($userId, $goalTitle, $description, $targetDate, $visibility, $status, $progress);
        if ($goalId) {
            $message = ["message" => "true", "goalId" => $goalId];
        } else {
        $message = ["message" => "false"];
        }
        echo json_encode($message);
        break;
        case 'deleteGoal':
            $goalId = $_POST["GoalID"];
            $result = $dalGoal->delete($goalId);
            $message = ["message" => json_encode($result)];
            break;
    case 'updateGoal':
        $goalId = $_POST["GoalID"];
        $userId = $_POST["UserID"];
        $goalTitle = $_POST["GoalTitle"];
        $description = $_POST["Description"];
        $targetDate = $_POST["TargetDate"];
        $visibility = $_POST["Visibility"];
        $status = $_POST["Status"];
        $progress = $_POST["Progress"];
        $result = $dalGoal->update($goalId, $userId, $goalTitle, $description, $targetDate, $visibility, $status, $progress);
        $message = ["message" => json_encode($result)];
        break;
        case 'updateProgressGoal':
            $goalId = $_POST["GoalID"];
            $progress = $_POST["Progress"];
            $result = $dalGoal->updateProgress($goalId, $progress);
            $message = ["message" => json_encode($result)];
            break;

        /** DayPlan DAL */
        case 'getDayplan':
            $dayplanId = $_POST["DayPlanID"];
            $result = $daldayplan->getDayplan($dayplanId);
            $message = ["message" => json_encode($result)];
            break;
        case 'getAllDayPlans':
            $goalId = $_POST["GoalID"];
            $message = $daldayplan->getAll($goalId);
            break;
            case 'insertDayPlan':
                $goalId = $_POST["GoalID"];
                $date = $_POST["Date"];
                $notes = $_POST["Notes"];
                $status = $_POST["Status"];
                $progress = $_POST["Progress"];
                
                $dayPlanId = $daldayplan->insert($goalId, $date, $notes, $status, $progress);
                if ($dayPlanId) {
                    $message = ["message" => true, "dayplanid" => $dayPlanId]; 
                } else {
                    $message = ["message" => false];
                }
                echo json_encode($message);
                break;
            
        case 'deleteDayPlan':
            $dayPlanId = $_POST["DayPlanID"];
            $result = $daldayplan->delete($dayPlanId);
            $message = ["message" => json_encode($result)];
            break;
            case 'deleteDayPlanByGoalID':
                $goalId = $_POST["GoalID"];
                $result = $daldayplan->deletebyGoalID($goalId);
                $message = ["message" => json_encode($result)];
                break;
        case 'updateDayPlan':
            $dayPlanId = $_POST["DayPlanID"];
            $goalId = $_POST["GoalID"];
            $date = $_POST["Date"];
            $notes = $_POST["Notes"];
            $status = $_POST["Status"];
            $progress = $_POST["Progress"];
            $result = $daldayplan->update($dayPlanId, $goalId, $date, $notes, $status, $progress);
            $message = ["message" => json_encode($result)];
            break;
        case 'updateProgressDayPlan':
            $dayPlanId = $_POST["DayPlanID"];
            $progress = $_POST["Progress"];
            $result = $daldayplan->updateProgress($dayPlanId, $progress);
            $message = ["message" => json_encode($result)];
            break;
        case 'getAverageProgressByGoalID':
            $goalid = $_POST["GoalID"];
            $jsonResult = $daldayplan->getAverageProgressByGoalID($goalid);
            $message = ["message" => json_encode($jsonResult)];
            break;
        /**Task DAL */
        case 'getAllTasks':
            $dayPlanId = $_POST["DayPlanID"];
            $message = $dalTask->getAll($dayPlanId);
            break;
        case 'insertTask':
            $dayPlanId = $_POST["DayPlanID"];
            $title = $_POST["Title"];
            $description = $_POST["Description"];
            $priority = $_POST["Priority"];
            $status = $_POST["Status"];
            $progress = $_POST["Progress"];
            $startTime = $_POST["StartTime"];
            $endTime = $_POST["EndTime"];
            $result = $dalTask->insert($dayPlanId, $title, $description, $priority, $status, $progress, $startTime, $endTime);
            $message = ["message" => json_encode($result)];
            break;
        case 'deleteTask':
            $taskId = $_POST["TaskID"];
            $result = $dalTask->delete($taskId);
            $message = ["message" => json_encode($result)];
            break;
        case 'updateTask':
            $taskId = $_POST["TaskID"];
            $dayPlanId = $_POST["DayPlanID"];
            $title = $_POST["Title"];
            $description = $_POST["Description"];
            $priority = $_POST["Priority"];
            $status = $_POST["Status"];
            $progress = $_POST["Progress"];
            $startTime = $_POST["StartTime"];
            $endTime = $_POST["EndTime"];
            $result = $dalTask->update($taskId, $dayPlanId, $title, $description, $priority, $status, $progress, $startTime, $endTime);
            $message = ["message" => json_encode($result)];
            break;
        case 'deleteTaskByDayplanID':
            $dayPlanId = $_POST["DayPlanID"];
            $result = $dalTask->deletebyDayplanID($dayPlanId);
            $message = ["message" => json_encode($result)];
            break;
        case 'AverageProgressByDayPlanID':
            $dayPlanId = $_POST["DayPlanID"];
            $jsonResult = $dalTask->getAverageProgressByDayPlanID($dayPlanId);
            $message = ["message" => json_encode($jsonResult)];
            break;
        /**Follwer DAL */
        case 'getAllFollowers':
            $goalId = $_POST["GoalID"];
            $message = $dalfollwer->getAll($goalId);
            break;
        case 'insertFollower':
            $goalId = $_POST["GoalID"];
            $userId = $_POST["UserID"];
            $followedAt = $_POST["FollowedAt"];
            $result = $dalfollwer->insert($goalId, $userId, $followedAt);
            $message = ["message" => json_encode($result)];
            break;
        case 'deleteFollower':
            $followerId = $_POST["FollowerID"];
            $result = $dalfollwer->delete($followerId);
            $message = ["message" => json_encode($result)];
            break;
            case 'copyGoalDayPlanTask':
                // Lấy thông tin GoalID và UserID từ POST
                $goalId = $_POST["GoalID"];
                $userId = $_POST["UserID"];  
            
                // Gọi hàm copyGoalDayPlanTask
                $result = $dalGoal->copyGoalDayPlanTask($goalId, $userId);
            
                // Trả về kết quả dưới dạng JSON
                $message = ["message" => $result ? "true" : "fasle"];
                echo json_encode($message);
                break;
      case 'checkGoalAndUserExist':
                $goalId = $_POST["GoalID"];
                $userId = $_POST["UserID"];
                $result = $dalfollwer->checkGoalAndUserExist($goalId, $userId);
                $message = ["message" => $result ? "true" : "fasle"];
                echo json_encode($message);
                break;          
    default:
        $message = ["message" => "Unknown method" . $_POST["action"]];
        break;
}

header('Content-type: application/json; charset=utf-8');

ob_clean();

echo json_encode($message);