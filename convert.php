<?php

function convertPrint()
{
    $base1 = mysql_pconnect ('localhost', 'root', 'lldjolcdt') or die("c1");
    $base2 = mysql_pconnect ('localhost', 'abs_db', 'qnjtvpvc') or die("c2");
    mysql_select_db('absences',$base1) or die("what");
    $liste = mysql_query("SELECT print,AbID FROM absences",$base1) or die("Fuck");
    mysql_select_db('absence_prof',$base2);
    for($i=0;$i<mysql_num_rows($liste);$i++)
    {
        $query = "UPDATE absences SET print=".mysql_result($liste, $i, 'print')." WHERE ABID = ".mysql_result($liste, $i,'AbID');
        mysql_query($query, $base2);
        echo $query;
        echo "</br>";
    }
}

function convertProfs()
{
    $base1 = mysql_pconnect ('127.0.0.1', 'root', 'lldojlcdt');
    $base2 = mysql_pconnect ('127.0.0.1', 'abs_db', 'qnjtvpvc');
    mysql_select_db('absences',$base1);
    $liste_prof = mysql_query("SELECT PrID,nom,prenom,discipline,statut,civilite FROM profs ORDER BY PrID",$base1);
    mysql_select_db('absence_prof',$base2);
    for($i=0;$i<mysql_num_rows($liste_prof);$i++)
    {
        $prid = mysql_result($liste_prof, $i, 'PrID');
        $civilite = mysql_result($liste_prof, $i, 'civilite');
        $nom = mysql_result($liste_prof, $i, 'nom');
        $prenom = mysql_result($liste_prof, $i, 'prenom');
        $discipline = mysql_result($liste_prof, $i, 'discipline');
        $requete = "INSERT INTO personnel (PRID, civilite, nom, prenom, poste, discipline) VALUES('$prid', '$civilite', '$nom', '$prenom', '', '$discipline')";
        mysql_query($requete, $base2);
    }
    mysql_close($base1);
    mysql_close($base2);
}

function convertLieu()
{
    $base1 = mysql_pconnect ('localhost', 'root', 'lldjolcdt') or die("c1");
    $base2 = mysql_pconnect ('localhost', 'abs_db', 'qnjtvpvc') or die("c2");
	mysql_select_db('absences',$base1);
    $liste = mysql_query("SELECT lieux FROM absences GROUP BY lieux",$base1);
    mysql_select_db('absence_prof',$base2);
    for($i=0;$i<mysql_num_rows($liste);$i++)
    {
        $lieu = mysql_result($liste, $i, 'lieux');
        $lieu = addslashes($lieu);
        echo $i." - ".$lieu;
        echo "</br>";
        if($lieu != '')
        {
            if($lieu == 'LLA Mulhouse')
        $requete = "INSERT INTO lieux (LID, nom_lieu, interne, capacite, adresse, temps_trajet, distance_trajet) VALUES('', 'LLA', 1, '', '', '', '')";
            else
        $requete = "INSERT INTO lieux (LID, nom_lieu, interne, capacite, adresse, temps_trajet, distance_trajet) VALUES('', '$lieu', 0, '', '', '', '')";
        mysql_query($requete, $base2);
        }
    }
    mysql_close($base1);
    mysql_close($base2);
}

function convertAbsences()
{
    $base1 = mysql_pconnect ('localhost', 'root', 'lldjolcdt') or die("c1");
    $base2 = mysql_pconnect ('localhost', 'abs_db', 'qnjtvpvc') or die("c2");
	mysql_select_db('absences',$base1);
    $liste = mysql_query("SELECT * FROM absences",$base1);
    mysql_select_db('absence_prof',$base2);
	echo mysql_num_rows($liste)." lignes à importer <BR>";
    for($i=0;$i<mysql_num_rows($liste);$i++)
    {
        $date_saisie = mysql_result($liste, $i, 'date_saisie');
        $abid = mysql_result($liste, $i, 'AbID');
        $prid = mysql_result($liste, $i, 'PrID');
        $confirm = mysql_result($liste, $i, 'confirm');
        $print = mysql_result($liste, $i, 'print');
        $date_saisie = mysql_result($liste, $i, 'date_saisie');
        //date debut et fin
        $date_abs = mysql_result($liste, $i, 'date_abs');
        $tranche = mysql_result($liste, $i, 'tranche');
        $debut = ($tranche == 'JOURNEE' || $tranche == 'MATIN') ? " 08:00:00" : " 13:00:00";
        $fin = ($tranche == 'JOURNEE' || $tranche == 'APRES-MIDI') ? " 18:00:00" : " 13:00:00";
        $date_debut = $date_abs . $debut;
        $date_fin = $date_abs . $fin;
        //
        $categorie = mysql_result($liste, $i, 'categorie');
        $examen = mysql_result($liste, $i, 'motif');
        $details = mysql_result($liste, $i, 'detail');
        //lieux
        $nom = mysql_result($liste, $i, 'lieux');
        $lid;
        if($nom == '')
            $lid = 0;
        else
        {
            if($nom == 'LLA Mulhouse')
                $nom = 'LLA';
            $requete = "SELECT LID from lieux WHERE nom_lieu='" . $nom . "'";
            //echo $requete;
            $foo = mysql_query($requete, $base2);
            if(is_bool($foo))
                $lid = 0;
            else
                $lid = mysql_result($foo, 0, 'LID');
        }
		echo "LID ";$lid." - ";
        $ordonateur = mysql_result($liste, $i, 'ordonateur');
        $ordonateur = addslashes($ordonateur);
        $categorie = addslashes($categorie);
        $examen = addslashes($examen);
        $details = addslashes($details);
        $etat = mysql_result($liste, $i, 'confirm');
        $affiche = mysql_result($liste, $i, 'affiche');
        $requete = "INSERT INTO absences (date_saisie,ABID,PRID,date_debut,date_fin,categorie,examen,details,ordonateur,etat,afficher,LID,fait,print) VALUES('$date_saisie', $abid, $prid, '$date_debut','$date_fin','$categorie','$examen','$details', '$ordonateur', $etat,$affiche,$lid,$confirm,$print)";
		echo "ligne ".$i." - ".$requete."<br>";
        mysql_query($requete, $base2) or die('Erreur de selection '.mysql_error());
    }
    mysql_close($base1);
    mysql_close($base2);
}

//convertPrint();
//convertLieu();
//convertAbsences();
//convertProfs();
?>
