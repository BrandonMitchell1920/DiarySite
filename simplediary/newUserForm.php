<?php

/*
 * Name:    Brandon Mitchell
 * Description: Simple form so person can create a user.  Requires a token to 
 *              access.
 */

require_once("constants.php");

if (!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER']) ||
    parse_url($_SERVER['HTTP_REFERER'])['host'] !== $_SERVER['HTTP_HOST'])
{
    header("location: .");
}

else if (strcmp($_POST['token'], TOKEN) !== 0)
{
    header('location: .?incorrectToken');
}

?>

<html>
<head>
	<link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="createNewUser.php" method="post" autocomplete="off">
		New Users Sign-Up <br /><br />
		Username: <input type="text" name="username" maxlength="20" required /><br /><br />
        First Name: <input type="text" name="firstName" maxlength="20" required /><br /><br />
        Middle Initial: <input type="text" name="middleInitial" maxlength="1" /><br /><br />
        Last Name: <input type="text" name="lastName" maxlength="20" required /><br /><br />
		Password: <input type="password" name="password1" maxlength="20" required /><br /><br />
		Confirm Password: <input type="password" name="password2" maxlength="20" required /><br /><br />
		<input type="hidden" name="token" maxlength="20" value=<?php echo $_POST['token'] ?> required />
		<div style="text-align: center">
			<input type="submit" value="Sign-Up" />
		</div>
	</form>
    
</body>
</html>