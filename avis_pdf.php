<?php
require "modele_lla.php";
require "db_fonction.php";

function dateFR($date)
{
    $joursem = array("Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam");
    list($annee, $mois, $jour) = explode('-', $date);
    $timestamp = mktime (0, 0, 0, $mois, $jour, $annee);
    return $joursem[date("w",$timestamp)];
}
// ###############selection de l'absence  ou des absences de même catégorie #########################################################

$abID = $_GET['abID']; // ID de l'absence passée en GET dans l'URL
$prID = $_GET['prID']; // récupère l'ID du prof
$categorie = $_GET['categorie']; // récupère la categorie d'absence
$printstatus = $_GET['printstatus']; // récupère la categorie d'absence

// prend toutes les absences de mêmes catégorie non imprimées ou seulement l'absences selectionnée si elle est déja imprimée
$ma_base = connect_db();
$query = "SELECT * FROM personnel,absences,lieux WHERE absences.LID = lieux.LID AND personnel.PRID = absences.PRID AND absences.PRID=".$prID." AND absences.categorie='".$categorie."' ";
$query .= " AND absences.preset = 0 ";
if($printstatus==0) $query .= "AND absences.print=0"; else $query .="AND absences.ABID =".$abID;
// echo $query."<br>"; // DEBUG

$abs=mysql_query($query,$ma_base); //Liste des absences à imprimer pour ce profs
// echo "<br>lignes:".mysql_num_rows($abs); // DEBUG

// ###############################################################################################

$querytxt =  "SELECT * FROM textes WHERE categorie = '".$categorie."'";
$textes = mysql_query($querytxt,$ma_base);
// passage de utf8 à iso pour les classes fpdf
$titre_page = "\n".utf8_decode(mysql_result($textes,0,"titre"));
$intro = utf8_decode(mysql_result($textes,0,"intro_multi"));
$bas_intro = utf8_decode(mysql_result($textes,0,"bas_intro_multi"));


//echo mysql_num_rows($abs); //DEBUG

if ($categorie == "Examen") $bordereau_double = true;
else $bordereau_double = false;

// ###############################################################################################

$pdf=new BORDEREAU(); // crée un bordereau basée sur modele_lla.php

for ($i=0;$i<mysql_num_rows($abs);$i++)
    {
	// -------- collecte des infos -----------
	$abID=mysql_result($abs,$i,"ABID");
	$civilite = mysql_result($abs,$i,"civilite");
	$nom = utf8_decode(mysql_result($abs,$i,"nom"));
	$prenom = utf8_decode(mysql_result($abs,$i,"prenom"));
	$motif = utf8_decode(mysql_result($abs,$i,"examen"));
	$discipline = utf8_decode(mysql_result($abs,$i,"discipline"));
	$categorie = utf8_decode(mysql_result($abs,$i,"categorie"));
	$detail = utf8_decode(mysql_result($abs,$i,"details"));
	$lieux = utf8_decode(mysql_result($abs,$i,"nom_lieu"));
	
	
	$date_d = mysql_result($abs,$i,'date_debut');
	$date_f = mysql_result($abs,$i,'date_fin');
	list($date_debut, $heure_debut) = explode(" ", $date_d);
	list($date_fin, $heure_fin) = explode(" ", $date_f);
	
	list($annee_debut,$mois_debut,$jour_debut) = explode("-",$date_debut);
	list($annee_fin,$mois_fin,$jour_fin) = explode("-",$date_fin);
	$joursemdebut = dateFR($date_debut);
	$joursemfin = dateFR($date_fin);
			
	$horaire;
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
	// ---------------- intro ----------------
	if ($i==0)
	{
		$pdf->titre_page = $titre_page;
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->intro_haut($intro);
		$pdf->titre_table_abs($civilite,$nom,$prenom);
		$next_abs_haut = $pdf->GetY();
		
		if ($bordereau_double)
		{
			$pdf->intro_bas($bas_intro);
			$pdf->SetXY(10,170);
			$pdf->date_impression();
			$pdf->titre_table_abs($civilite,$nom,$prenom);
			$next_abs_bas = $pdf->GetY();
			$pdf->signature();
		}	
	}
	//-------------------- lignes pour chaque absence -----------------------------
	
	$pdf->SetY($next_abs_haut);
	$pdf->detail_table_abs($motif,$joursemdebut." ".$jour_debut,$mois_debut,$horaire,$lieux,$detail);
	$next_abs_haut = $pdf->GetY();
	if ($date_debut != $date_fin)
	{
		$pdf->SetY($next_abs_haut);
		$inter = intval((strtotime($date_fin)-strtotime($date_debut))/86400)+1;
		$pdf->detail_table_abs("jusqu'au",$joursemfin." ".$jour_fin,$mois_fin,$inter." jours","","");
		$next_abs_haut = $pdf->GetY();
	}
	
	if ($bordereau_double)
	{
		$pdf->SetY($next_abs_bas);
		$pdf->detail_table_abs($motif,$joursemdebut." ".$jour_debut,$mois_debut,$horaire,$lieux,$detail);
		$next_abs_bas = $pdf->GetY();
		if ($date_debut != $date_fin)
		{
			$pdf->SetY($next_abs_bas);
			$inter = intval((strtotime($date_fin)-strtotime($date_debut))/86400)+1;
			$pdf->detail_table_abs("jusqu'au",$joursemfin." ".$jour_fin,$mois_fin,$inter." jours","","");
			$next_abs_bas = $pdf->GetY();
		}
	}	
	// echo $i."<br>"; //DEBUG
	// enregistre l'absence comme imprimée
	$update_print = mysql_query("UPDATE absences SET print='1' WHERE absences.PRID=".$prID." AND absences.categorie='".$categorie."'",$ma_base); 
	
    }
$pdf->Output();

?>
