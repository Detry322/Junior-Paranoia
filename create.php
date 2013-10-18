<?php
	$email = "";
	$cfmemail = "";
	$password = "";
	$cfmpassword ="";
	$error = "";

	if (isset($_GET['error']))
	{
		switch ($_GET['error']) {
			case 1:
				$error = "passwords do not match";
				$password = "bad";
				$cfmpassword = "bad";
				break;
			case 2:
				$error = "emails do not match";
				$email = "bad";
				$cfmemail = "bad";
				break;
			case 3:
				$error = "invalid email";
				$email = "bad";
				break;
			case 4:
				$error = "must fill in all fields";
				break;
			case 5:
				$error = "email already in use";
				$email = "bad";
				break;
			default:
				break;
		}
	}

?>
<html>
<head>
	<title>Junior Paranoia</title>
	<link rel="icon" type="image/png" href="icon.png" />
	<link rel="apple-touch-icon-precomposed" href="icon.png" />
	<link rel="stylesheet" type="text/css" href="create.css" />
</head>
<body>
	<div id="wrapper">
	<a href="/paranoia"><img src="header.png" width=666 height=157 /><br /></a>
	<div id="createbox" class="box">
		create a junior paranoia 2012 account<hr />
		<span class="note">all fields required, must be valid for eligibility<br /><br /></span>
		<form action="action.php?method=create" method="POST">
			<div id="labels">
				<label class="" for="first">first name:</label><br />
				<label class="" for="last">last name:</label><br />
				<label class=<?php echo "\"".$email."\""; ?> for="email">email:</label><br />
				<label class=<?php echo "\"".$cfmemail."\""; ?> for="emailcon">confirm email:</label><br />
				<label class="" for="address">address *:</label><br />
				<label class="" for="city">city:</label><br />
				<label class=<?php echo "\"".$password."\""; ?>  for="password">password ^:</label><br />
				<label class=<?php echo "\"".$cfmpassword."\""; ?>  for="passwordcon">confirm password:</label><br />
			</div>
			<div id="fields">
				<input type="text" name="first" id="first" /><br />
				<input type="text" name="last" id="last" /><br />
				<input type="text" name="email" id="email" /><br />
				<input type="text" name="emailcon" id="emailcon" /><br />
				<input type="text" name="address" id="address" /><br />
				<select name="city" id="city">
					<option value="Glencoe">Glencoe</option>
					<option value="Winnetka">Winnetka</option>
					<option value="Wilmette">Wilmette</option>
					<option value="Kenilworth">Kenilworth</option>
					<option value="Northfield">Northfield</option>
					<option value="Glenview">Glenview</option>
				</select><br />
				<input type="password" name="password" id="password" /><br />
				<input type="password" name="passwordcon" id="passwordcon" /><br />
			</div>
			<div id="submitbutton">
				<span class="note"><br />* address only visible to opposing team's captain<br />^ must be at least 6 characters<br /></span>
				<span class="note bad"><?php echo $error; ?> <br /></span>
				<hr />
				<input type="submit" value="create account" id="submit" />
			</div>
		</form>
	</div>
</body>
</html>