<?php
// Inialize session
session_start();
// Check, if user is already login, then jump to secured page
if (isset($_SESSION['username'])) {
    echo $_SESSION['username'];
	//header('Location: absences.php');
}
?>
<html>
<head>
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
Gestion des absences du personnel du Lycée Jean Mermoz<br>
<br><br>veuillez saisir votre identifiant et mot de passe<br>

<?php
$users = array(
1 => array('userID' => 'mermoz', 'passmd5' => '5f4dcc3b5aa765d61d8327deb882cf99', 'userType' => '1'),
2 => array('userID' => 'ceich', 'passmd5' => '29f750e9f5cbbd1adab551521d615c58', 'userType' => '2'),
3 => array('userID' => 'jschildknecht1', 'passmd5' => 'fac05328668f599efe18e76cdb284aab', 'userType' => '3'),
4 => array('userID' => 'mzougui', 'passmd5' => '7253b86365a59884ea8aecc8661ed683', 'userType' => '2'),
5 => array('userID' => 'dkherbouche', 'passmd5' => 'acf1ce7039854e65ae1fa44b7835f24b', 'userType' => '3'),
6 => array('userID' => 'jrichmann', 'passmd5' => '9c151641752d221c80ac2c3b9567e621', 'userType' => '3'),
7 => array('userID' => 'paymonin', 'passmd5' => '396a55777d4dd2c8f63574645f850083', 'userType' => '1')
);
	
if (isset($_POST['username']) && isset($_POST['password']))
	{
	echo $user = $_POST['username'];
	echo "<BR>";
	echo $passmd5 = $_POST['password'];
	foreach($users as $userInfo)
		{
		if ( ($user == $userInfo['userID']) && ($passmd5 == $userInfo['passmd5']))
			{
			session_start();
			$_SESSION['username'] = $userInfo['userID'];
			$_SESSION['userType'] = $userInfo['userType'];
			header('Location: absences.php');
			break;
			}
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
<body>
<div id='global'>
</br>
<img src='logo_lla.png' align='left'>
<h3>Absences</h3>
<table border="0">
<form method="POST" onsubmit="encodePassword();" action="index.php">
<tr><td>Utilisateur</td><td>:</td><td><input type="text" name="username" size="20"></td></tr>
<tr><td>Mot de passe</td><td>:</td><td><input id="mot_de_passe" type="hidden" name="password" size="20"><input id="password" type="password" name="password_clair" size="20"></td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td><input type="submit" value="Login"></td></tr>
</form>
</table>
</div>

</body>

</html>

