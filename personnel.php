<?php
require_once("./menu.php");
$menu = affiche_menu();
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>Edition des absences</title>
<script type="text/javascript" src="js/edit_personnel.js"></script>
<script type="text/javascript" src="js/add_personnel.js"></script>
<script type="text/javascript" src="js/bsn.AutoSuggest_c_2.0.js"></script>
    <script type="text/javascript" src="js/completion.js"></script>
 <link rel="stylesheet" href="css/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />
            <link href="absences.css" rel="stylesheet" type="text/css">
        <link href="css/menu.css" type="text/css" rel="stylesheet" />
        </head>
<body>
<?php
echo $menu;
?>
<div class='corps'>

<?php
$options = array();
include('db_fonction.php');
$ma_base = connect_db();
if(ISSET($_GET['action']))
{
    if ($_GET['action'] == 1)
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
            $query = "DELETE FROM personnel WHERE PRID=" . $_GET['id'];
            mysql_query($query, $ma_base);
        }
    }
    if ($_GET['action'] == 2)
    {
        $query = "UPDATE personnel SET civilite='".mysql_real_escape_string($_GET['civi'])."', nom='".mysql_real_escape_string($_GET['nom'])."', prenom='".mysql_real_escape_string($_GET['prenom']). "', poste='".mysql_real_escape_string($_GET['poste']). "', discipline='".mysql_real_escape_string($_GET['discipline']). "' WHERE PRID=".$_GET['id'];
        mysql_query($query, $ma_base);
    }
    if ($_GET['action'] == 3)
    {
        $query = "INSERT INTO personnel(PRID,civilite,nom,prenom,poste,discipline) VALUES('','".mysql_real_escape_string($_GET['civi'])."','".mysql_real_escape_string($_GET['nom'])."','".mysql_real_escape_string($_GET['prenom'])."','".mysql_real_escape_string($_GET['poste'])."','".mysql_real_escape_string($_GET['discipline'])."')";
        mysql_query($query, $ma_base);
    }
}

$query = "SELECT * FROM personnel ORDER BY nom,prenom";
$liste_profs=mysql_query($query,$ma_base);
?>
<div id="statut"></div>

<table border=1 cellpadding=0 cellspacing=0>
    <tr>
        <th rowspan="2" width = 75px></th>
		<th colspan="6" class="TDligne2"><?php echo mysql_num_rows($liste_profs); ?> enregistrements</th>
	</tr>
	<tr>
		<th width = "70px" BGCOLOR="#99CCFF">civilite</th>
		<th width = "200px" BGCOLOR="#99CCFF">Nom</th>
		<th width = "120px" BGCOLOR="#99CCFF">prenom</th>
		<th width = "130px" BGCOLOR="#99CCFF">Poste</th>
		<th width = "200px" BGCOLOR="#99CCFF">Discipline</th>
		<th width = "200px"><input type='button' onClick="javascript:add_personnel()" value='Ajouter une personne'></th>
	</tr>
	<tr id="addPersonnel">
	</tr>

	
<?php //autre lignes du tableau
for($j=0;$j<mysql_num_rows($liste_profs);$j++) // enumere les absences
{
    $PrID = mysql_result($liste_profs,$j,"PrID");
    echo "<tr><td>";
    $civilite = mysql_result($liste_profs,$j,"civilite");
    $nom = mysql_result($liste_profs,$j,"nom");
    $prenom = mysql_result($liste_profs,$j,"prenom");
    $poste = mysql_result($liste_profs,$j,"poste");
    $discipline = mysql_result($liste_profs,$j,"discipline");

?>
    <input border=0 src="ico/supp.gif" type=image onClick="javascript:window.location='personnel.php?action=1&id=<?php echo $PrID ?>';" align="middle" > 
    <input border=0 src="ico/edit.gif" type=image onClick="javascript:edit_personnel('text','<?php echo addslashes($civilite) ?>','<?php echo addslashes($nom) ?>','<?php echo addslashes($prenom) ?>', '<?php echo addslashes($poste) ?>', '<?php echo addslashes($discipline) ?>','<?php echo $j?>', <?php echo $PrID ?>);" align="middle" > 
    <input border=0 src="ico/view.gif" type=image onClick="javascript:window.location='voir_personnel.php?id=<?php echo $PrID?>'" align="middle"> 
    </td>
<?php
    $bgColor = ($j % 2) ? "#CCFFCC" : "#66FFFF";

    echo "<td bgcolor=$bgColor id='civi$j'>$civilite</td>";
    echo "<td bgcolor=$bgColor id='nom$j'>$nom</td>";
    echo "<td bgcolor=$bgColor id='prenom$j'>$prenom</td>";
    echo "<td bgcolor=$bgColor id='poste$j'>$poste</td>";
    echo "<td bgcolor=$bgColor id='discipline$j'>$discipline</td>";
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

