<?php 
include 'meekrodb.2.0.class.php';
session_start();

DB::$user = 'paranoia';
DB::$password = 'password';
DB::$dbName = 'paranoia';
DB::$host = 'localhost'; //defaults to localhost if omitted
DB::$port = '3306'; // defaults to 3306 if omitted
DB::$encoding = 'utf8';

$unpaid = DB::query("SELECT * FROM teams WHERE paid=%s",'no');
$paid = DB::query("SELECT * FROM teams WHERE paid=%s",'yes');
$count = DB::count();

?>
<html>
<head>
	<title>Junior Paranoia Team Listing</title>
	<link rel="stylesheet" type="text/css" href="home.css" />
	<link rel="stylesheet" type="text/css" href="teams.css" />
	<link rel="icon" type="image/png" href="icon.png" />
	<link rel="apple-touch-icon-precomposed" href="icon.png" />
</head>
<body>
	<div id="wrapper">
	<a href="/paranoia"><img src="header.png" width=666 height=157 /></a><br />
	<a href="info.html" class="info box">
		info
	</a>
	<a class="info box" href="http://ifiwereblank.com/?cat=3" >
		announcements
	</a>
	<a class="info box" href="rules.html">
		rules
	</a>
	<a class="info box" href="#facebook">
		facebook
	</a>
	<a class="info box" href="http://challonge.com/juniorparanoia2013finals/" >
		schedule
	</a>
	<a class="info box" href="#captain">
		captain's corner
	</a>
	<div id="teamsbox" class="box">
		Team Listing
		<hr />
		<center>
		<table>
		<tr><td><b>Total Money in The Pot:</b></td><td><b>$<?php printf("%.2f",225); ?></b></td></tr>
				<tr><td>First place team prize:</td><td>$<?php printf("%.2f",225*0.8); ?></td></tr>
				<tr><td>Second place team prize:</td><td>$<?php printf("%.2f",225/10); ?></td></tr>
				<tr><td>Best player prize:</td><td>$<?php printf("%.2f",225*0.1); ?></td></tr>
		</table>
		<hr />
		<span style="color: #060">Paid Teams</span>
		<hr />
		<ul>
			<?php
				foreach ($paid as $team)
				{
					echo "<li><a href=\"team.php?id=".$team['id']."\">".$team['name']."</a></li>";
				}
			?>
		</ul>
		<hr />
		<span style="color: #600">Unpaid Teams</span>
		<hr />
		<ul>
			<?php
				foreach ($unpaid as $team)
				{
					echo "<li><a href=\"team.php?id=".$team['id']."\">".$team['name']."</a></li>";
				}
			?>
		</ul>
	</center>
	</div>
</div>
</body>
</html>