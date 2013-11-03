<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>Edition des utilisateurs</title>
	<script language="javascript" src="js/md5.js"></script>
	<script type="text/javascript" src="js/user.js"></script>
	
	<link href="absences.css" rel="stylesheet" type="text/css">
	<link href="css/menu.css" type="text/css" rel="stylesheet" />
	<link href="css/users.css" type="text/css" rel="stylesheet" />
</head>

<body>

<?php
require_once("./menu.php");
$menu = affiche_menu();
echo $menu;
?>
<div class='corps'>
<?php
include('db_fonction.php');
$ma_base = connect_db();

if(ISSET($_GET['action'])) // si appel avec action demandée
{
    if ($_GET['action'] == 1)  // demande de suppresssion
    {
        if(!isset($_GET['confirm']))
        {
?>
            <script type="text/javascript">
            var answer = confirm ("Confirmer la suppression ?")
                if (answer)
                    window.location='<?php echo "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>' + '&confirm=1';
                else
                    window.location='<?php echo "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>' + '&confirm=0';
            </script>
<?php
        }
        if(isset($_GET['confirm']) && $_GET['confirm'] == 1)
        {
            $query = "DELETE FROM users WHERE username='" . $_GET['username']."'";
            mysql_query($query, $ma_base);
        }
    }
    if ($_GET['action'] == 2) // demande de mise à jour
    {
        	
		$query = "UPDATE users SET password='".mysql_real_escape_string($_GET['password'])."', usertype='".mysql_real_escape_string($_GET['usertype'])."' WHERE username='".mysql_real_escape_string($_GET['username'])."'";
		mysql_query($query, $ma_base);
    }
    if ($_GET['action'] == 3) // demande d'ajout
    {
        $query = "INSERT INTO users(username,password,usertype) VALUES('".mysql_real_escape_string($_GET['username'])."','".mysql_real_escape_string($_GET['password'])."','".mysql_real_escape_string($_GET['usertype'])."')";
        echo $query;
		mysql_query($query, $ma_base);
    }
}

 // affichage du tableau des utilisateurs
 
$query = "SELECT * FROM users ORDER BY username";
$user_list=mysql_query($query,$ma_base);
?>

<div id="statut"></div>

<table border=1 cellpadding=0 cellspacing=0>
    <tr>
        <th rowspan="2" width = 75px></th>
		<th colspan="6" class="TDligne2"><?php echo mysql_num_rows($user_list); ?> utilisateur(s)</th>
	</tr>
	<tr>
		<th width = "200px" BGCOLOR="#99CCFF">identifiant</th>
		<th width = "120px" BGCOLOR="#99CCFF">mot de passe</th>
		<th width = "130px" BGCOLOR="#99CCFF">niveau d'utilisation</th>
		<th width = "200px"><input type='button' onClick="javascript:add_user()" value='Ajouter un utilisateur'></th>
	</tr>
	<tr id="adduser">
	</tr>

	
<?php //autre lignes du tableau
for($j=0;$j<mysql_num_rows($user_list);$j++) // enumere les utilisateurs
{
    $username = mysql_result($user_list,$j,"username");
    $password = mysql_result($user_list,$j,"password");
    $usertype = mysql_result($user_list,$j,"usertype");
?>
	<tr>
	<td align='middle'>
    <input border=0 src="ico/supp.gif" type=image onClick="javascript:window.location='users.php?action=1&username=<?php echo $username ?>';" align="middle" > 
   	</td>
<?php
    $bgColor = ($j % 2) ? "#CCFFCC" : "#66FFFF";

    echo "<td bgcolor=$bgColor align='middle' id='username$j'>$username</td>";
	
    echo "<td bgcolor=$bgColor align='middle' id='password$j'>";
	echo "<img src='ico/edit.gif' onClick=\"javascript:edit_user_password('".addslashes($username)."','".addslashes($password)."','".addslashes($usertype)."',$j)\">"; 
	echo "</td>";
		
    echo "<td bgcolor=$bgColor align='middle' id='usertype$j'>";
	echo "<img src='ico/edit.gif' onClick=\"javascript:edit_user_level('".addslashes($username)."','".addslashes($password)."','".addslashes($usertype)."',$j)\">"; 
	echo "&nbsp $usertype";
	echo "</td>";
	
?>
	<td id="save<?php echo $j?>"></td>
    </tr>
<?php
}
?>

</table>

<br>
</div>
</body>
</html>

