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
mysql_select_db($mysql_serv_DB,$lien);
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
        if (isset($selected_id) && mysql_result($liste_prof,$i,"PrID")==$selected_id)
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

function formDate($prefix, $disabled)
{
    $disabled = $disabled == 1 ? "DISABLED" : "";
    echo "
    <select name=\"".$prefix."Day\" id=\"".$prefix."Day\"".$disabled.">
                                <option value=\"\" selected></option>
                                <option value=\"1\" selected>1</option>
                                <option value=\"2\">2</option>
                                <option value=\"3\">3</option>
                                <option value=\"4\">4</option>
                                <option value=\"5\">5</option>
                                <option value=\"6\">6</option>
                                <option value=\"7\">7</option>
                                <option value=\"8\">8</option>
                                <option value=\"9\">9</option>
                                <option value=\"10\">10</option>
                                <option value=\"11\">11</option>
                                <option value=\"12\">12</option>
                                <option value=\"13\">13</option>
                                <option value=\"14\">14</option>
                                <option value=\"15\">15</option>
                                <option value=\"16\">16</option>
                                <option value=\"17\">17</option>
                                <option value=\"18\">18</option>
                                <option value=\"19\">19</option>
                                <option value=\"20\">20</option>
                                <option value=\"21\">21</option>
                                <option value=\"22\">22</option>
                                <option value=\"23\">23</option>
                                <option value=\"24\">24</option>
                                <option value=\"25\">25</option>
                                <option value=\"26\">26</option>
                                <option value=\"27\">27</option>
                                <option value=\"28\">28</option>
                                <option value=\"29\">29</option>
                                <option value=\"30\">30</option>
                                <option value=\"31\">31</option>
                            </select>
                            <select id=\"".$prefix."Month\" name=\"".$prefix."Month\"".$disabled.">
                                <option value=\"\" selected></option>
                                <option value=\"01\" selected>Janvier</option>
                                <option value=\"02\">Fevrier</option>
                                <option value=\"03\">Mars</option>
                                <option value=\"04\">Avril</option>
                                <option value=\"05\">Mai</option>
                                <option value=\"06\">Juin</option>
                                <option value=\"07\">Juillet</option>
                                <option value=\"08\">Aout</option>
                                <option value=\"09\">Septembre</option>
                                <option value=\"10\">octobre</option>
                                <option value=\"11\">Novembre</option>
                                <option value=\"12\">Decembre</option>
                            </select> 
                            <select name=\"".$prefix."Year\" id=\"".$prefix."Year\"".$disabled.">
                                <option value=\"2012\">2012</option>
                                <option value=\"2013\">2013</option>
                                <option value=\"2014\">2014</option>
                                <option value=\"2015\">2015</option>
                                <option value=\"2016\">2016</option>
                                </select> 
                                ";
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
?>