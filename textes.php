<?php
    require_once("./menu.php");
    $menu = affiche_menu();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="content-type">
	<link href="css/menu.css" type="text/css" rel="stylesheet" />
	<link href="absences.css" rel="stylesheet" type="text/css">
	<title>edition textes</title>
</head>
<body>
<?php
echo $menu;
//connexion à la base absences
include('db_fonction.php');
$ma_base = connect_db();
?>
<div class='corps'>
<form method="post" action="textes.php" name="edit">
	<p>Choisissez la categorie de document à éditer: </p>
	<select name="categorie" id="categorie" onchange="document.edit.submit()">
	<option value="rien">choisissez une categorie</option>
	<?php option_liste_categorie();?>
	</select>
	<input name="status" type="hidden" value="edit">
	<input type="submit" value="VALIDER">
</form>
<?php


//*******************************************************************************************************************************
// 2ème appel de la page ($_POST['status']=set) ==> mise à jour données dans la BDD, fermeture page et mise à jour page appelante
//*******************************************************************************************************************************
if( isset( $_POST['status'])&&($_POST['status']=="set"))
{
echo "Le texte à été mise a jour ";
$requete = "UPDATE textes SET titre='".$_POST['titre']."', intro='".$_POST['intro']."', bas_intro='".MyAddSlashes($_POST['bas_intro'])."', intro_multi='".MyAddSlashes($_POST['intro_multi'])."', bas_intro_multi='".MyAddSlashes($_POST['bas_intro_multi'])."' WHERE categorie='".$_POST['categorie']."'";
$update = mysql_query($requete,$ma_base);
}
//*******************************************************************************************************************************
// 2ème appel de la page ($_POST['status']=set) ==> mise à jour données dans la BDD, fermeture page et mise à jour page appelante
//*******************************************************************************************************************************
else if( isset( $_POST['status'])&&($_POST['status']=="edit"))
{
//mise à jour du texte
echo "<form method=\"post\" action=".$_SERVER['PHP_SELF']." name=\"edit\">";
echo "<h1>".$_POST['categorie']."</h1>";

$requete = "SELECT * FROM textes WHERE categorie='".$_POST['categorie']."'";
$result= mysql_query($requete,$ma_base);

echo "<h2> Titre </h2>";
echo "<textarea name=\"titre\" cols=\"100\" rows=\"1\">";
echo mysql_result($result,0,"titre");
echo "</textarea>";

echo "<H2> Texte d'intro pour absence simple </h2>";
echo "<textarea name=\"intro\" cols=\"100\" rows=\"7\">";
echo mysql_result($result,0,"intro");
echo "</textarea>";

echo "<h2> signature texte intro absence simple </h2>";
echo "<textarea name=\"bas_intro\" cols=\"100\" rows=\"2\">";
echo mysql_result($result,0,"bas_intro");
echo "</textarea>";

echo "<h2> Texte d'intro pour absences multiples </h2>";
echo "<textarea name=\"intro_multi\" cols=\"100\" rows=\"7\">";
echo mysql_result($result,0,"intro_multi");
echo "</textarea>";

echo "<h2> signature texte intro absences multiples  </h2>";
echo "<textarea name=\"bas_intro_multi\" cols=\"100\" rows=\"2\">";
echo mysql_result($result,0,"bas_intro_multi");
echo "</textarea>";

echo "<input name=\"status\" type=\"hidden\" value=\"set\">";
echo "<input name=\"categorie\" type=\"hidden\" value=\"".$_POST['categorie']."\">";
}
?>
</table>
</br>
</br>
</div>
</body>
</html>
