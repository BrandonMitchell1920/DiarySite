<?php

/*
 * Name:    Brandon Mitchell
 * Description: This page validates the user's entries and creates a new user
 *              in the database if it can.
 */
 
require_once("constants.php");
 
 // Prevents creation attempts from other sites
if (!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER']) ||
    parse_url($_SERVER['HTTP_REFERER'])['host'] !== $_SERVER['HTTP_HOST'])
{
    header("location: .");
}

// Make sure all fields were filled
elseif (empty($_POST['username']) || empty($_POST['firstName']) || 
    empty($_POST['middleInitial']) || empty($_POST['lastName']) ||
    empty($_POST['password1']) || empty($_POST['password2']) || empty($_POST['token']))
{
    header('location: .?missingCreateInfo');
}

// Verify form values of correct length
elseif (strlen($_POST['username']) > 20 || strlen($_POST['firstName']) > 20 || 
    strlen($_POST['middleInitial']) > 20 || strlen($_POST['lastName']) > 20 ||
    strlen($_POST['password1']) > 20 || strlen($_POST['password2']) > 20)
{
    header('location: .?createInfoLength');
}

// Token is incorrect
elseif (strcmp($_POST['token'], TOKEN) !== 0)
{
    header('location: .?incorrectToken');
}
    
// Passwords aren't the same
elseif (strcmp($_POST['password1'], $_POST['password2']) !== 0)
{
    header('location: .?pwdMismatch');
}
    
else
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    $username = $_POST['username'];
    $password = password_hash($_POST['password1'], PASSWORD_DEFAULT);
    $firstName = $_POST['firstName'];
    $middleInitial = $_POST['middleInitial'];
    $lastName = $_POST['lastName'];
    
    if (empty($middleInitial))
    {
        $middleInitial = null;
    }

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) 
    {
        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = $conn->prepare("SELECT username FROM tbl_users WHERE username = ?;");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Above query will return results if a name already exists
    if ($result->num_rows > 0)
    {
        header("location: .?nameUsed");
        exit();
    }
    
    $result->close();

    $stmt = $conn->prepare("INSERT INTO tbl_users (username, password, first_name, middle_initial, last_name) VALUES (?, ?, ?, ?, ?);");
    $stmt->bind_param('sssss', $username, $password, $firstName, $middleInitial, $lastName);
    $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    header("location: .?success");
}

?>