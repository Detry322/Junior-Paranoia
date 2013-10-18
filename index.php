<?php 
include 'meekrodb.2.0.class.php';
session_start();

DB::$user = 'paranoia';
DB::$password = 'password';
DB::$dbName = 'paranoia';
DB::$host = 'localhost'; //defaults to localhost if omitted
DB::$port = '3306'; // defaults to 3306 if omitted
DB::$encoding = 'utf8';

if(isset($_SESSION['playerid']))
{
	$me = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$_SESSION['playerid']);
	$team = "";
	?>
<html>
<head>
	<title>Junior Paranoia 2013</title>
	<link rel="stylesheet" type="text/css" href="home.css" />
	<link rel="icon" type="image/png" href="icon.png" />
	<link rel="apple-touch-icon-precomposed" href="icon.png" />
</head>
<body>
	<div id="wrapper">
	<a href="/paranoia"><img src="header.png" width=666 height=157 /></a><br />
	<div id="playerinfo" class="box">
		welcome back, agent <?php echo stripslashes($_SESSION['last']); 
		
		if ($_SESSION['playerid'] == 9)
		{
			echo "<br /><span class=\"note bad\" style=\"font-size: 10em;\">You are banned from paranoia</span>";
		}
		?>
	</div><br />
	<a href="teams.php" class="info box">
		teams/info
	</a>
	<a class="info box" href="http://ifiwereblank.com/?cat=3" >
		announcements
	</a>
	<a class="info box" href="rules.html">
		rules
	</a>
	<a class="info box" href="https://www.facebook.com/groups/114173765401642/">
		facebook
	</a>
	<a class="info box" href="http://challonge.com/juniorparanoia2013finals/" >
		schedule
	</a>
	<?php
	$lolwat = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i",$me['team']);
	if ($lolwat['captain'] == $me['id'])
	{
		?>
		<a class="info box" href="captain.php">captain's corner</a>
		<?php
	}
	if ($me['commissioner'] == 'yes')
	{
		?>
		<a class="info box" href="admin/commissioner.html">Commissioner's Panel</a>
		<?php
	}
	if ($me['verified'] != 'yes')
	{
		?>
		<div id="overview" class="box">
		your account has not yet been verified.<hr />
		<span class="medium">please wait <b>24 hours</b> for your account to be activated<br /><br />
			Your account must have a valid address<br />
			You must have supplied a valid name.<br /><br />
			please contact the commissioner if you account does not become active.<br />
			you cannot be added to a team until your account is active.
		</span>
		<?php
	}
	else if ($me['team'] == -1)
	{
		?>
		<div id="overview" class="box">
		you are not currently on a team.<hr />
		<span class="medium"><a href="teamcreate.php">create your own</a><br />OR<br />give your player number to a captain.<br />
		<i>your player number is <b><?php echo $me['id']; ?></b></i></span>
		<?php
	} else {
		$team = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i",$me['team']);
		?>
		<div id="overview" class="box">
		<?php 
			echo "<span><a href=\"team.php?id=".$team['id']."\">".stripslashes($team['name'])."</a></span>";
			if ($team['paid'] != 'yes')
				echo "<span class=\"bad\"><br />Your team captain has not paid the required fee of $25 to play in paranoia. In order to be eligible, notify your captain.</span>";
		?><hr />
		<div class="teamparts">
			<b>Captain:</b><br />
			Player 2:<br />
			Player 3:<br />
			Player 4:<br />
			Player 5:<br />
		</div>
		<div class="players">
			<b><?php
				if ($team['captain'] == -1)
					echo "<b><span class=\"open\">Open slot</span></b>";
				else
				{
					$player = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$team['captain']);
					switch ($player['dead']) {
						case 'no':
							echo "<span class=\"alive\"><a href=\"player.php?id=".$team['captain']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
						
						default:
							echo "<span class=\"dead\"><a href=\"player.php?id=".$team['captain']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
					}
				}
			?></b><br />
			<?php
				if ($team['player2'] == -1)
					echo "<b><span class=\"open\">Open slot</span></b>";
				else
				{
					$player = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$team['player2']);
					switch ($player['dead']) {
						case 'no':
							echo "<span class=\"alive\"><a href=\"player.php?id=".$team['player2']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
						
						default:
							echo "<span class=\"dead\"><a href=\"player.php?id=".$team['player2']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
					}
				}
			?><br />
			<?php
				if ($team['player3'] == -1)
					echo "<b><span class=\"open\">Open slot</span></b>";
				else
				{
					$player = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$team['player3']);
					switch ($player['dead']) {
						case 'no':
							echo "<span class=\"alive\"><a href=\"player.php?id=".$team['player3']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
						
						default:
							echo "<span class=\"dead\"><a href=\"player.php?id=".$team['player3']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
					}
				}
			?><br />
			<?php
				if ($team['player4'] == -1)
					echo "<b><span class=\"open\">Open slot</span></b>";
				else
				{
					$player = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$team['player4']);
					switch ($player['dead']) {
						case 'no':
							echo "<span class=\"alive\"><a href=\"player.php?id=".$team['player4']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
						
						default:
							echo "<span class=\"dead\"><a href=\"player.php?id=".$team['player4']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
					}
				}
			?><br />
			<?php
				if ($team['player5'] == -1)
					echo "<b><span class=\"open\">Open slot</span></b>";
				else
				{
					$player = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$team['player5']);
					switch ($player['dead']) {
						case 'no':
							echo "<span class=\"alive\"><a href=\"player.php?id=".$team['player5']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
						
						default:
							echo "<span class=\"dead\"><a href=\"player.php?id=".$team['player5']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
					}
				}
			?><br />
		</div>
		<div class="clear"></div>
		<?php
		if ($team['opponent'] != -1)
		{
			$opteam = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i",$team['opponent']);
			?>
				<hr />
				<?php echo "<span><a href=\"team.php?id=".$opteam['id']."\">".stripslashes($opteam['name'])."</a></span>"; ?>
				<hr />
		<div class="teamparts">
			<b>Captain:</b><br />
			Player 2:<br />
			Player 3:<br />
			Player 4:<br />
			Player 5:<br />
		</div>
		<div class="players">
			<b><?php
				if ($opteam['captain'] == -1)
					echo "<b><span class=\"open\">Open slot</span></b>";
				else
				{
					$player = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$opteam['captain']);
					switch ($player['dead']) {
						case 'no':
							echo "<span class=\"alive\"><a href=\"player.php?id=".$opteam['captain']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
						
						default:
							echo "<span class=\"dead\"><a href=\"player.php?id=".$opteam['captain']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
					}
				}
			?></b><br />
			<?php
				if ($opteam['player2'] == -1)
					echo "<b><span class=\"open\">Open slot</span></b>";
				else
				{
					$player = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$opteam['player2']);
					switch ($player['dead']) {
						case 'no':
							echo "<span class=\"alive\"><a href=\"player.php?id=".$opteam['player2']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
						
						default:
							echo "<span class=\"dead\"><a href=\"player.php?id=".$opteam['player2']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
					}
				}
			?><br />
			<?php
				if ($opteam['player3'] == -1)
					echo "<b><span class=\"open\">Open slot</span></b>";
				else
				{
					$player = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$opteam['player3']);
					switch ($player['dead']) {
						case 'no':
							echo "<span class=\"alive\"><a href=\"player.php?id=".$opteam['player3']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
						
						default:
							echo "<span class=\"dead\"><a href=\"player.php?id=".$opteam['player3']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
					}
				}
			?><br />
			<?php
				if ($opteam['player4'] == -1)
					echo "<b><span class=\"open\">Open slot</span></b>";
				else
				{
					$player = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$opteam['player4']);
					switch ($player['dead']) {
						case 'no':
							echo "<span class=\"alive\"><a href=\"player.php?id=".$opteam['player4']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
						
						default:
							echo "<span class=\"dead\"><a href=\"player.php?id=".$opteam['player4']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
					}
				}
			?><br />
			<?php
				if ($opteam['player5'] == -1)
					echo "<b><span class=\"open\">Open slot</span></b>";
				else
				{
					$player = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$opteam['player5']);
					switch ($player['dead']) {
						case 'no':
							echo "<span class=\"alive\"><a href=\"player.php?id=".$opteam['player5']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
						
						default:
							echo "<span class=\"dead\"><a href=\"player.php?id=".$opteam['player5']."\">".$player['first']." ".$player['last']."</a></span>";
							break;
					}
				}
			?><br />
		</div>
		<div class="clear"></div>
			<?php
		}
		?>
	<hr />
		<table id="stats">
			<tr>
				<th>kills</th>
				<th>deaths</th>
				<th>assists</th>
			</tr>
			<tr>
				<td><?php echo $me['kills']; ?></td>
				<td><?php echo $me['deaths']; ?></td>
				<td><?php echo $me['assists']; ?></td>
			</tr>
		</table>
		<?php
	}
	?>
	<?php
	if ($me['team'] != -1)
	{ ?>
		<?php
		if ($me['dead'] != 'yes')
		{
		?>
		<hr />
		<form action="action.php?method=suicide" method="POST">
			<input type="submit" name="suicide" value="suicide for this week" />
		</form>
		<?php
		}
		?>
			<?php
			$lolwat = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i",$me['team']);
			if ($lolwat['captain'] != $me['id'])
			{
				if ($me['dead'] == 'yes')
					echo "<hr />";
			?>
			<form action="action.php?method=verify&action=leave" method="POST">
			<input type="submit" name="leave" value="leave your team" />
			</form>
			<?php
			}
		}
			?>
	<hr />
	<form action="action.php?method=logout" method="POST">
			<input type="submit" name="suicide" value="log out" />
	</form>
	</div>
	</div>
