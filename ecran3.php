<!DOCTYPE html> 
<HTML>
<HEAD>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<TITLE>Fond d'Ecran Dynamique</TITLE>
<STYLE type="text/css">
<!--
.format {
position: absolute;
font-family:Verdana;
color:200000;
font-size:30px;
letter-spacing:2;
background-color:#F0FFF0;
top:40;
left:40;
}

.jour {
font-family:Verdana;
color:004000;
font-size:25px;
font-weight:bold;
background-color:#AAAAFF;
}

.liste {
font-family:Verdana;
color:000040;
font-size:15px;

body {
color:black;
background-image:url(fond.jpg);
background-repeat:no-repeat;
background-attachment:fixed;
}
}
-->
</STYLE>

<SCRIPT LANGUAGE="JavaScript">
<!--
var Hauteur=100;         //Hauteur du div   
var pos_x=20;            //Position du div (marge gauche en pixel)
var pos_y;    //Position du div (vertical)
var MonObjet;

function GetObject(ID) {
   if (document.getElementById) {
      return document.getElementById(ID);
   }
   if (document.layers) {
      return eval('document.'+ID);
   }
   if (document.all) {
      return eval('document.all.'+ID);
   }
}

function MoveTo(MyObject, x, y) {   // Déplacement du DIV
   if (document.getElementById||document.all) {
      MyObject.style.left = x + "px";
      MyObject.style.top = y + "px";
      return;
   }
   if (document.layers) MyObject.moveTo(x, y);
}

function InitObjet(ID) {            //Initialisation du DIV
   MonObjet = GetObject(ID);
   pos_y = document.body.clientHeight/2;
   Hauteur = get_div_height(ID);
   if ((Hauteur+20) > document.body.clientHeight){ // défilement si le texte ne tient pas sur la fenêtre
   		MoveTo(MonObjet, pos_x, pos_y);
   		scroll();
   }
   else MoveTo(MonObjet, pos_x, 10);
}

function get_div_height(ID){
	var doc = document.getElementById(ID);
	if (document.all) // ok I.E
		{
		H = doc.offsetHeight;
		}
	else // ok firefox.0.9.2 , pas mozilla.1.0 ni netscape.7.02
		{
		H = document.defaultView.getComputedStyle(doc, null).height;
		H = eval ( H.substr( 0 , ( H.length - 2) )); // enleve le px à la fin
		}
	return H;
	}

function scroll() {                  // Défilement du DIV
   if (pos_y > (-1 * Hauteur)) {      //Teste si le DIV est complètement sorti
      pos_y--;
      MoveTo(MonObjet, pos_x, pos_y)
   }
   else {
      pos_y=document.body.clientHeight/2; // démmarre le scroll au milieu de l'écran
      MoveTo(MonObjet, pos_x, pos_y)
   }
   var timer = setTimeout('scroll();',20);
}
//-->
</SCRIPT>

<BODY>

<DIV id="Texte" class="format">
<h2 align="center">Prévision des absences de professeurs</h2><BR>
<?php
require "db_fonction.php";
/*
function dateFR($date)
{
    $joursem = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
    list($annee, $mois, $jour) = explode('-', $date);
    $timestamp = mktime (0, 0, 0, $mois, $jour, $annee);
    return $joursem[date("w",$timestamp)];
}

$ma_base = connect_db();
$date_courante = date("Y-m-d"); //date courante au format mysql
$date_max = date("Y-m-d",mktime(0,0,0,date("m"),date("d")+$nb_jour_affichage,date("Y")));
setlocale(LC_TIME, "fr"); // passe au système de date français
// enumere les dates ayant des absences confirmée égale ou après la date du jour

echo "<table width='100%' class=\"liste\">";
//echo "<tr><td colspan='3' width='100%' class=\"jour\">".strftime('%A %d %B %Y' ,$date_courante)."</td></tr>\n";
for ($k=1; $k < $nb_jour_affichage; $k++)
{
	$query = "SELECT date_debut,date_fin,PRID FROM absences WHERE afficher='1' AND TO_DAYS('$date_courante') >= TO_DAYS(date_debut) AND TO_DAYS('$date_courante') <= TO_DAYS(date_fin)";
	$liste_date=mysql_query($query,$ma_base);
	//echo "<tr><td>".$query."</tr></td>";//DEBUG
	//echo "<tr><td>lignes ".mysql_num_rows($liste_date)." ".$k."</tr></td>";//DEBUG
	
    for($i=0;$i<mysql_num_rows($liste_date);$i++)
    {
        $date_d = mysql_result($liste_date,$i,'date_debut');
        $date_f = mysql_result($liste_date,$i,'date_fin');
        $horaire;
        list($date_debut, $heure_debut) = explode(" ", $date_d);
        list($date_fin, $heure_fin) = explode(" ", $date_f);
        $heure_debut = substr($heure_debut,0,5);
		$heure_fin = substr($heure_fin,0,5);
		if($heure_debut == "08:00")
        {
            if($heure_fin == "13:00")
                $horaire = 'Matin';
            elseif ($heure_fin == "18:00")
                $horaire = 'Journée';
        }
        elseif($heure_debut == "13:00" && $heure_fin == "18:00")
            $horaire = 'Apres-Midi';
        else
            $horaire = $heure_debut . " - " . $heure_fin;
        if($i == 0)
        {
            list($y,$m,$d) = explode("-",$date_courante);
            $j = dateFR($date_courante);
            $jour = $j . " " . $d . "/" . $m . "/" . $y;
            echo "<tr><td align='center' width='30%'>".$jour."</td></tr>";
        }
        $query = "SELECT * FROM personnel WHERE personnel.PRID = ".mysql_result($liste_date,$i,'PRID');
        $personne=mysql_query($query,$ma_base);
        for($j=0;$j<mysql_num_rows($personne);$j++)
        {
            echo "<tr><td width='30%'>".$horaire."</td>
			<td width='40%'>".mysql_result($personne,$j,"civilite")." ".mysql_result($personne,$j,"nom")."</td>
			<td  width='40%'>".mysql_result($personne,$j,"discipline")."</td></tr>\n";
			
        }
    }
    $date_courante = date('Y-m-d', strtotime("+". $k. " day"));
}
echo "</table><br>";*/
?>
</DIV>
</BODY>
</HTML> 
