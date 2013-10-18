<?php 
include 'meekrodb.2.0.class.php';
session_start();

DB::$user = 'paranoia';
DB::$password = 'password';
DB::$dbName = 'paranoia';
DB::$host = 'localhost'; //defaults to localhost if omitted
DB::$port = '3306'; // defaults to 3306 if omitted
DB::$encoding = 'utf8';

function getName($id)
{
	$p = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$id);
	if ($id != -1)
		return "<span class=".(($p['dead'] == 'yes')?"\"dead\"":"\"alive\"")."><a href=\"player.php?id=".$id."\">".$p['first']." ".$p['last']."</a></span>";
	else
		return "<span class=\"open\">Open Slot</span>";
}

if (!isset($_SESSION['playerid']))
{
	header("Location: http://ifiwereblank.com/paranoia/");
	die();
}

$me = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$_SESSION['playerid']);

if ($me['team'] == -1 || $me['verified'] != 'yes')
{
	header("Location: http://ifiwereblank.com/paranoia/");
	die();
}

$team = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i",$me['team']);

if ($team['captain'] != $me['id'])
{
	header("Location: http://ifiwereblank.com/paranoia/");
	die();
}
?>
<html>
<head>
	<title>The Captain's Corner</title>
	<link rel="stylesheet" type="text/css" href="main.css" />
	<link rel="stylesheet" type="text/css" href="captain.css" />
	<link rel="icon" type="image/png" href="icon.png" />
	<link rel="apple-touch-icon-precomposed" href="icon.png" />
</head>
<body>
	<div id="wrapper">
		<a href="/paranoia"><img src="header.png" width=666 height=157 /></a><br />
		<div class="box" id="captainbox">
			captain's corner<hr />
			pending kills<br />
			<div id="verify">
				<ol>
					<?php
						$teamids = array($team['captain'],$team['player2'],$team['player3'],$team['player4'],$team['player5']);
						$killids = array();
						for ($i = 0; $i < 5; $i++)
						{
							$p = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$teamids[$i]);
							if ($p['dead'] != 'yes' || $p['killid'] <= 0)
								continue;
							$k = DB::queryFirstRow("SELECT * FROM kills WHERE id=%i",$p['killid']);
							if ($k['status'] == 'pending')
								$killids[count($killids)] = $k['id'];
						}
						if (count($killids) == 0)
						{
							echo "<center>No Pending Kills To Display</center>";
						}
						else
						{
							foreach ($killids as $killid)
							{
								$kill = DB::queryFirstRow("SELECT * FROM kills WHERE id=%i",$killid);
								echo "<li>".getName($kill['shooter'])." killed ".getName($kill['victim']);
								?>
								<form action="action.php?method=killaction" method="POST">
									<input type="hidden" name="id" value=<?php echo $killid; ?> />
									<input type="submit" class="button" name="action" value="verify" />
									<input type="submit" class="button" name="action" value="dispute" />
								</form></li>
								<?php
							}
						}
					?>
				</ol>
			</div>
			<hr />
			manage team
			<div id="manage">
				<ul>
					<?php
						for ($i = 1;$i < 5;$i++)
						{
							if ($teamids[$i] != -1)
							{
								echo "<li>".getName($teamids[$i]);
								?>
								<form action="action.php?method=playermanage" method="POST">
									<input type="hidden" name="id" value=<?php echo $teamids[$i]; ?> />
									<input type="submit" class="button" name="action" value="kill" />
									<input type="submit" class="button" name="action" value="remove player" />
								</form></li>
								<?php
							}
							else
							{
								?>
									<li>
						Enter player number to add player:
						<form action="action.php?method=addmember" method="POST">
							<input type="hidden" name="playernumber" value=<?php echo "\"player".($i+1)."\""; ?> />
							<input type="text" name="id" />
							<input type="submit" class="button" name="add player" value="add" />
						</form>
					</li>
								<?php
							}
						}
					?>
				</ul>
			</div>
			<hr />
			<form action="action.php?method=verify&action=leave" method="POST">
				<input type="submit" class="button" name="action" value="leave team" />
			</form>
			<form action="action.php?method=verify&action=disband" method="POST">
				<input type="submit" class="button" name="action" value="disband team" />
			</form>
		</div>
	</div>
</body>
</html>