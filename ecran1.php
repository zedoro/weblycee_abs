<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>ecran dynamique V1</title>
    <link href="css/dyn_screen.css" type="text/css" rel="stylesheet">
  </head>
  <body>
	<h2 id='titre'>Prévision des absences de professeurs</h2>
	<div id="texte">
	<?php
	require "db_fonction.php";

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
	
	// enumere les dates ayant des absences à afficher

	for ($k=1; $k < $nb_jour_affichage; $k++)  // pour chaque jour à afficher
	{
		$query = "SELECT date_debut,date_fin,PRID FROM absences WHERE afficher='1' AND TO_DAYS('$date_courante') >= TO_DAYS(date_debut) AND TO_DAYS('$date_courante') <= TO_DAYS(date_fin)";
		$liste_date=mysql_query($query,$ma_base);
		
		for($i=0;$i<mysql_num_rows($liste_date);$i++)  // pour chaque absence
		{
			// affiche le jour sur la première ligne d'une série d'absences
			if($i == 0) 
			{
				list($y,$m,$d) = explode("-",$date_courante);
				$j = dateFR($date_courante);
				$jour = $j . " " . $d . "/" . $m . "/" . $y;
				echo "<h2 class='date'>".$jour."</h2>";
			}
			// calcul de la durée à afficher
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
			// recupere le nom du prof et la discipline
			$query = "SELECT * FROM personnel WHERE personnel.PRID = ".mysql_result($liste_date,$i,'PRID');
			$personne=mysql_query($query,$ma_base);
			
			for($j=0;$j<mysql_num_rows($personne);$j++)
			{
				echo "<div class='ligne'><h3 class='info duree'>".$horaire."</h3>
				<h3 class='info nom'>".mysql_result($personne,$j,"civilite")." ".mysql_result($personne,$j,"nom")."</h3>
				<h3 class='info discipline'>".mysql_result($personne,$j,"discipline")."</h3></div>\n";
				
			}
		}
		// passe à la date suivante
		$date_courante = date('Y-m-d', strtotime("+". $k. " day"));
	}
	?>
	</div>
</body>
  </body>
</html>