</body>
</html>
	<?php
}
else
{ 
	$error = "";
	if(isset($_GET['error']))
		$error = "invalid username or password";
	?>
<html>
<head>
	<title>Junior Paranoia</title>
	<link rel="stylesheet" type="text/css" href="main.css" />
	<link rel="icon" type="image/png" href="icon.png" />
	<link rel="apple-touch-icon-precomposed" href="icon.png" />
</head>
<body>
	<div id="wrapper">
	<img src="header.png" width=666 height=157 /><br />
	<div id="loginbox" class="box">
		please log in
		<hr>
		<form id="login" method="POST" action="action.php?method=login">
			<div id="loginfields">
				<label for="email">email: </label><input id="email" name="email" type="text"/><br />
				<label for="password">password: </label><input id="password" name="password" type="password" />
			</div>
			<span class="note bad"><?php echo $error ?></span><?php if (isset($_GET['error'])) echo "<br />"; ?>
			<input id="submit" name="submit" type="submit" value="log in" /> 
		</form>
		<hr />
		<a href="create.php">sign up now!</a><span class="note bad"><br />&nbsp;</span>
	</div><br />
	<a href="info.html" class="info box">
		info<br /><br />
		<img src="question.png" width=100 height=100>
	</a>
	<a class="info box" href="http://ifiwereblank.com/?cat=3" >
		announcements<br /><br />
		<img src="bell.png" width=100 height=100>
	</a>
	<a class="info box" href="rules.html">
		rules<br /><br />
		<img src="sheriff.png" width=100 height=100>
	</a><br />

	<a class="info box" href="https://www.facebook.com/groups/114173765401642/">
		facebook<br /><br />
		<img src="facebook.png" width=100 height=100>
	</a>
	<a class="info box" href="http://challonge.com/juniorparanoia2013finals/" >
		schedule<br /><br />
		<img src="globe.png" width=100 height=100>
	</a>
</div>
</body>
</html> <?php
}
?>