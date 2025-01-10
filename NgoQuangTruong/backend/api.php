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

switch($_GET["action"])
{
    case 'getallUser':
        $message = $dalUser->getall();
        break;
    case 'insertUser':
        $name = $_GET["Name"];
        $email = $_GET["Email"];
        $password = $_GET["Password"];
        $Role = $_GET["Role"];
        $result = $dalUser->insert($name,$email,$password,$Role);
        $message = ["message" => json_encode($result)];
        break;
    case 'deleteUser':
        $userId = $_GET["UserID"];
        $result = $dalUser->delete($userId);
        $message = ["message" => json_encode($result)];
        break;
    case 'updateUser':
        $userId = $_GET["UserID"];
        $name = $_GET["Name"];
        $email = $_GET["Email"];
        $password = $_GET["Password"];
        $Role = $_GET["Role"];
        $result = $dalUser->update($userId,$name,$email,$password,$Role);
        $message = ["message" => json_encode($result)];    
        break;
    case 'getUser':
        $Name = $_GET["Email"];
        $message = $dalUser->getUser($Name);
        break;

    /**Goals DAL */
    case 'getAllGoals':
        $userId = $_GET["UserID"];
        $message = $dalGoal->getAll($userId);
        break;
    
    case 'getGoalPublic':
        $message = $dalGoal->getgoalPublic();
        break;
    case 'insertGoal':
        $userId = $_GET["UserID"];
        $goalTitle = $_GET["GoalTitle"];
        $description = $_GET["Description"];
        $targetDate = $_GET["TargetDate"];
        $visibility = $_GET["Visibility"];
        $status = $_GET["Status"];
        $progress = $_GET["Progress"];
        $result = $dalGoal->insert($userId, $goalTitle, $description, $targetDate, $visibility, $status, $progress);
        $message = ["message" => json_encode($result)];
        break;
    case 'deleteGoal':
        $goalId = $_GET["GoalID"];
        $result = $dalGoal->delete($goalId);
        $message = ["message" => json_encode($result)];
        break;
    case 'updateGoal':
        $goalId = $_GET["GoalID"];
        $userId = $_GET["UserID"];
        $goalTitle = $_GET["GoalTitle"];
        $description = $_GET["Description"];
        $targetDate = $_GET["TargetDate"];
        $visibility = $_GET["Visibility"];
        $status = $_GET["Status"];
        $progress = $_GET["Progress"];
        $result = $dalGoal->update($goalId, $userId, $goalTitle, $description, $targetDate, $visibility, $status, $progress);
        $message = ["message" => json_encode($result)];
        break;

        /** DayPlan DAL */
        case 'getAllDayPlans':
            $goalId = $_GET["GoalID"];
            $message = $daldayplan->getAll($goalId);
            break;
        case 'insertDayPlan':
            $goalId = $_GET["GoalID"];
            $date = $_GET["Date"];
            $notes = $_GET["Notes"];
            $status = $_GET["Status"];
            $progress = $_GET["Progress"];
            $result = $daldayplan->insert($goalId, $date, $notes, $status, $progress);
            $message = ["message" => json_encode($result)];
            break;
        case 'deleteDayPlan':
            $dayPlanId = $_GET["DayPlanID"];
            $result = $daldayplan->delete($dayPlanId);
            $message = ["message" => json_encode($result)];
            break;
        case 'updateDayPlan':
            $dayPlanId = $_GET["DayPlanID"];
            $goalId = $_GET["GoalID"];
            $date = $_GET["Date"];
            $notes = $_GET["Notes"];
            $status = $_GET["Status"];
            $progress = $_GET["Progress"];
            $result = $daldayplan->update($dayPlanId, $goalId, $date, $notes, $status, $progress);
            $message = ["message" => json_encode($result)];
            break;
        /**Task DAL */
        case 'getAllTasks':
            $dayPlanId = $_GET["DayPlanID"];
            $message = $dalTask->getAll($dayPlanId);
            break;
        case 'insertTask':
            $dayPlanId = $_GET["DayPlanID"];
            $title = $_GET["Title"];
            $description = $_GET["Description"];
            $priority = $_GET["Priority"];
            $status = $_GET["Status"];
            $progress = $_GET["Progress"];
            $startTime = $_GET["StartTime"];
            $endTime = $_GET["EndTime"];
            $result = $dalTask->insert($dayPlanId, $title, $description, $priority, $status, $progress, $startTime, $endTime);
            $message = ["message" => json_encode($result)];
            break;
        case 'deleteTask':
            $taskId = $_GET["TaskID"];
            $result = $dalTask->delete($taskId);
            $message = ["message" => json_encode($result)];
            break;
        case 'updateTask':
            $taskId = $_GET["TaskID"];
            $dayPlanId = $_GET["DayPlanID"];
            $title = $_GET["Title"];
            $description = $_GET["Description"];
            $priority = $_GET["Priority"];
            $status = $_GET["Status"];
            $progress = $_GET["Progress"];
            $startTime = $_GET["StartTime"];
            $endTime = $_GET["EndTime"];
            $result = $dalTask->update($taskId, $dayPlanId, $title, $description, $priority, $status, $progress, $startTime, $endTime);
            $message = ["message" => json_encode($result)];
            break;
        /**Follwer DAL */
        case 'getAllFollowers':
            $goalId = $_GET["GoalID"];
            $message = $dalfollwer->getAll($goalId);
            break;
            case 'getAllFollowersUser':
                $userId = $_GET["UserID"];
                $message = $dalfollwer->getAllUser($userId);
                break;    
        case 'insertFollower':
            $goalId = $_GET["GoalID"];
            $userId = $_GET["UserID"];
            $followedAt = $_GET["FollowedAt"];
            $result = $dalfollwer->insert($goalId, $userId, $followedAt);
            $message = ["message" => json_encode($result)];
            break;
        case 'deleteFollower':
            $followerId = $_GET["FollowerID"];
            $result = $dalfollwer->delete($followerId);
            $message = ["message" => json_encode($result)];
            break;
            case 'getGoalsOverview':
                $user_id = $_GET["UserID"];
                $overview = $dalGoal->getGoalsOverview($user_id);
                $message = ["goalsOverview" => $overview];
                break;
        
        
            case 'getTasksOverview':
                $user_id = $_GET["UserID"];
                $overview = $dalTask->getTasksOverview($user_id); 
                $message = ["tasksOverview" => $overview];
                break;
    case 'getGoalsOverview':
        $user_id = $_GET["UserID"];
        $overview = $dalGoal->getGoalsOverview($user_id); 
        $message = ["goalsOverview" => $overview];
        break;
        
    default:
        $message = ["message" => "Unknown method" . $_GET["action"]];
        break;
}

header('Content-type: application/json; charset=utf-8');

ob_clean();

echo json_encode($message);