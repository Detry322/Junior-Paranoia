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
if (isset($_POST['teams']))
{	
$message = "Teams have been paid";	
foreach ($_POST['teams'] as $id)	
{		
DB::update('teams',array('paid' => 'yes'),'id=%i',$id);	
}
}
?>
<html><head><title>Team paid</title></head><body><center><h1>Count a team as paid</h1>
<?php echo $message; ?>
<form action="payment.php" method="POST"><table><tr>	<th>Captain</th>	<th>Team Name</th>	<th>Count as paid</th></tr>
<?php
$teams = DB::query("SELECT * FROM teams WHERE paid=%s",'no');
foreach ($teams as $team)
{
	echo "<tr><td>".getName($team['captain'])."</td><td>".$team['name']."</td><td><input type=\"checkbox\" name=\"teams[]\" value=".$team['id']." /></td></tr>";
}
?>
</table><input type="submit" value="Count teams" /></form></center></body></html>