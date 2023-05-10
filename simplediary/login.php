<?php

/*
 * Name:    Brandon Mitchell
 * Description: Checks if the login info is valid and logs the user in if
 *              possible.  Also updates logon history.
 */

require_once("constants.php");

// Prevents log in attempts from other sites
if (!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER']) ||
    parse_url($_SERVER['HTTP_REFERER'])['host'] !== $_SERVER['HTTP_HOST'])
{
    header("location: .");
}

// Sent a form with missing data
else if (empty($_POST['user']) || empty($_POST['password']))
{
    header("location: .?missingLoginInfo");
}

// Values pass are larger than they should be able to input
else if (strlen($_POST['user']) > 20 || strlen($_POST['password']) > 20)
{
    header("location: .?logonInfoLength");
}

else
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    $username = $_POST['user'];
    $password = $_POST['password'];

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error)
    {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Create a query statement resource
    $stmt = $conn->prepare("SELECT password FROM tbl_users WHERE username = ?;");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0)
    {
        $row = $result->fetch_assoc();
        
        $datetime = date("Y-m-d H:i:s");
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $status = "";
        
        if (password_verify($password, $row['password']))
        {
            // Correct password
            $numLogUpdate = $conn->prepare("UPDATE tbl_users SET num_logons = num_logons + 1, last_successful_logon = ? WHERE username = ?;");
            $numLogUpdate->bind_param('ss', $datetime, $username);
            $numLogUpdate->execute();
            $numLogUpdate->close();
            
            $status = "SUCCESS";
            
            session_start();
            $_SESSION['username'] = $username;
            
            header("location: entries.php");
        }
        
        else
        {
            // Incorrect password
            $numLogUpdate = $conn->prepare("UPDATE tbl_users SET last_unsuccessful_logon = ? WHERE username = ?;");
            $numLogUpdate->bind_param('ss', $datetime, $username);
            $numLogUpdate->execute();
            $numLogUpdate->close();
            
            $status = "FAILURE";
            
            header("location: .?loginFailed");
        }
    
        $logonInfo = $conn->prepare("INSERT INTO tbl_logon_attempts (username, attempt_datetime, status, ipaddress, user_agent) VALUES (?, ?, ?, ?, ?);");
        $logonInfo->bind_param('sssss', $username, $datetime, $status, $ipAddress, $userAgent);
        $logonInfo->execute();
        $logonInfo->close();
    }
    else
    {
        // No user with that name, so can't enter anything in the database
        header("location: .?loginFailed");
    }

    $result->close();
    $stmt->close();
    $conn->close();
}

?>