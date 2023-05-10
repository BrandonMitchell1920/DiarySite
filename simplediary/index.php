<?php

/*
 * Name:    Brandon Mitchell
 * Description: Landing page of the site.  Allows the user to logon or go to
 *              the create a new user page if the token is correct.
 */

?>

<html>
<head>
	<link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Login form, uses PHP to display error messages -->
	<form action="login.php" method="post" autocomplete="off">
		<?php
			if (isset($_REQUEST['notLoggedIn']))
			{
				echo '<p class="error">Please login to access the website.</p>';
			}
			else if (isset($_REQUEST['missingLoginInfo']))
			{
				echo '<p class="error">All fields are required.</p>';
			}
            else if (isset($_REQUEST['logonInfoLength']))
			{
				echo '<p class="error">Fields are limited to 20 characters.</p>';
			}
            else if (isset($_REQUEST['loginFailed']))
			{
				echo '<p class="error">Invalid username or password is incorrect.</p>';
			}
		?>
		Returning Users <br /><br />
		Username: <input type="text" name="user" maxlength="20" required /><br /><br />
		Password: <input type="password" name="password" maxlength="20" required /><br /><br />
		<div style="text-align: center">
			<input type="submit" value="Login" />
		</div>
	</form>
	
    <!-- Sign up form, uses PHP to display error messages -->
	<form action="newUserForm.php" method="post" autocomplete="off">
		<?php
			if (isset($_REQUEST['nameUsed']))
			{
				echo '<p class="error">That name is already in use.</p>';
			}
			else if (isset($_REQUEST['pwdMismatch']))
			{
				echo '<p class="error">Passwords don\'t match.</p>';
			}
			else if (isset($_REQUEST['incorrectToken']))
			{
				echo '<p class="error">Incorrect Token.</p>';
			}
            else if (isset($_REQUEST['missingCreateInfo']))
			{
				echo '<p class="error">All fields in form must be filled.</p>';
			}
            else if (isset($_REQUEST['createInfoLength']))
			{
				echo '<p class="error">Fields are too long.</p>';
			}
            else if (isset($_REQUEST['nameUsed']))
			{
				echo '<p class="error">That name is already in use.</p>';
			}
			else if (isset($_REQUEST['success']))
			{
				echo '<p class="success">Success!  Log in above.</p>';
			}
		?>
		New Users Sign-Up <br /><br />
        Secret Token: <input type="text" name="token" maxlength=20 required /><br /><br />
		<div style="text-align: center">
			<input type="submit" value="Start Sign-Up" />
		</div>
	</form>
</body>
</html>