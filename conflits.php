<?php
    require_once("./menu.php");
    $menu = affiche_menu();
	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title>recherche de conflits de dates</title>
        <link rel="stylesheet" type="text/css" href="yui/css/fonts-min.css" />
        <link rel="stylesheet" type="text/css" href="yui/css/slider.css" />
        <link rel="stylesheet" type="text/css" href="yui/css/calendar.css" />
        <link rel="stylesheet" type="text/css" href="yui/css/button.css" />
        <link rel="stylesheet" type="text/css" href="css/slider.css" />
        <link rel="stylesheet" type="text/css" href="css/calendar.css" />
        <link href="absences.css" rel="stylesheet" type="text/css">
        <link href="css/menu.css" type="text/css" rel="stylesheet" />
        <link rel="stylesheet" href="css/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />
	</head>
	<body class="yui-skin-sam">
<?php
	echo $menu;
	include("./abs_table.inc.php");
	include('db_fonction.php');
	
	
	if (isset($_GET['filtre']))
	{
	if ($_GET['filtre'] == "tous") $filtre=""; else $filtre = " WHERE status=0";
	}
	else $filtre = " WHERE status=0";

$ma_base = connect_db();	
//##########################################################	
// AFFICHAGE DES CONFLITS DANS TOUTE LA BASE
//##########################################################	

$total_conflit = 0;
$conflit_list_query = "SELECT * FROM conflits" .$filtre." ORDER BY CFID DESC";
$conflit_list = mysql_query($conflit_list_query,$ma_base);
for ($i=0;$i<mysql_num_rows($conflit_list);$i++)
	{
	//echo "<br>".$i; //DEBUG
	$abid1 = mysql_result($conflit_list,$i,'ABID1');
	$abs1 = mysql_query("SELECT * FROM absences,personnel,lieux WHERE absences.PRID = personnel.PRID AND absences.LID = lieux.LID AND ABID=".$abid1,$ma_base);
	$date_debut1 = strtotime(mysql_result($abs1,0,'date_debut'));
	$date_fin1 = strtotime(mysql_result($abs1,0,'date_fin'));
	
	$abid2 = mysql_result($conflit_list,$i,'ABID2');
	$abs2 = mysql_query("SELECT * FROM absences,personnel,lieux WHERE absences.PRID = personnel.PRID AND absences.LID = lieux.LID AND ABID=".$abid2,$ma_base);
	$date_debut2 = strtotime(mysql_result($abs2,0,'date_debut'));
	$date_fin2 = strtotime(mysql_result($abs2,0,'date_fin'));
	
	
	$CFID = mysql_result($conflit_list,$i,'CFID');
	$comment = mysql_result($conflit_list,$i,'comment');
	$status = mysql_result($conflit_list,$i,'status');
	
	
	if (($date_debut1 >= $date_debut2 && $date_debut1 <= $date_fin2)
		|| ($date_fin1 >= $date_debut2 && $date_fin1 <= $date_fin2)
		|| ($date_debut2 >= $date_debut1 && $date_debut2 <= $date_fin1)
		|| ( $date_fin2 >= $date_debut1 && $date_fin2 <= $date_fin1 ))
		{
		echo "\n<fieldset id=\"fs_conflits\"><legend>conflits</legend>";
		echo "<table><tr>";
		echo "\n<td valign=\"center\"> commentaire : </td>";
		echo "\n<td valign=\"center\"><textarea rows='3' cols='80' name='comment".$CFID."' readonly>".$comment."</textarea></td>";
		echo "\n<td valign=\"center\" align=\"center\">Validé : </td>";
		echo "\n<td valign=\"center\">";
		if($status == 0) echo "<img border=0 src=\"ico/check_off.gif\" type=image>\n";	else echo "<img border=0 src=\"ico/check_on.gif\" type=image>\n";
		echo "</td></tr></table>";
			
		mini_tableau($abs1,0);
		echo "<a href=absences_new.php?action=3&abid=".$abid1.">editer l'absence</a>";
		mini_tableau($abs2,0);
		echo "<a href=absences_new.php?action=3&abid=".$abid2.">editer l'absence</a>";
		echo "\n</fieldset>";
		$total_conflit++;
		}
	else mysql_query( "DELETE FROM conflits WHERE CFID=".$CFID,$ma_base);
		
		
	}

ob_flush();
flush();
ob_flush();
flush(); 	
	
//##########################################################	
// RECHERCHE DE CONFLITS DANS TOUTE LA BASE
//##########################################################	
$query = mysql_query("SELECT * FROM absences,personnel,lieux WHERE absences.PRID = personnel.PRID AND absences.LID = lieux.LID ORDER BY absences.date_debut",$ma_base);
for ($j=0;$j<mysql_num_rows($query);$j++)
{	
	$startDate = mysql_result($query,$j,"date_debut");
	$endDate = mysql_result($query,$j,"date_fin");
	$PRID = mysql_result($query,$j,"PRID");
	$abid = mysql_result($query,$j,"ABID");
		
	$conflits_query = "SELECT * FROM absences,personnel,lieux 
	WHERE absences.PRID = personnel.PRID 
	AND absences.LID = lieux.LID AND absences.PRID = ".$PRID." 
	AND (
		(date_debut >= '".$startDate."' AND date_debut <= '".$endDate."')
		OR (date_fin >= '".$startDate."' AND date_fin <= '".$endDate."')
		OR ('".$startDate."' >= date_debut AND '".$startDate."' <= date_fin)
		OR ( '".$endDate."' >= date_debut AND '".$endDate."' <= date_fin )
		)
	AND absences.ABID != ".$abid." 	
	AND date_fin >= NOW() 
	ORDER BY date_debut"; 
	//echo $conflits_query."<br>"; // DEBUG
	$conflits_result = mysql_query($conflits_query, $ma_base);
	$nb_conflits = mysql_num_rows($conflits_result);
			
	if ($nb_conflits >= 1) // si un conflit détecté
	{
		for ($i=0;$i<$nb_conflits;$i++)  // pour chaque absence en conflit, recherhce l'enregistrement de conflit
			{
			$ABID1 = $abid;
			$ABID2 = mysql_result($conflits_result,$i,'ABID');
			$conflit_exist = mysql_query("SELECT * FROM conflits WHERE ((ABID1=".$ABID1." AND ABID2=".$ABID2.") OR (ABID1=".$ABID2." AND ABID2=".$ABID1."))",$ma_base);
			if (mysql_num_rows($conflit_exist) == 0) // pas encore d'enregistrment du conflit
				{
				$insert_conflit_query = "INSERT INTO conflits (ABID1,ABID2,comment,status) VALUES (".$ABID1.",".$ABID2.",'',0)";
				$insert_conflit = mysql_query($insert_conflit_query, $ma_base) or die();
				}
			}
	}
	
	
}

echo "<br><H1>".$total_conflit." conflis de dates AU TOTAL</H1></br>"; // DEBUG
?>

	</body>
</html>
