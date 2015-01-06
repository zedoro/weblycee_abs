<?php
require "./modele.php";
require "./db_fonction.php";

// ###############selection de l'absence  ou des absences de m�me cat�gorie #########################################################

$abID = $_GET['abID']; // ID de l'absence pass�e en GET dans l'URL
$prID = $_GET['prID']; // r�cup�re l'ID du prof
$categorie = $_GET['categorie']; // r�cup�re la categorie d'absence
$printstatus = $_GET['printstatus']; // r�cup�re la categorie d'absence

// prend toutes les absences de m�mes cat�gorie non imprim�es ou seulement l'absences selectionn�e si elle est d�ja imprim�e
$ma_base = connect_db();
$query = "SELECT * FROM personnel,absences,lieux WHERE absences.LID = lieux.LID AND personnel.PRID = absences.PRID AND absences.PRID=".$prID." AND absences.categorie='".$categorie."' ";
$query .= " AND absences.preset = 0 ";
if($printstatus==0) $query .= "AND absences.print=0"; else $query .="AND absences.ABID =".$abID;  // si avis d�ja imprim�, ne r�imprime que cet avis, sinon imprime tous les avis non imprim� de m�me cat�gorie
// echo $query."<br>"; // DEBUG

$abs=mysql_query($query,$ma_base); //Liste des absences � imprimer pour ce profs
// echo "<br>lignes:".mysql_num_rows($abs); // DEBUG

// d�termine 48h avant la 1�re convocation

$last = mysql_num_rows($abs);
for ($i=0;$i<$last;$i++)
{
$limite = mysql_result($abs,$i,'date_debut');
if ($limite >= mysql_result($abs,$i,'date_debut')) $limite = mysql_result($abs,$i,'date_debut');
}

// d�termine le gestionnaire de la premi�re convocation et le lieu de retrait

$query_gest = "SELECT usertxt FROM users WHERE username = '".mysql_result($abs,0,'gest')."'";
$result_gest = mysql_query($query_gest,$ma_base);
$usertxt = mysql_result($result_gest,0,'usertxt');

// ###################### Selection des textes du bordereau  ################################################################

$querytxt =  "SELECT * FROM textes WHERE categorie = '".$categorie."'";
$textes = mysql_query($querytxt,$ma_base);

// passage de utf8 � iso pour les classes fpdf
$titre_page = utf8_decode(mysql_result($textes,0,"titre"));

$intro = utf8_decode(mysql_result($textes,0,"intro_multi"));
$usertxt = utf8_decode($usertxt);
$intro = str_replace( '[gest]',$usertxt,$intro);

$bas = utf8_decode(mysql_result($textes,0,"bas_intro_multi"));


if ($categorie == "Examen" || $categorie == "Stage") $bordereau_double = true;
else $bordereau_double = false;

// ####################### composition du bordereau     ########################################################################

$pdf=new BORDEREAU(); // cr�e un bordereau bas�e sur modele.php

// ------------------------   cr�ation page -----------------------
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
	$pdf->SetFont('Times','B',12);
	setlocale(LC_TIME, "fr");
	$pdf->SetXY(40,155); // cadre accus� de r�ception
	$pdf->Cell(160,8," ACCUSE DE RECEPTION imprim� le ".strftime('%A %d %B %Y � %H:%M'),1,0,'C');
	
	$pdf->SetXY(20,165); // cadre nom pr�nom
	$pdf->SetFont('Times','B',14);
	$pdf->Cell(100,8,$civilite." ".$nom." ".$prenom,0,1,"L");
	
	list($date_limite, $heure_limite) = explode(" ", $limite);
	$date_limite = date('Y-m-d',strtotime($date_limite.' - 2 days'));
	list($annee_limite,$mois_limite,$jour_limite) = explode("-",$date_limite);
	$date_limite = $jour_limite."/".$mois_limite."/".$annee_limite;
	
	$pdf->SetFont('Times','',12);
	$pdf->SetXY(20,175); // choix 1
	$pdf->Cell(4,4,"",1,0,'L');
	$pdf->Write(4,"Je me rends � la convocation, fiche d'absence/remplacement � rendre avant le ");
	$pdf->SetFont('Times','B',12);
	$pdf->Write(4,$date_limite);
	
	$pdf->SetFont('Times','',12);
	$pdf->SetXY(20,182); // choix 2
	$pdf->Cell(4,4,"",1,0,'L');
	$pdf->Cell(5,5,"Je ne me rends pas � la convocation pour le motif :",0,0,'L');
	
	$pdf->SetXY(10,200); // tableau ligne de titre
	$pdf->titre_table($categorie);
	$next_abs_bas = $pdf->GetY();
	
	

}


// ------------------------   d�tails  -----------------------

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
				$horaire = 'Journ�e';
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
	// enregistre l'absence comme imprim�e
	$update_print = mysql_query("UPDATE absences SET print='1' WHERE absences.PRID=".$prID." AND absences.categorie='".$categorie."'",$ma_base); 
	
    }
$pdf->Output();

?>
