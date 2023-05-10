<?php

/*
 * Name:    Brandon Mitchell
 * Description: This page handles inserting the diary entry into the datebase.
 *              It then redirects them back to the diary page so it looks like 
 *              their post appears instantly.
 */
 
session_start();

// Make sure any user accessing this page is already logged in and that they 
// don't navigate to it some other way
if (!isset($_SESSION['username']))
{
	header('location: .?notLoggedIn');
}

else if (empty($_POST['entry']))
{
    header("location: entries.php?missingEntry");
}

else if (strlen($_POST['entry']) > 255)
{
    header("location: entries.php?entryTooLong");
}

else
{
    require_once("constants.php");
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error)
    {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    $username = $_SESSION['username'];
    $datetime = date("Y-m-d H:i:s");
    $entry = $_POST['entry'];
    
	$stmt = $conn->prepare("INSERT INTO tbl_diary_entries (username, entry_datetime, entry) VALUES (?, ?, ?);");
    $stmt->bind_param('sss', $username, $datetime, $entry);
    $stmt->execute();

    $stmt->close();
    $conn->close();
    
    // Refresh the page so the feedback shows up
    header("location: entries.php");
}

?>