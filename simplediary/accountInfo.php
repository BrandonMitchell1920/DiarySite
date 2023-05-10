<link rel="stylesheet" href="style.css">

<?php

/*
 * Name:    Brandon Mitchell
 * Description: Creates the page that show cases the user's account info.  This
 *              is mostly their logon history and similar things.
 */

session_start();

// Check they are logged in and if not, redirect to the login page
if (!isset($_SESSION['username']))
{
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

    // Fetch user information such as last logon, name
    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE username = ?;");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $userRow = $result->fetch_assoc();
    
    $result->close();
    $stmt->close();
    
    // Fetch all logon attemps associated with the given user
    $stmt = $conn->prepare("SELECT * FROM tbl_logon_attempts WHERE username = ?;");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $stmt->close();
    
    // From user, can't trust
    $firstName = htmlentities(stripcslashes($userRow['first_name']));
    $middleIntitial = htmlentities(stripcslashes($userRow['middle_initial']));
    $lastName = htmlentities(stripcslashes($userRow['last_name']));
    
    // Generated from my scripts, can trust
    $success = $userRow['last_successful_logon'];
    $unsuccess = $userRow['last_unsuccessful_logon'];
    $numLogons = $userRow['num_logons'];
    
    $escName = htmlentities(stripcslashes($username));
    
    // Print out tables containing user info
    echo <<<END
    <h2>$escName's Account Info</h2>
    <div class='roundbox'>
    <table>
        <tr>
            <th>First Name</th>
            <th>Middle Initial</th>
            <th>Last Name</th>
        </tr>
        <tr>
            <td>$firstName</th>
            <td>$middleIntitial</th>
            <td>$lastName</th>
        </tr>
    </table>
    <br>
    <table>
        <tr>
            <th>Last Sucessful Attempt</th>
            <th>Last Unsucessful Attempt</th>
            <th>Number of Logons</th>
        </tr>
         <tr>
            <td>$success</th>
            <td>$unsuccess</th>
            <td>$numLogons</th>
        </tr>
    </table>
    <br>
    <table>
        <tr>
            <th>Attempt DateTime</th>
            <th>Status</th>
            <th>IP Address</th>
            <th>User Agent</th>
        </tr>
END;
    
    while($row = $result->fetch_assoc()) 
    {        
        $attempt = $row['attempt_datetime'];
        $status = $row['status'];
        $ipAddress = $row['ipaddress'];
        $userAgent = $row['user_agent'];
        
        echo <<<END
            <tr>
                <td>$attempt</th>
                <td>$status</th>
                <td>$ipAddress</th>
                <td>$userAgent</th>
            </tr>
END;
    }
    
    echo "</table></div>";
    
    $result->close();
    $conn->close();
}

?>

<a href="entries.php">
	<button class="topButton secondButton">Diary Entries</button>
</a>

<!-- Logout button, uses CSS for positioning -->
<a href="logout.php">
	<button class="topButton">Logout</button>
</a>