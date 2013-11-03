<?php
// Inialize session
session_start();
// Check, if user is already login, then jump to secured page
if (isset($_SESSION['username'])) 
	{
    header('Location: absences.php');
	}
?>

<html>
	<head>
	<meta content="text/html; charset=utf-8" http-equiv="content-type">
		<title>authentification gestion des absences</title>
		<script language="javascript" src="js/md5.js"></script>
		<script language="javascript">
		<!--
		  function encodePassword() {
			str = document.getElementById("password").value;
			document.getElementById("mot_de_passe").value = MD5(str);
		   }
		// -->
		</script>
		<link href="css/login.css" type="text/css" rel="stylesheet" />
	</head>
	<body>

		<div> Gestion des absences du personnel du Lycée Jean Mermoz </div>

		<?php
		include('db_fonction.php');
		$ma_base = connect_db();

		if (isset($_POST['username']) && isset($_POST['password']))
			{
			echo $user = $_POST['username'];
			echo "<br>".$passmd5 = $_POST['password'];
			echo "<br>".$logquery = "SELECT * FROM users WHERE username='".$user."' AND password='".$passmd5."'";
			$logresult = mysql_query($logquery, $ma_base);
			if (mysql_num_rows($logresult) == 1)
				{
				session_start();
				echo $_SESSION['username'] = mysql_result($logresult,0,"username");
				echo $_SESSION['usertype'] = mysql_result($logresult,0,"usertype");
				header('Location: absences.php');
				break;
				}
			echo "<br><br>UTILISATEUR OU MOT DE PASSE INCONNU <br><br>";
			}
			
		if (isset($_POST['logout']))
			{
				// On démarre la session
				session_start ();  
				// On détruit les variables de notre session
				session_unset ();  
				// On détruit notre session
				session_destroy ();  
				// On redirige le visiteur vers la page d'accueil
				header ('location: index.php');  
			}


		?>
		<div id="login_box">
			<h3>veuillez saisir votre identifiant et mot de passe</h3>
			<img id="logo" src='logo.png'>  <? // imposer la taille du logo ?>
			<form id="input"  method="POST" onsubmit="encodePassword();" action="index.php">
				<div>
					Utilisateur <input type="text" name="username" size="20">
				</div>	
				<div>
					Mot de passe <input id="mot_de_passe" type="hidden" name="password" size="20"><input id="password" type="password" name="password_clair" size="20">
				</div>	
				<div>
					<input type="submit" value="Login">
				</div>	
			</form>
		</div>

	</body>
</html>

