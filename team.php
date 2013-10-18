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
function teamnotfound()
{
	echo "Team not found<hr /><br /> We could not find the team you specified<br /><br /><br />";
}
?>
<html>
<head>
	<title>Team Information</title>
	<link rel="stylesheet" type="text/css" href="main.css" />
	<link rel="stylesheet" type="text/css" href="team.css" />
	<link rel="icon" type="image/png" href="icon.png" />
	<link rel="apple-touch-icon-precomposed" href="icon.png" />
</head>
<body>
	<div id="wrapper">
		<a href="/paranoia"><img src="header.png" width=666 height=157 /></a><br />
		<div class="box" id="teambox">
			<?php 
			$id = $_GET['id'];
			if (!isset($id) || !is_numeric($id))
			{
				teamnotfound();
			}
			else
			{	
				$team1 = DB::query("SELECT * FROM teams WHERE id=%i",$id);
				if (DB::count() == 0)
				{
					teamnotfound();
				}
				else
				{
					$team = $team1[0];
					echo stripslashes($team['name']);
				?>
			<hr />
			<div class="teamparts">
				<b>Captain:</b><br />
				Player 2:<br />
				Player 3:<br />
				Player 4:<br />
				Player 5:<br />
				Paid:<br />
				Status:<br />
				Opponent:<br />
			</div>
			<div class="players">
				<b><?php echo getName($team['captain']); ?></b><br />
				<?php echo getName($team['player2']); ?><br />
				<?php echo getName($team['player3']); ?><br />
				<?php echo getName($team['player4']); ?><br />
				<?php echo getName($team['player5']); ?><br />
				<?php echo ($team['paid'] == 'yes')?'Paid':'Not paid'; ?><br />
				<?php 
					if ($team['status'] == 'waiting')
						echo "Waiting for match";
					else if ($team['status'] == 'playing')
						echo "Playing";
					else
						echo "Eliminated"; 
				?><br />
				<?php
					if ($team['opponent'] == -1)
					{
						echo "N/A";
					}
					else
					{
						$opponent = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i", $team['opponent']);
						echo "<span><a href=\"team.php?id=".$team['opponent']."\">".stripslashes($opponent['name'])."</a></span>";
					}
				?><br />
			</div>
			<div class="clear"></div><hr />
			<table id="stats">
			<tr>
				<th>wins</th>
				<th>losses</th>
			</tr>
			<tr>
				<td><?php echo $team['wins']; ?></td>
				<td><?php echo $team['losses']; ?></td>
			</tr>
		</table>
		<?php 	}
			}
		?>
	</div>
</body>
</html>