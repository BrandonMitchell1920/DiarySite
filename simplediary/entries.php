<link rel="stylesheet" href="style.css">

<?php

/*
 * Name:    Brandon Mitchell
 * Description: Formats and displays the user's entries.  Also provides a form
 *              for them to submit feedback.
 */

session_start();

// Check they are logged in and if not, redirect to the login page
if (!isset($_SESSION['username']))
{
    // Include a additional item in the url to give a special message
	header('location: .?notLoggedIn');
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

    $stmt = $conn->prepare("SELECT * FROM tbl_diary_entries WHERE username = ?;");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $espName = htmlentities(stripcslashes($username));
    echo "<h2>$espName's Diary</h2>";

    if ($result->num_rows > 0) 
    {
        // Can only delete if confirmation key is set and matches, prevents 
        // cross site request forgery, maybe not critial for this site
        $confirm = uniqid(rand(), true);
        $_SESSION['confirmationKey'] = $confirm;
        
        // Output data of each row
        while ($row = $result->fetch_assoc()) 
        {
            $datetime = $row["entry_datetime"];
            $entry = htmlentities(stripcslashes($row["entry"]));
            
            // Each entry is in its own little box, form is for a image button 
            // that submits via post
            echo <<<END
            <div class="roundBox"><p style="overflow-wrap: break-word;">$entry</p>
                <span class="date">
                    <form style='all: unset; float: right; margin-left: 10px;' action='deleteEntries.php' method='POST' 
                        onsubmit='return window.confirm("Are you sure you want to delete this post?");' >
                        <input type='hidden' name='username' value="$username" /> 
                        <input type='hidden' name='datetime' value="$datetime" /> 
                        <input type='hidden' name='confirm' value="$confirm" />
                        <input type='hidden' name='entry' value="$entry" />
                        <input type='image' src='./img/trash.png' width='20' alt='Submit' />
                    </form>
                    <p>Posted: $datetime</p>
                </span>
            </div><br>
END;
        }
    } 
    else 
    {
        echo '<div class="roundBox">No entries yet!  Create one below!</div>';
    }

    $result->close();
    $stmt->close();
    $conn->close();
}

?>

<form action="insertEntry.php" method="post">
    <?php
        // Display error message to user in box
        if (isset($_REQUEST['missingEntry']))
        {
            echo '<p class="error">Please fill out the entry field.</p>';
        }
        else if (isset($_REQUEST['entryTooLong']))
        {
            echo '<p class="error">Diary entries limited to 255 characters.</p>';
        }
        else if (isset($_REQUEST['incorrectKey']))
        {
            echo '<p class="error">Invalid confimation key.</p>';
        }
    ?>
    Submit Diary Entry:<br /><br /><textarea placeholder="Tell us about your day!" name="entry" 
        rows="6" cols="34" maxlength="255" required ></textarea><br />
    <input type="submit" value="Submit" />
</form>

<a href="accountInfo.php">
    <button class="topButton secondButton">Account Info</button>
</a>

<!-- Logout button, uses CSS for positioning -->
<a href="logout.php">
    <button class="topButton">Logout</button>
</a>