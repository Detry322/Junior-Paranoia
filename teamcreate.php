<?php session_start();

if (!isset($_SESSION['playerid']))
{
	header("Location: http://ifiwereblank.com/paranoia/");
	die();
}
$error = "";
if (isset($_GET['error']))
	if ($_GET['error'] == 1)
		$error = "player id doesn't exist";
	else if ($_GET['error'] == 2)
		$error = "player is already on a team";
	else if ($_GET['error'] == 3)
		$error = "you added yourself as a player!";
	else if ($_GET['error'] == 4)
		$error = "you didn't enter an name!";
	else if ($_GET['error'] == 5)
		$error = "duplicate player!";
	else if ($_GET['error'] == 6)
		$error = "you're already on a team!";
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
		create a team<hr />
		<span class="note bad"><?php echo $error; ?><br /></span>
		<form action="action.php?method=teamcreate" method="POST">
			<div id="labels">
				<label class="" for="teamname">team name:</label><br />
				<label class="">captain:</label><br />
				<label class="" for="player2">player 2 id*:</label><br />
				<label class="" for="player3">player 3 id*:</label><br />
				<label class="" for="player4">player 4 id*:</label><br />
				<label class="" for="player5">player 5 id*:</label><br />
			</div>
			<div id="fields">
				<input type="text" name="teamname" id="teamname" /><br />
				<?php echo $_SESSION['first']." ".$_SESSION['last']; ?><br />
				<input type="text" name="player2" id="player2" /><br />
				<input type="text" name="player3" id="player3" /><br />
				<input type="text" name="player4" id="player4" /><br />
				<input type="text" name="player5" id="player5" />
			</div>
			<div id="submitbutton">
				<span class="note"><br />* optional<br /></span>
				<hr />
				<input type="submit" value="create team" id="submit" />
			</div>
		</form>
	</div>
</body>
</html>