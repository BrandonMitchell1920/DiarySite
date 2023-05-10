<?php

/*
 * Name:    Brandon Mitchell
 * Description:     This page handles delete requests for entries.  A 
 *                  confirmation key is used for extra security, but probably
 *                  isn't needed with a site like this.
 */
 
session_start();

// Make sure the user is logged in
if (!isset($_SESSION['username']))
{
	header('location: .?notLoggedIn');
}

else if (!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER']) ||
    parse_url($_SERVER['HTTP_REFERER'])['host'] !== $_SERVER['HTTP_HOST'])
{
    header("location: entries.php");
}

// Checks that the confirmation key was generated
else if (!isset($_POST['confirm'], $_SESSION['confirmationKey']) || 
    strcmp($_POST['confirm'], $_SESSION['confirmationKey']) !== 0)
{
    unset($_SESSION['confirmationKey']);
    header("location: entries.php?incorrectKey");
}

else
{
    unset($_SESSION['confirmationKey']);
    
    require_once("constants.php");
        
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error)
    {
        die("Connection failed: " . mysqli_connect_error());
    }
	
    $username = $_POST['username'];
    $datetime = $_POST['datetime'];
    $entry = $_POST['entry'];
    
    // Ideally would just have a primary key assocated with all diary entries so I 
    // wouldn't need such a complicated delete statement, also prevent multiple, accidental deletions
	$stmt = $conn->prepare("DELETE FROM tbl_diary_entries WHERE username = ? AND entry_datetime = ? AND entry = ?;");
    $stmt->bind_param("sss", $username, $datetime, $entry);
    $stmt->execute();
	
	$stmt->close();
    $conn->close();

    // Bring them back to the original page
    header("location: entries.php");
}

?>