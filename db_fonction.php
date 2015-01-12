<?php
require_once('./config.inc.php');

####################################################################################
# crée une connection persistante avec le serveur Mysql
####################################################################################
function connect_db()
{
global $mysql_serv_IP;
global $mysql_serv_UID;
global $mysql_serv_passwd;
global $mysql_serv_DB;

$lien = mysql_pconnect ($mysql_serv_IP,$mysql_serv_UID,$mysql_serv_passwd);
if (!$lien) {
    die('Connexion impossible : ' . mysql_error());
}
$db_selected = mysql_select_db($mysql_serv_DB,$lien);
if (!$db_selected) {
   die ('Impossible de sélectionner la base de données : ' . mysql_error());
}
mysql_query("SET CHARACTER SET 'utf8'", $lien);
return ($lien);
}
####################################################################################
#   renvoie la liste des profs et ID comme option d'un champ de formulaire          
#   si $selected est donné, l'élément correspondant est selectionné
####################################################################################

function option_liste_prof($selected_id)
{
    $ma_base = connect_db();
    $liste_prof = mysql_query("SELECT PRID,nom,prenom FROM personnel ORDER BY nom,prenom",$ma_base);
    for($i=0;$i<mysql_num_rows($liste_prof);$i++)
    {
        echo "<option value=".mysql_result($liste_prof,$i,"PRID");
        if (isset($selected_id) && mysql_result($liste_prof,$i,"PRID")==$selected_id)
            echo " selected";
        echo ">"." ". mysql_result($liste_prof,$i,"nom")." ".mysql_result($liste_prof,$i,"prenom")."</option>\n";
    }
}

####################################################################################
#   renvoie la liste des categories comme option d'un champ de formulaire          
#   si $selected_categorie est donné, l'élément correspondant est selectionné
####################################################################################

function option_liste_categorie($selected_categorie)
{
	global $tabl_categorie;
	for ($i=0;$i<count($tabl_categorie);$i++)
	{
		echo "<option value=\"".$tabl_categorie[$i]."\"";
        if(isset($selected_categorie) && $tabl_categorie[$i] == $selected_categorie)
            echo " selected";
		echo ">";
		echo $tabl_categorie[$i]."</option>\n";
	}
}

####################################################################################
#   renvoie la liste des gestionnaires comme option d'un champ de formulaire          
#   si $selected_categorie est donné, l'élément correspondant est selectionné
####################################################################################

function option_liste_gest($selected_gest)
{
	$ma_base = connect_db();
    $liste_gest = mysql_query("SELECT username FROM users ORDER BY username",$ma_base);
    for($i=0;$i<mysql_num_rows($liste_gest);$i++)
    {
        echo "<option value=".mysql_result($liste_gest,$i,"username");
        if (isset($selected_gest) && mysql_result($liste_gest,$i,"username")==$selected_gest)
            echo " selected";
        echo ">"." ". mysql_result($liste_gest,$i,"username")."</option>\n";
    }
}
####################################################################################
#   renvoie la liste des tranches horaires comme option d'un champ de formulaire   
#   si $selected_categorie est donné, l'élément correspondant est selectionné
####################################################################################

function option_liste_tranche($selected_tranche)
{
	global $tabl_tranche;
	for ($i=0;$i<count($tabl_tranche);$i++)
	{
		echo "<option value=\"".$tabl_tranche[$i]."\"";
		if(isset($selected_tranche) && $tabl_tranche[$i] == $selected_tranche) echo " selected";
		echo ">";
		echo $tabl_tranche[$i]."</option>";
	}
}

####################################################################################
#   renvoie la liste des examens comme option d'un champ de formulaire   
#   si $selected_categorie est donné, l'élément correspondant est selectionné
####################################################################################

function option_liste_examen($selected_id)
{
    $ma_base = connect_db();
    $liste_examen = mysql_query("SELECT examen FROM absences WHERE CHAR_LENGTH(examen) > 2 GROUP BY examen ORDER BY examen ",$ma_base);
    for($i=0;$i<mysql_num_rows($liste_examen);$i++)
    {
        $examen = htmlentities(mysql_result($liste_examen,$i,"examen"), ENT_QUOTES, "UTF-8");
		echo "<option value='".$examen."'";
        if (isset($selected_id) && $examen==$selected_id)
            echo " selected";
        echo ">"." ". $examen."</option>\n";
    }
}

####################################################################################
#   ajoute les slaches devant les caractères spéciaux selon la configuration de
#   magic_quote_gpc
####################################################################################

function MyAddSlashes($chaine ) {
  return( get_magic_quotes_gpc() == 1 ?
          $chaine :
          addslashes($chaine) );
}

########################################################################
#  date française en format texte                                     
########################################################################

function dateFR($thedate)
{
	$joursem = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
	list($annee, $mois, $jour) = explode('-', $thedate);
	$timestamp = mktime (0, 0, 0, $mois, $jour, $annee);
	return $joursem[date("w",$timestamp)];
}

####################################################################################
#   renvoie une source de donnée pour l'autocopmlétion de champs input text   
####################################################################################

function autocomplete_source($field,$table)
{
    $ma_base = connect_db();
	$requete = "SELECT $field FROM $table GROUP BY $field ORDER BY $field";
	$result = mysql_query($requete,$ma_base);
	echo "source: ["; 
	for($j=0; $j < mysql_num_rows($result); $j++)
	   echo "\"".addslashes(mysql_result($result, $j))."\",";
	echo "\"\"]";
}

?>