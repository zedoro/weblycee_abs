<?php
require "./modele.php";
require "./db_fonction.php";

$ma_base = connect_db();
$date_courante = date("Y-m-d"); //date courante au format mysql
setlocale(LC_TIME, "fr");

$pdf=new PDF();
$pdf->titre_page = 'PREVISION DES ABSENCES DE PROFESSEURS';
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$pdf->SetY(35);

for ($k=1; $k < $nb_jour_affichage; $k++)
{
    
	$query = "SELECT date_debut,date_fin,PRID FROM absences WHERE afficher='1' AND TO_DAYS(date_debut) <= TO_DAYS('$date_courante') AND TO_DAYS(date_fin) >= TO_DAYS('$date_courante')";
	//  afficher='1' AND TO_DAYS(date_debut) <= TO_DAYS('$date_courante') AND TO_DAYS(date_fin) >= TO_DAYS('$date_courante')
    $liste_date=mysql_query($query,$ma_base);

    for($i=0;$i<mysql_num_rows($liste_date);$i++)
    {
        $pdf->SetFont('Times','',11);
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
            $pdf->SetWidths(array(50,50,50,50));
            $pdf->SetAligns(array('L','L','L', 'L'));
            $pdf->Row(array($jour,'','',''));
        }
        $query = "SELECT * FROM personnel WHERE personnel.PRID = ".mysql_result($liste_date,$i,'PRID');
        $personne=mysql_query($query,$ma_base);
        for($j=0;$j<mysql_num_rows($personne);$j++)
        {
            $pdf->SetWidths(array(50,50,50,50));
            $pdf->SetAligns(array('L','L','L', 'L'));
            $pdf->Row(array(
                '',
                $horaire,
                mysql_result($personne,$j,"civilite")." ".mysql_result($personne,$j,"nom")." ",
                mysql_result($personne,$j,"discipline")));
        }
    }
    $date_courante = date('Y-m-d', strtotime("+". $k. " day"));
}
$pdf->Output();
?>
