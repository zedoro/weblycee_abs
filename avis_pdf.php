<?php
require "./modele.php";
require "./db_fonction.php";

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

// ###################### Selection du des textes du bordereau  ################################################################

$querytxt =  "SELECT * FROM textes WHERE categorie = '".$categorie."'";
$textes = mysql_query($querytxt,$ma_base);

// passage de utf8 à iso pour les classes fpdf
$titre_page = utf8_decode(mysql_result($textes,0,"titre"));
$intro = utf8_decode(mysql_result($textes,0,"intro_multi"));
$bas = utf8_decode(mysql_result($textes,0,"bas_intro_multi"));


if ($categorie == "Examen") $bordereau_double = true;
else $bordereau_double = false;

// ####################### composition du bordereau     ########################################################################

$pdf=new BORDEREAU(); // crée un bordereau basée sur modele.php

// ------------------------   création page -----------------------
$pdf->titre_page = $titre_page;
$pdf->AliasNbPages();
$pdf->AddPage();

$civilite = mysql_result($abs,0,"civilite");
$nom = utf8_decode(mysql_result($abs,0,"nom"));
$prenom = utf8_decode(mysql_result($abs,0,"prenom"));
	
$pdf->haut($intro);

$pdf->titre_table($categorie);
$next_abs_haut = $pdf->GetY();

$pdf->SetY(22);
$pdf->nom($civilite,$nom,$prenom);

if ($bordereau_double)
{
	$pdf->SetXY(10,170);
	$pdf->date_impression();

	$pdf->titre_table($categorie);
	$next_abs_bas = $pdf->GetY();
	
	$pdf->SetY(170);
	$pdf->nom($civilite,$nom,$prenom);

}


// ------------------------   détails  -----------------------
$last = mysql_num_rows($abs);
for ($i=0;$i<$last;$i++)
    {
	// -------- collecte des infos -----------
	$motif = utf8_decode(mysql_result($abs,$i,"examen"));
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
	if($date_debut == $date_fin)
	{
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
	}		
	else
		$horaire = " au ";
	
	//-------------------- lignes pour chaque absence -----------------------------
	
	$pdf->SetY($next_abs_haut);
	if ($date_debut == $date_fin)
	{
		$pdf->detail_table($joursemdebut." ".$jour_debut,$mois_debut,$horaire,$lieux,$motif."  ".$detail);
	}
	else
	{
		$inter = intval((strtotime($date_fin)-strtotime($date_debut))/86400)+1;
		$pdf->detail_table($joursemdebut." ".$jour_debut,$mois_debut,$horaire,"","");
		$pdf->detail_table($joursemfin." ".$jour_fin,$mois_fin,$inter." jours",$lieux,$motif."  ".$detail);
	}
	$next_abs_haut = $pdf->GetY();
	if ($i == $last -1)
	{
		$pdf->bas($bas);
	}
	if ($bordereau_double)
	{
		$pdf->SetY($next_abs_bas);
		if ($date_debut == $date_fin)
		{
			$pdf->detail_table($joursemdebut." ".$jour_debut,$mois_debut,$horaire,$lieux,$motif."  ".$detail);
		}
		else
		{
			$inter = intval((strtotime($date_fin)-strtotime($date_debut))/86400)+1;
			$pdf->detail_table($joursemdebut." ".$jour_debut,$mois_debut,$horaire,"","");
			$pdf->detail_table($joursemfin." ".$jour_fin,$mois_fin,$inter." jours",$lieux,$motif."  ".$detail);
		}
		$next_abs_bas = $pdf->GetY();
		if ($i == $last -1)
		{
		$pdf->signature();
		}
	}	
	// enregistre l'absence comme imprimée
	$update_print = mysql_query("UPDATE absences SET print='1' WHERE absences.PRID=".$prID." AND absences.categorie='".$categorie."'",$ma_base); 
	
    }
$pdf->Output();

?>
