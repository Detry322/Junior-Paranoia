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
	return $p['first']." ".$p['last'];
}

function playernotfound()
{
	echo "Player not found<hr /><br /> We could not find the player you specified<br /><br /><br />";
}

?>
<html>
<head>
	<title>Player Information</title>
	<link rel="stylesheet" type="text/css" href="main.css" />
	<link rel="stylesheet" type="text/css" href="player.css" />
	<link rel="icon" type="image/png" href="icon.png" />
	<link rel="apple-touch-icon-precomposed" href="icon.png" />
</head>
<body>
	<div id="wrapper">
		<a href="/paranoia"><img src="header.png" width=666 height=157 /></a><br />
		<div class="box" id="playerbox">
			<?php 
			$id = $_GET['id'];
			if (!isset($id,$_SESSION['playerid']) || !is_numeric($id))
			{
				playernotfound();
			}
			else
			{	
				$me = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$_SESSION['playerid']);
				$getplayer1 = DB::query("SELECT * FROM players WHERE id=%i",$id);
				if (DB::count() == 0)
				{
					playernotfound();
				}
				else
				{
					$getplayer = $getplayer1[0];
				?>
			<span class=<?php echo (($getplayer['dead'] == 'yes')?"\"dead\"":"\"alive\""); ?>><?php echo $getplayer['first']." ".$getplayer['last']; ?></span>
			<hr />
			<div class="labels">
				Team:<br />
				Address:<br />
				City:<br />
				Dead:<br />
				Killed by:<br />
				Paid:<br />
			</div>
			<div class="information">
				<?php
					if ($getplayer['team'] == -1)
					{
						echo "Not on a team";
					}
					else
					{
						$team = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i", $getplayer['team']);
						echo "<span><a href=\"team.php?id=".$getplayer['team']."\">".stripslashes($team['name'])."</a></span>";
					}
				?><br />
				<?php
					if ($getplayer['team'] == -1)
					{
						echo "Not available";
					}
					else
					{
						$team = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i", $getplayer['team']);
						if ($me['id'] == $getplayer['id'] || $me['team'] == $getplayer['team'])
							echo $getplayer['address'];
						else
						{
							if ($team['opponent'] == -1)
								echo "Not available";
							else
							{
								$opponent = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i", $team['opponent']);
								if ($me['id'] == $opponent['captain'])
									echo $getplayer['address'];
							}
						}
					}
				?><br />
				<?php echo $getplayer['city']; ?><br />
				<?php echo (($getplayer['dead'] == 'yes')?"Yes":"No"); ?><br />
				<?php 
				if ($getplayer['dead'] != 'yes')
				{
					echo "N/A";
				}
				else
				{
					if ($getplayer['killid'] == 0)
					{
						echo "Suicide";
					}
					else if ($getplayer['killid'] == -2)
					{
						echo "Killed by Team Captain";
					}
					else
					{
						$kill = DB::queryFirstRow("SELECT * FROM kills WHERE id=%i",$getplayer['killid']);
						$other = DB::queryFirstRow("SELECT * FROM players WHERE id=%i", $kill['shooter']);
						echo "<span class=\"".(($other['dead'] == 'yes')?"dead":"alive")."\"><a href=\"player.php?id=".$other['id']."\">".$other['first']." ".$other['last']."</a></span>";
					}
				}
				?><br />
				<?php echo (($getplayer['paid'] == 'yes')?"Yes":"No"); ?><br />
			</div>
			<div class="clear"></div>
			<hr />
			<table id="stats">
			<tr>
				<th>kills</th>
				<th>deaths</th>
				<th>assists</th>
			</tr>
			<tr>
				<td><?php echo $getplayer['kills']; ?></td>
				<td><?php echo $getplayer['deaths']; ?></td>
				<td><?php echo $getplayer['assists']; ?></td>
			</tr>
			</table>
			<?php 
			if ($getplayer['team'] != -1 && $getplayer['dead'] != 'yes')
			{
				$team = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i", $getplayer['team']);
				if ($team['opponent'] != -1)
				{
					$opponent = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i", $team['opponent']);
					if ($me['team'] == $opponent['id'] && $me['dead'] != 'yes')
					{
			?>
			<hr />
			<form action="action.php?method=kill" method="POST">
				<input type="hidden" name="id" value=<?php echo "\"".$getplayer['id']."\""; ?>>
				<input id="killplayer" type="submit" name="kill" value="kill player" /><br />
				<label for="assist">Assisted by: </label>
				<select name="assist">
					<option value="-1">No one</option>
					<?php
					$ids = array($opponent['captain'],$opponent['player2'],$opponent['player3'],$opponent['player4'],$opponent['player5']);
					for ($i = 0;$i < 5;$i += 1)
					{
						if ($ids[$i] != -1 && $ids[$i] != $_SESSION['playerid'])
						{
							echo "<option value=\"".$ids[$i]."\">".getName($ids[$i])."</option>";
						}
					}
					?>
				</select>
			</form>
			<?php
							}
						}
					}
				}
			}
			?>
		</div>
	</div>
</body>
</html>