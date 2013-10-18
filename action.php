<?php 
include 'meekrodb.2.0.class.php';
session_start();

DB::$user = 'paranoia';
DB::$password = 'password';
DB::$dbName = 'paranoia';
DB::$host = 'localhost'; //defaults to localhost if omitted
DB::$port = '3306'; // defaults to 3306 if omitted
DB::$encoding = 'utf8';

function plog($filename, $data)
{
	$f = fopen($filename,'a');
	fwrite($f,date("[G:i:s D, M j] ").$data);
	fclose($f);
}

function getName($id)
{
	$p = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$id);
	return $p['first']." ".$p['last'];
}

function passhash($password)
{
	return hash('md5',hash('md5',hash('md5',hash('md5',hash('md5',hash('md5',hash('md5',$password)))))));
}

function validEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}

$title = "";
$secondary = "";
$information = "";
$method = $_GET['method'];
switch ($method) {
	case 'create':
		$title = "creating account";
		$secondary = "creating account...";
		$information = "account created!<br /><br /><span><a href=\"/paranoia\">return to home</a></span>";
		$first = $_POST['first'];
		$last = $_POST['last'];
		$email = $_POST['email'];
		$emailcon = $_POST['emailcon'];
		$address = $_POST['address'];
		$city = $_POST['city'];
		$password = $_POST['password'];
		$passwordcon = $_POST['passwordcon'];
		if (!isset($first,$last,$address,$city,$password,$passwordcon,$emailcon,$email))
			header('Location: http://ifiwereblank.com/paranoia/create.php?error=4');
		if ($password != $passwordcon)
			header('Location: http://ifiwereblank.com/paranoia/create.php?error=1');
		if ($email != $emailcon)
			header('Location: http://ifiwereblank.com/paranoia/create.php?error=2');
		if (!validEmail($email))
			header('Location: http://ifiwereblank.com/paranoia/create.php?error=3');
		DB::query('SELECT * FROM players WHERE email=%s', strtolower($email));
		if (DB::count() != 0)
			header('Location: http://ifiwereblank.com/paranoia/create.php?error=5');
		DB::insert('players', array(
			'first' => $first,
			'last' => $last,
			'email' => strtolower($email),
			'password' => passhash($password),
			'address' => $address,
			'city' => $city
		));
		$_SESSION['playerid'] = DB::insertId();
        $_SESSION['first'] = $first;
        $_SESSION['last'] = $last;
		plog('players.txt',getName(DB::insertId())." created a paranoia account");
		break;
   case 'login':
      $title = "Logging in";
      $secondary = "Logging in...";
      $information = "You are logged in<br /><br /><span><a href=\"/paranoia\">continue</a></span>";
      if (!isset($_POST['email'], $_POST['password']))
         header("Location: http://ifiwereblank.com/paranoia/");
      $row = DB::queryFirstRow("SELECT * FROM players WHERE email=%s",$_POST['email']);
      if ($row['password'] != passhash($_POST['password']))
         header("Location: http://ifiwereblank.com/paranoia/?error=1");
      else
      {
         $_SESSION['playerid'] = $row['id'];
         $_SESSION['first'] = $row['first'];
         $_SESSION['last'] = $row['last'];
      }
      break;
	case 'logout':
      $title = "Logging out";
      $secondary = "Log out";
      $information = "You have logged out<br /><br /><span><a href=\"/paranoia\">return to home</a></span>";
      session_destroy();
      break;
   case 'teamcreate':
      $title = "creating team";
      $secondary = "creating team";
      $information = "your team has been created<br /><br /><span><a href=\"/paranoia\">return to home</a></span><br /><span><a href=\"/paranoia/captain.php\">go to the captain's corner</a></span>";
      if ($_POST['teamname'] == "")
         header("Location: http://ifiwereblank.com/paranoia/teamcreate.php?error=4");
      $playerids = array($_POST['player2'], $_POST['player3'], $_POST['player4'], $_POST['player5']);
      if (!isset($_SESSION['playerid']))
      {
         header("Location: http://ifiwereblank.com/paranoia/");
         die();
      }
      $me = DB::queryFirstRow('SELECT * FROM players WHERE id=%i', $_SESSION['playerid']);
      if ($me['team'] != -1)
      {
         header("Location: http://ifiwereblank.com/paranoia/teamcreate.php?error=6");
         die();
      }
      for ($i = 0; $i < 4; $i += 1)
      {
         if ($playerids[$i] != "")
         {
            if (!is_numeric($playerids[$i]))
            {
               header("Location: http://ifiwereblank.com/paranoia/teamcreate.php?error=1");
               die();
            }
            $s = DB::query('SELECT * FROM players WHERE id=%i', $playerids[$i]);
            $player = $s[0];
            if (DB::count() == 0 || $player['verified'] != 'yes')
            {
               header("Location: http://ifiwereblank.com/paranoia/teamcreate.php?error=1");
               die();
            }
            if ($player['team'] != -1 || $player['verified'] != 'yes')
            {
               header("Location: http://ifiwereblank.com/paranoia/teamcreate.php?error=2");
               die();
            }
            if ($playerids[$i] == $_SESSION['playerid'])
            {
               header("Location: http://ifiwereblank.com/paranoia/teamcreate.php?error=3");
               die();
            }
            for ($j = $i + 1;$j < 4;$j += 1)
            {
               if ($playerids[$i] == $playerids[$j])
               {
                  header("Location: http://ifiwereblank.com/paranoia/teamcreate.php?error=5");
                  die();
               }
            }
         }
         else
         {
            $playerids[$i] = -1;
         }
      }
      DB::insert('teams', array(
         'name' => $_POST['teamname'],
         'captain' => $_SESSION['playerid'],
         'player2' => $playerids[0],
         'player3' => $playerids[1],
         'player4' => $playerids[2],
         'player5' => $playerids[3]
      ));
      $inid = DB::insertId();
      DB::update('players', array('team' => $inid), 'id=%i', $_SESSION['playerid']);
      for ($i = 0; $i < 4; $i += 1)
      {
         if ($playerids[$i] != -1)
         {
            DB::update('players', array('team' => $inid), 'id=%i', $playerids[$i]);
         }
      }
	  plog('teams.txt',"\"".$_POST['teamname']."\" created by".getName($_SESSION['playerid']));
      break;
   case 'suicide':
      $deaths = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$_SESSION['playerid']);
      DB::update('players', array('dead' => 'yes', 'killid' => 0, 'deaths' => ($deaths['deaths'] + 1)), 'id=%i', $_SESSION['playerid']);
      $title = "suicide";
      $secondary = "suicide";
      $information = "you have suicided for the week<br /><br /><span><a href=\"/paranoia\">return to home</a></span>";
	  plog('kills.txt',getName($_SESSION['playerid'])." suicided!");
      break;
   case 'teamkill':
      $player1 = DB::query("SELECT * FROM players WHERE id=%i",$_POST['id']);
      $player = $player1[0];
      $title = "Team Kill";
      $secondary = "Captain Kill";
      if (DB::count() == 0)
      {
         $information = "Count not kill player<br />Player not found.";
      }
      else
      {
         if ($player['team'] == -1)
         {
            $information = "Count not kill player<br />Player not found.";  
         }
         else
         {
            $team = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i",$player['team']);
            if ($team['captain'] == $_SESSION['playerid'])
            {
               $deaths = $player['deaths'] + 1;
               DB::update('players', array('dead' => 'yes', 'killid' => -2, 'deaths' => $deaths), 'id=%i', $_POST['id']);
               $information = "You have killed:<br />".$player['first']." ".$player['last'];
            }
            else
            {
               $information = "Count not kill player<br />Player not found.";  
            }
         }
      }
	  plog('kills.txt',getName($_POST['id'])." was teamkilled by ".getName($_SESSION['playerid']));
      break;
   case 'kill':
      $player1 = DB::query("SELECT * FROM players WHERE id=%i",$_POST['id']);
      $player = $player1[0];
      $title = "Kill player";
      $secondary = "Kill player";
      if (DB::count() == 0)
      {
         $information = "Count not kill player<br />Player not found.";
      }
      else
      {
         if ($player['team'] == -1)
         {
            $information = "Count not kill player<br />Player not found.";  
         }
         else
         {
            $team = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i",$player['team']);
            $me = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$_SESSION['playerid']);
            if ($me['team'] != $team['opponent'] || $me['dead'] == 'yes')
            {
               $information = "Count not kill player<br />You are dead or on the wrong team";  
            }
            else
            {
               $deaths = $player['deaths'] + 1;
               DB::insert('kills', array(
                  'shooter' => $_SESSION['playerid'],
                  'assister' => $_POST['assist'],
                  'victim' => $_POST['id'],
                  'team1' => $me['team'],
                  'team2' => $player['team']
               ));
               $insertid = DB::insertId();
               if ($_POST['assist'] != -1)
               {
                  $assister = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$_POST['assist']);
                  $assists = $assister['assists'] + 1;
                  DB::update('players', array('assists' => $assists), 'id=%i', $_POST['assist']);
               }
               DB::update('players', array('kills' => ($me['kills'] + 1)), 'id=%i', $_SESSION['playerid']);
               DB::update('players', array('dead' => 'yes', 'killid' => $insertid, 'deaths' => $deaths), 'id=%i', $_POST['id']);
               $information = "You have killed:<br />".$player['first']." ".$player['last'];
            }
         }
      }
	  plog('kills.txt',getName($_POST['id'])." was killed by ".getName($_SESSION['playerid']));
      break;
   case 'addmember':
      $me = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$_SESSION['playerid']);
      $team = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i",$me['team']);
      if ($me['team'] == -1)
      {
         $title = "Error";
         $secondary = "Could not add member";
         $information = "You are not on a team";
      } else if ($team['captain'] != $me['id']) {
         $title = "Error";
         $secondary = "Could not add member";
         $information = "You are not the captain";
      } else {
         $newmember1 = DB::query("SELECT * FROM players WHERE id=%i",$_POST['id']);
         $newmember = $newmember1[0];
         if (DB::count() == 0)
         {
            $title = "Error";
            $secondary = "Could not add member";
            $information = "Player doesn't exist";
         } else if ($newmember['team'] != -1) {
            $title = "Error";
            $secondary = "Could not add member";
            $information = "Player already on team";
         } else if ($newmember['verified'] != 'yes') {
            $title = "Error";
            $secondary = "Could not add member";
            $information = "Player not yet verified";
         } else {
            DB::update('teams',array($_POST['playernumber'] => $_POST['id']),'id=%i',$me['team']);
            DB::update('players',array('dead' => 'yes', 'killid' => 0, 'team' => $me['team']),'id=%i',$_POST['id']);
            $title = "Add Player";
            $secondary = "Player Added Successfully";
            $information = "Player added to team<br /><span><a href=\"captain.php\">continue</a></span>";
         }
      }
	  plog('teams.txt',"\"".$team['name']."\" got a new member.");
	  plog('players.txt',getName($_POST['id'])."joined team \"".$team['name']."\"");
      break;
	case 'verify':
		$title = "Action Verification";
        $secondary = "Are you sure you want to".(($_GET['action'] == 'disband')? " disband your team?" : " leave your team?");
        $information = "<span><a href=\"action.php?method=".$_GET['action']."\">Yes</a>&nbsp;&nbsp;<a href=\"/paranoia\">No</a></span><span class=\"note\"><br /><br />Note: This operation cannot be undone.</span>";
		if ($_GET['action'] == 'disband')
			$information = $information."<span class=\"note\"><br />Your team money will not be refunded if it was paid.</span>";
		break;
	case 'disband':
		if (!isset($_SESSION['playerid']))
		{
			header("Location: http://ifiwereblank.com/paranoia");
			die();
		}
		$me = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$_SESSION['playerid']);
		$team = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i",$me['team']);
		if ($me['id'] != $team['captain'])
		{
			$title = "Error";
			$secondary = "error";
			$information = "an error occured.";
		}
		else
		{
			DB::update('teams', array('captain' => -1, 'player2' => -1, 'player3' => -1, 'player4' => -1, 'player5' => -1, 'status' => 'eliminated', 'valid' => 'no'), "id=%i", $me['team']);
			$teamids = array($team['captain'],$team['player2'],$team['player3'],$team['player4'],$team['player5']);
			for ($i = 0;$i < 5;$i++)
				DB::update('players',array('team' => -1),'id=%i',$teamids[$i]);
		}
		plog('teams.txt',"\"".$team['name']."\" disbanded!");
		break;
	case 'leave':
		$title = "Leave";
		$secondary = "You have left.";
		if (!isset($_SESSION['playerid']))
		{
			header("Location: http://ifiwereblank.com/paranoia");
			die();
		}
		$me = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$_SESSION['playerid']);
		if ($me['team'] == -1)
		{
			header("Location: http://ifiwereblank.com/paranoia");
			die();
		}
		$team = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i",$me['team']); 
		if ($me['id'] != $team['captain'] && $me['id'] != $team['player2'] && $me['id'] != $team['player3'] && $me['id'] != $team['player4'] && $me['id'] != $team['player5'])
		{
			header("Location: http://ifiwereblank.com/paranoia");
			die();
		}
		if ($team['captain'] == $me['id'])
		{
			DB::update('players', array('team' => '-1'), "id=%i", $me['id']);
			$teamids = array($team['player2'],$team['player3'],$team['player4'],$team['player5']);
			$found = false;
			for ($i = 2;$i < 6;$i++)
			{
				if ($team['player'.$i] != -1)
				{
					DB::update('teams', array('captain' => $team['player'.$i], 'player'.$i => -1), "id=%i", $me['team']);
					$found = true;
					break;
				}
			}
			if (!$found)
			{
				header("Location: http://ifiwereblank.com/paranoia/action.php?method=disband");
				die();
			}
			$information = "You have left, and a new captain has been appointed";
		} else if ($team['player2'] == $me['id'])
		{
			DB::update('teams', array('player2' => -1), "id=%i", $me['team']);
			DB::update('players', array('team' => '-1'), "id=%i", $me['id']);
		} else if ($team['player3'] == $me['id'])
		{
			DB::update('teams', array('player3' => -1), "id=%i", $me['team']);
			DB::update('players', array('team' => '-1'), "id=%i", $me['id']);
		} else if ($team['player4'] == $me['id'])
		{
			DB::update('teams', array('player4' => -1), "id=%i", $me['team']);
			DB::update('players', array('team' => '-1'), "id=%i", $me['id']);
		} else if ($team['player5'] == $me['id'])
		{
			DB::update('teams', array('player5' => -1), "id=%i", $me['team']);
			DB::update('players', array('team' => '-1'), "id=%i", $me['id']);
		}
		plog("players.txt",getName($me['id'])." left his team!");
		break;
	case 'playermanage':
		if (!isset($_SESSION['playerid']))
		{
			header("Location: http://ifiwereblank.com/paranoia");
			die();
		}
		$me = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$_SESSION['playerid']);
		$team = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i",$me['team']);
		if ($me['id'] != $team['captain'])
		{
			$title = "Error";
			$secondary = "error";
			$information = "an error occured.";
		}
		else
		{
			if ($_POST['action'] == "kill")
			{
				DB::update('players',array('dead' => 'yes', 'killid' => -2),'id=%i',$_POST['id']);
				$title = "Captain kill";
				$secondary = "Kill teammate.";
				$information = "Teammate successfully killed.";
			}
			else if ($_POST['action'] == "remove player")
			{
				$teamids = array(-1,$team['player2'],$team['player3'],$team['player4'],$team['player5']);
				for ($i = 1;$i < 5;$i++)
				{
					if ($_POST['id'] == $teamids[$i])
					{
						DB::update('players',array('team' => -1),'id=%i',$_POST['id']);
						DB::update('teams',array("player".($i+1) => '-1'),'id=%i',$team['id']);
						$title = "Remove Teammate";
						$secondary = "remove teammate.";
						$information = "Teammate successfully removed.";
						plog("players.txt",getName($_POST['id'])." left his team!");
					}
				}
			}
			else
			{
				$title = "Error";
				$secondary = "error";
				$information = "an error occured.";
			}
		}
		break;
	case 'killaction':
		if (!isset($_SESSION['playerid']))
		{
			header("Location: http://ifiwereblank.com/paranoia");
			die();
		}
		$me = DB::queryFirstRow("SELECT * FROM players WHERE id=%i",$_SESSION['playerid']);
		$team = DB::queryFirstRow("SELECT * FROM teams WHERE id=%i",$me['team']);
		$kill = DB::queryFirstRow("SELECT * FROM kills WHERE id=%i",$_POST['id']);
		if (($me['team'] != $kill['team1'] && $me['team'] != $kill['team2']) || $kill['status'] != 'pending' || $team['captain']  != $me['id'])
		{
			$title = "Error";
			$secondary = "error";
			$information = "an error occured.";
		}
		else
		{
			if ($_POST['action'] == "verify")
			{
				DB::update('kills',array('status' => 'valid'),'id=%i',$kill['id']);
				$title = "Verify kill";
				$secondary = "Kill verified.";
				$information = "The kill has been verified and counted.";
				plog("kills.txt",getName($_SESSION['id'])." verified kill id ".$kill['id']);
			}
			else if ($_POST['action'] == "dispute")
			{
				DB::update('kills',array('status' => 'disputed'),'id=%i',$kill['id']);
				DB::update('players',array('dead' => 'no'),'id=%i',$kill['victim']);
				$title = "Dispute kill";
				$secondary = "Kill disputed.";
				$information = "The kill has been disputed.<br />An email has been sent out to both captains.<br />A response is necessary.";
				plog("kills.txt",getName($_SESSION['id'])." disputed kill id ".$kill['id']);
				//email bullshit here.
			}
			else
			{
				$title = "Error";
				$secondary = "error";
				$information = "an error occured.";
			}
		}
		break;
	default:
		$title = "Error";
      $secondary = "error";
      $information = "that is not a valid action code";
		break;
}
?>
<html>
<head>
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="main.css" />
	<link rel="stylesheet" type="text/css" href="action.css" />
	<link rel="icon" type="image/png" href="icon.png" />
	<link rel="apple-touch-icon-precomposed" href="icon.png" />
</head>
<body>
	<div id="wrapper">
	<a href="/paranoia"><img src="header.png" width=666 height=157 /></a><br />
		<div id="info" class="box">
			<?php echo $secondary; ?><hr />
			<?php echo $information; ?>
			<br />
		</div>
	</div>
</body>
</html>
