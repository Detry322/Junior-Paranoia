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
if (isset($_POST['players']))
{	
$message = "Players have been verified";	
foreach ($_POST['players'] as $id)	
{		
DB::update('players',array('verified' => 'yes'),'id=%i',$id);	
}
}
?>
<html><head><title>Verify Players</title></head><body><center><h1>Verify Players</h1>
<?php echo $message; ?>
<form action="verify.php" method="POST"><table><tr>	<th>Player Name</th>	<th>Address</th>	<th>Verify</th></tr>
<?php
$players = DB::query("SELECT * FROM players WHERE verified=%s",'no');
foreach ($players as $player)
{
	echo "<tr><td>".$player['first']." ".$player['last']."</td><td>".$player['address']."<br />".$player['city']."</td><td><input type=\"checkbox\" name=\"players[]\" value=".$player['id']." /></td></tr>";
}
?>
</table><input type="submit" value="Verify Players" /></form></center></body></html>