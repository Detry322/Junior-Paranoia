<?php 
include '../meekrodb.2.0.class.php';
session_start();
DB::$user = 'paranoia';
DB::$password = 'Super.123';
DB::$dbName = 'paranoia';
DB::$host = 'paranoia.db.9376441.hostedresource.com'; //defaults to localhost if omitted
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
function getTeam($id)
{	
$p = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i",$id);	
return $p['name'];
}

if (!isset($_SESSION['playerid']))
{	
header("Location: http://ifiwereblank.com/paranoia/");	
die();
}

$me = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$_SESSION['playerid']);

if ($me['commissioner'] != 'yes')
{	
header("Location: http://ifiwereblank.com/paranoia/");	
die();
}
$message == "";
if (isset($_POST['kill']))
{	
$message = "Kill has been marked as valid or invalid";		
DB::update('kills',array('status' => $_POST['kill']),'id=%i',$_POST['killid']);	
if ($_POST['kill'] == "valid")
{
	$kill = DB::query("SELECT * FROM kills WHERE id=%i",$_POST['killd']);
	DB::update('players',array('dead' => "yes", "killid" = $_POST['killd']),'id=%i',$kill['victim']);	
}
}
?>
<html><head><title>Verify Players</title></head><body><center><h1>Verify Players</h1>
<?php echo $message; ?><table><tr>	<th>Victim</th>	<th>Shooter</th> <th>Team 1</th> <th>Team 2</th> <th>Action</th></tr>
<?php
$players = DB::query("SELECT * FROM kills WHERE status=%s",'disputed');
foreach ($players as $player)
{
	?>
		<td><?php echo getName($player['victim']); ?></td>
		<td><?php echo getName($player['shooter']); ?></td>
		<td><?php echo getTeam($player['team1']); ?></td>
		<td><?php echo getTeam($player['team2']); ?></td>
		<td>
		<form action="dispute.php" method="POST">
			<input type="hidden" name="killid" value=<?php echo $player['id']; ?> />
			<input type="submit" name="kill" value="valid" />
			<input type="submit" name="kill" value="invalid" />
		</form>
	</td>
	<?php
}
?>
</table></center></body></html>