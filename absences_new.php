<?php
require_once("./menu.php");
include("./abs_table.inc.php");

$menu = affiche_menu();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title>Saisie absence</title>
        <link rel="stylesheet" type="text/css" href="yui/css/fonts-min.css" />
        <link rel="stylesheet" type="text/css" href="yui/css/slider.css" />
        <link rel="stylesheet" type="text/css" href="yui/css/calendar.css" />
        <link rel="stylesheet" type="text/css" href="yui/css/button.css" />
        <link rel="stylesheet" type="text/css" href="css/slider.css" />
        <link rel="stylesheet" type="text/css" href="css/calendar.css" />
        <link href="absences.css" rel="stylesheet" type="text/css">
        <link href="css/menu.css" type="text/css" rel="stylesheet" />
        <link rel="stylesheet" href="css/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />
        <script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3_comp.js"></script>
        <script type="text/javascript" src="js/completion.js"></script>
        <script type="text/javascript" src="js/windows.js"></script>
        <script type="text/javascript" src="yui/js/yahoo-dom-event.js"></script>
        <script type="text/javascript" src="yui/js/dragdrop-min.js"></script>
        <script type="text/javascript" src="yui/js/slider-min.js"></script>
        <script type="text/javascript" src="yui/js/calendar-min.js"></script>
        <script type="text/javascript" src="yui/js/container_core-min.js"></script>
        <script type="text/javascript" src="yui/js/element-min.js"></script>
        <script type="text/javascript" src="yui/js/button-min.js"></script>
        <script type="text/javascript" src="js/slider.js"></script>
        <script type="text/javascript" src="js/calendar.js"></script>
		<script type="text/javascript" src="js/form_select.js"></script>
		<script type="text/javascript">
		function surligne(id,color)
		{
		if (color == "vert")
			{
			document.getElementById(id).style.borderColor='green';
			document.getElementById(id).style.backgroundColor='#33FF00';
			}
		else if (color == 'rouge')
			{
			document.getElementById(id).style.borderColor='red';
			document.getElementById(id).style.backgroundColor='#FF3333';
			}
		else if (color == 'bleu')
			{
			document.getElementById(id).style.borderColor='blue';
			document.getElementById(id).style.backgroundColor='#3333FF';
			}
		else
			{
			document.getElementById(id).style.borderColor='black';
			document.getElementById(id).style.backgroundColor='#DEDEDE';
			}
		}
		</script>
        
	</head>
	<body class="yui-skin-sam">
	<?php echo $menu;?>
	<div class='corps'>
		<form name="dates">	
<?php
include('db_fonction.php');
$ma_base = connect_db();


//###################################################################################
// appel de la page sans $_GET['action'] valeurs par défaut pour le formulaire:
//###################################################################################
if(!isset($_GET['action']))
{
	// definition des valeurs du formulaire par défaut
	// nom
	$nom;
	//date
	if (isset($_SESSION['date_abs']))
		{
		$array_debut = $_SESSION['date_abs'];
		$array_fin = $_SESSION['date_abs'];
		}
	else
		{
		setlocale(LC_TIME, "fr");
		$today = date("d-m-Y H:i:s");
		$array_debut = date_parse($today);
		$array_debut['hour']=8;
		$array_debut['minute']=0;
		$array_fin = date_parse($today);
		$array_fin['hour']=18;
		$array_fin['minute']=0;
		}
	// categorie
	$categorie = "Stage"; 
	//lieux
	$lid;
	//ordonateur
	$ordonateur;
	//examen
	$examen;
	//detail
	$details;
	//pré saisie
	if ($_SESSION['userType']==1) $presaisie = 0;
	if ($_SESSION['userType']==2) $presaisie = 1;
	$action=1; // pour inserer au prochain appel
	$gest = $_SESSION['username'];
}
//###################################################################################
// appel de la page $_GET['action'] = 1-->INSERT 2-->UPDATE 3-->EDIT 5-->GESTION CONFLIT
//###################################################################################

else if ($_GET['action'] == 1 || $_GET['action'] == 2 || $_GET['action'] == 3 || $_GET['action'] == 5 )
{
    // definition des valeurs du formulaire depuis le GET ou bien valeur par défaut
	
	//nom
	$nom = $_GET['nom'];
	// PRID
	$PRID = $_GET['nom'];
	//date
	$array_debut = date_parse($_GET['selYear']."-".$_GET['selMonth']."-".$_GET['selDay']." ".$_GET['bselHour'].":".$_GET['bselMin'].":00");
	$array_fin = date_parse($_GET['eselYear']."-".$_GET['eselMonth']."-".$_GET['eselDay']." ".$_GET['eselHour'].":".$_GET['eselMin'].":00");
		// mise en mémoire session de la date
		$_SESSION['date_abs'] = $array_debut;
	//categorie
	$categorie = $_GET['categorie'];
	//lieux
	if(!isset($_GET['lieux']))
		$lid = 0;
	else
	{
		if(strlen($_GET['lieux'])>0){
			$lidquery = "SELECT LID from lieux WHERE nom_lieu = '" . $_GET['lieux'] . "'";
			$res = mysql_query($lidquery, $ma_base);
			if(mysql_num_rows($res) == 0)
			{
				$myquery = "INSERT INTO lieux (LID, nom_lieu) VALUES('','".$_GET['lieux']."')";
				mysql_query($myquery, $ma_base);
				$lidquery = "SELECT LID from lieux WHERE nom_lieu = '" . mysql_real_escape_string($_GET['lieux']) . "'";
				$res = mysql_query($lidquery, $ma_base);
			}
			$lid = mysql_result($res,0,"LID");
		}
		else {
			$lid = 0;
		}
	}
	//ordonateur
	if(!isset($_GET['ordonateur']))
		$ordonateur = '';
	else
		$ordonateur = $_GET['ordonateur'];
	//examen
	if(!isset($_GET['examen']))
		$examen='';
	if ($_GET['categorie'] == "Examen" && ($_GET['examen']) == '')
		$examen = "Non defini";
	else
		$examen = $_GET['examen'];
	//detail
	if(!isset($_GET['details']))
		$details = '';
	else
		$details = $_GET['details'];
	//pré saisie
	if (isset($_GET['pre-saisie'])) $presaisie = 1;	else $presaisie = 0;
	// gestionnaire de la convocation
	if (!isset($_GET['gest']))
		$gest = $_SESSION['username'];
	else
		$gest = $_GET['gest'];
	
	//###################################################################################
	if($_GET['action'] == 1) // INSERT
		{
		$date_saisie = date("Y-m-d H:i:s");
		$query = "INSERT INTO absences(date_saisie,
			ABID,
			PRID,
			date_debut,
			date_fin,
			categorie,
			examen,
			details,
			ordonateur,
			LID,
			user,
			afficher,
			preset,
			gest) 
			VALUES('".$date_saisie."',"
			."'',"
			.$_GET['nom'].",'"
			.$_GET['selYear']."-".$_GET['selMonth']."-".$_GET['selDay']." ".$_GET['bselHour'].":".$_GET['bselMin'].":00"."','"
			.$_GET['eselYear']."-".$_GET['eselMonth']."-".$_GET['eselDay']." ".$_GET['eselHour'].":".$_GET['eselMin'].":00"."','"
			.$_GET['categorie']."','"
			.mysql_real_escape_string($examen)."','"
			.mysql_real_escape_string($details)."','"
			.mysql_real_escape_string($ordonateur)."','"
			.$lid."','".$_SESSION['username']."','0',"
			.$presaisie
			.",'".$gest."')";
			//echo "<BR>".$query;        //DEBUG
			mysql_query($query, $ma_base) or die();
			$abid = mysql_insert_id(); // récupère l'ABID de la dernière insertion
			echo "<input type=\"hidden\" name=\"abid\" value=". $abid.">";
			$startDate = "'".$_GET['selYear']."-".$_GET['selMonth']."-".$_GET['selDay']." ".$_GET['bselHour'].":".$_GET['bselMin'].":00"."'";
			$endDate = "'".$_GET['eselYear']."-".$_GET['eselMonth']."-".$_GET['eselDay']." ".$_GET['eselHour'].":".$_GET['eselMin'].":00"."'";
			echo "<!-- INSERT -->";
			// echo $abid; //DEBUG
		}

	//###################################################################################
	if($_GET['action'] == 2 || $_GET['action'] == 5) // UPDATE absence(2) ou UPDATE absence et conflit(5)
		{
		$query = "UPDATE absences SET
			date_saisie='".date("Y-m-d H:i:s")."',
			PRID=".$_GET['nom'].",
			date_debut='".$_GET['selYear']."-".$_GET['selMonth']."-".$_GET['selDay']." ".$_GET['bselHour'].":".$_GET['bselMin'].":00"."',
			date_fin='".$_GET['eselYear']."-".$_GET['eselMonth']."-".$_GET['eselDay']." ".$_GET['eselHour'].":".$_GET['eselMin'].":00"."',
			categorie='".$_GET['categorie']."',
			examen='".mysql_real_escape_string($examen)."',
			details='".mysql_real_escape_string($details)."',
			ordonateur='".mysql_real_escape_string($ordonateur)."',
			print=0,
			LID=".$lid.",
			preset=".$presaisie.",
			user='".$_SESSION['username']."',
			gest='".$gest."' WHERE ABID=".$_GET['abid'];
			//echo "<BR>".$query;        //DEBUG
			mysql_query($query, $ma_base) or die();
			$abid = $_GET['abid'];
			echo "<input type=\"hidden\" name=\"abid\" value=". $abid.">";
			$startDate = "'".$_GET['selYear']."-".$_GET['selMonth']."-".$_GET['selDay']." ".$_GET['bselHour'].":".$_GET['bselMin'].":00"."'";
			$endDate = "'".$_GET['eselYear']."-".$_GET['eselMonth']."-".$_GET['eselDay']." ".$_GET['eselHour'].":".$_GET['eselMin'].":00"."'";
			echo "<!-- UPDATE -->";
		}

	//###################################################################################
	if ($_GET['action'] == 3) // EDIT
		{    
		// definition des valeurs du formulaire depuis les données de la base
		$query = "SELECT * from absences where ABID=" . $_GET['abid'];
		$abs_data = mysql_query($query, $ma_base);
		// nom
		$nom = mysql_result($abs_data,0,'PRID');
		// PRID
		$PRID = mysql_result($abs_data,0,'PRID');
		//date
		$array_debut = date_parse(mysql_result($abs_data,0,'date_debut'));
		$array_fin = date_parse(mysql_result($abs_data,0,'date_fin'));
		// categorie
		$categorie = mysql_result($abs_data,0,'categorie');
		// lieux
		$lid = mysql_result($abs_data,0,'LID');
		//ordonateur
		$ordonateur = mysql_result($abs_data,0,'ordonateur');
		//examen
		$examen = mysql_result($abs_data,0,'examen');
		//detail
		$details = mysql_result($abs_data,0,'details');
		//pré saisie 
		if (mysql_result($abs_data,0,'preset') == 1) $presaisie = 1; else $presaisie = 0;
		// gestionnaire de la convocation
		$gest = mysql_result($abs_data,0,'gest');
		
		$action=2;
		$abid = $_GET['abid'];
		echo "<input type=\"hidden\" name=\"abid\" value=". $abid.">";
		$startDate = "'".mysql_result($abs_data,0,'date_debut')."'";
		$endDate = "'".mysql_result($abs_data,0,'date_fin')."'";
		echo "<!-- EDIT -->";
		$edit_flag = true;
		}
	else $edit_flag = false;	
	
	
	//###################################################################################
	if($_GET['action'] == 5) // MISE A JOUR DES CONFLITS
		{
		foreach($_GET['cfid'] as $cfid)
			{
			echo $cfid;
			echo " - ";
			echo $status = $_GET['status'.$cfid];
			echo " - ";
			echo $comment = mysql_real_escape_string($_GET['comment'.$cfid]);
			echo "<br>";
			echo $update_conflit_query = "UPDATE conflits SET comment='".$comment."' , status=".$status." WHERE CFID=".$cfid;
			echo "<br>";
			mysql_query($update_conflit_query, $ma_base) or die();
			}	
		header('Location: absences.php'); // retour à la page de saisie des absences
		}
		
	//###################################################################################
	// RECHERCHE DE DOUBLONS en cas de mise à jour ou de nouvelle saisie
	//###################################################################################

	$conflit_query = "SELECT * FROM absences,personnel,lieux WHERE absences.PRID = personnel.PRID AND absences.LID = lieux.LID AND absences.PRID = ".$PRID." AND ( 
	(absences.date_debut >= ".$startDate." AND absences.date_debut <= ".$endDate.")
	OR (absences.date_fin >= ".$startDate." AND absences.date_fin <= ".$endDate.")
	OR (".$startDate." >= absences.date_debut AND ".$startDate." <= absences.date_fin)
	OR ( ".$endDate." >= absences.date_debut AND ".$endDate." <= absences.date_fin )
	) AND absences.ABID != ".$abid; 
	//echo $conflit_query; // DEBUG
	$conflits = mysql_query($conflit_query, $ma_base) or die();
	$nb_conflits = mysql_num_rows($conflits);  // nombre d'absneces en conflit

	
	if ($nb_conflits >= 1) // si un conflit détecté
	{
		$nb_conflits_non_resolu = 0;
		echo "\n<fieldset id=\"fs_conflits\"><legend>conflit détecté</legend>";
		echo "<table>";
		for ($i=0;$i<$nb_conflits;$i++)  // pour chaque absence en conflit, recherhce l'enregistrement de conflit
			{
			echo "<tr><td>";
			$ABID1 = $abid;
			$ABID2 = mysql_result($conflits,$i,'ABID');
			$conflit_exist = mysql_query("SELECT * FROM conflits WHERE ((ABID1=".$ABID1." AND ABID2=".$ABID2.") OR (ABID1=".$ABID2." AND ABID2=".$ABID1."))",$ma_base);
			if (mysql_num_rows($conflit_exist) == 0) // pas encore d'enregistrment du conflit
				{
				$insert_conflit_query = "INSERT INTO conflits (ABID1,ABID2,comment,status) VALUES (".$ABID1.",".$ABID2.",'',0)";
				$insert_conflit = mysql_query($insert_conflit_query, $ma_base) or die();
				$status = 0;
				$comment = "";
				$CFID = mysql_insert_id(); // récupère le CFID de la dernière insertion
				$abid_en_conflit = $ABID2; 
				}
			else // enregistrement existant
				{
				$CFID = mysql_result($conflit_exist,0,'CFID');
				$status = mysql_result($conflit_exist,0,'status');
				$comment = mysql_result($conflit_exist,0,'comment');
				if (mysql_result($conflit_exist,0,'ABID1')==$abid) $abid_en_conflit = mysql_result($conflit_exist,0,'ABID2'); else $abid_en_conflit = mysql_result($conflit_exist,0,'ABID1');
				}
			
			
			
			echo "conflit n° ".$CFID." - abscence:".$abid_en_conflit." en conflit avec la saissie courante:".$abid; //DEBUG
					
			echo "\n<input type=\"hidden\" name=\"cfid[]\" value=".$CFID.">";
			echo "<table><tr>";
			echo "\n<td valign=\"center\">explication</td>";
			echo "\n<td valign=\"center\"><textarea rows='3' cols='80' name='comment".$CFID."' 
				onkeypress=\"javascript:this.value=this.value.substr(0,200);\"
				onchange=\"javascript:this.value=this.value.substr(0,200);\"
				>".$comment."</textarea></td>";
			echo "\n<td valign=\"center\" align=\"center\">Cocher la case<br> pour marquer le conflit comme<br>résolu</td>";
			echo "\n<td valign=\"center\">";
			
			// basculement de la case statut conflit et mise à jour formulaire
			echo "\n<input id=\"status".$CFID."\" type=\"hidden\" name=\"status".$CFID."\" value=".$status.">";
			if ($status == 0) $on_off="off"; else $on_off = "on";
			echo "<img id=\"status_check_".$CFID."\" border=0 src=\"ico/check_".$on_off.".gif\" type=image onclick=\"bascule_status(".$CFID.");\">";
			?>
			<script type="text/javascript">
			function bascule_status(CFID)
			{
			status_checkbox_id = "status"+CFID;
			mem_status = document.getElementById(status_checkbox_id).value;
			if(mem_status == 0)
				{
				document.getElementById("status_check_"+CFID).src = "ico/check_on.gif";
				document.getElementById(status_checkbox_id).value = 1;	
				}
			else
				{
				document.getElementById("status_check_"+CFID).src = "ico/check_off.gif";
				document.getElementById(status_checkbox_id).value = 0;	
				}
			}
			</script>
			<?php
			echo "</td>";
			echo "<td><img src=\"ico/bouton_valider.png\" onclick=\"document.dates.submit()\" 
				onmouseover=\"surligne('fs_saisie','vert');surligne('fs_conflits','vert');\"	
				onmouseout=\"surligne('fs_saisie','none');surligne('fs_conflits','none');\"></td>";
			echo "</tr></table>";
			
			mini_tableau($conflits,$i);
			echo "</td><td valign='bottom' align='right'>";
			echo "<img src=\"ico/bouton_echanger.png\" onclick=\"window.location.replace('absences_new.php?action=3&abid=".$abid_en_conflit."');\" 
			onmouseover=\"surligne('fs_saisie','bleu');surligne('fs_conflits','bleu');\"
			onmouseout=\"surligne('fs_saisie','none');surligne('fs_conflits','none');\"><br>";
			echo "<img src=\"ico/bouton_supprimer.png\" onclick=\"window.open('abs_delete.php?abid=".$abid_en_conflit."','launch','menubar=no, status=no, scrollbars=no, width=400, height=200');
			window.location.replace('absences.php');\"			 
			onmouseover=\"surligne('fs_saisie','vert');surligne('fs_conflits','rouge');\"
			onmouseout=\"surligne('fs_saisie','none');surligne('fs_conflits','none');\">";
			echo "</td></tr>";
			}
		echo "</table>";	
		$action = 5;
		echo "\n</fieldset>";
	}
	else
	{
	if (!$edit_flag) header('Location: absences.php'); // retour à la page de saisie des absences après un insert ou update sans conflit
	}
}
//###################################################################################
// appel de la page $_GET['action'] = 4-->COPIE
//###################################################################################

else if ($_GET['action'] == 4)
{    
	// definition des valeurs du formulaire depuis les données de la base
	$query = "SELECT * from absences where ABID=" . $_GET['abid'];
	$abs_data = mysql_query($query, $ma_base);
	// nom
	$nom = mysql_result($abs_data,0,'PRID');
	//date
	$array_debut = date_parse(mysql_result($abs_data,0,'date_debut'));
	$array_fin = date_parse(mysql_result($abs_data,0,'date_fin'));
	// categorie
	$categorie = mysql_result($abs_data,0,'categorie');
	// lieux
	$lid = mysql_result($abs_data,0,'LID');
	//ordonateur
	$ordonateur = mysql_result($abs_data,0,'ordonateur');
	//examen
	$examen = mysql_result($abs_data,0,'examen');
	//detail
	$details = mysql_result($abs_data,0,'details');
	//pré saisie 
	if (mysql_result($abs_data,0,'preset') == 1) $presaisie = 1; else $presaisie = 0;
	// gestionnaire de la convocation
	$gest = mysql_result($abs_data,0,'gest');
    
	$action=1;
    $abid = $_GET['abid'];
	echo "<input type=\"hidden\" name=\"abid\" value=". $abid.">";
}

//###################################################################################
// dessin du formulaire
//###################################################################################
	
?>
		<fieldset id="fs_saisie"><legend>Saisie courante</legend>
        <fieldset id="fs_identite"><legend>Identite</legend>
            Nom 
            <select id="nom" name="nom"><?php option_liste_prof();?></select>
        </fieldset>
        <fieldset id="fs_date"><legend>Date</legend>
		<table>
			<tr>
				<td valign="CENTER" align="CENTER" rowspan=3>
                    <div id="cal1Container"></div>
				</td>
				<td valign="CENTER" align="CENTER">
					Debut
                    <?php formDate('sel', 0) ?>
                    Fin
                    <?php formDate('esel', 0) ?>
				</td>
			</tr>
			<tr>
				<td valign="CENTER" align="CENTER">
				    Debut
                    <select name="bselHour" id="bselHour">
                        <option value="8" selected>8h</option>
                        <option value="9">9h</option>
                        <option value="10">10h</option>
                        <option value="11">11h</option>
                        <option value="12">12h</option>
                        <option value="13">13h</option>
                        <option value="14">14h</option>
                        <option value="15">15h</option>
                        <option value="16">16h</option>
                        <option value="17">17h</option>
                        <option value="18">18h</option>
                    </select> 
                    <select name="bselMin" id="bselMin">
                        <option value="0" selected>00</option>
                        <option value="15">15</option>
                        <option value="30">30</option>
                        <option value="45">45</option>
                    </select> 
                    Fin
                    <select name="eselHour" id="eselHour">
                        <option value="8">8h</option>
                        <option value="9">9h</option>
                        <option value="10">10h</option>
                        <option value="11">11h</option>
                        <option value="12">12h</option>
                        <option value="13">13h</option>
                        <option value="14">14h</option>
                        <option value="15">15h</option>
                        <option value="16">16h</option>
                        <option value="17">17h</option>
                        <option value="18" selected>18h</option>
                    </select> 
                    <select name="eselMin" id="eselMin">
                        <option value="0" selected>00</option>
                        <option value="15">15</option>
                        <option value="30">30</option>
                        <option value="45">45</option>
                    </select> 
				</td>
			</tr>
			<tr>
				<td valign="CENTER" align="CENTER">
                    <button type="button" id="button_day">Journee</button>
                    <button type="button" id="button_morning">Matin</button>
                    <button type="button" id="button_afternoon">Apres-Midi</button>
                    <div id="demo_bg" title="Range slider">
                        <span id="demo_highlight"></span>
                        <div id="demo_min_thumb"><img src="yui/assets/l-thumb-round.gif"></div>
                        <div id="demo_max_thumb"><img src="yui/assets/r-thumb-round.gif"></div>
                    </div>
                </td>
			</tr>
		</table>
		</fieldset>	
		<table><tr><td rowspan="3">
		<fieldset id="fs_details"><legend>Details</legend>
			<table>
			<tr>
				<td valign="TOP" rowspan="2">Categorie</td>
				<td valign="TOP" rowspan="2">
					<select id="categorie" onchange='adaptForm()' name="categorie">
					<?php option_liste_categorie();?>
					</select>
				</td>
			</tr>
			</table>
			<table>
				<tr>
					<td id='head_examen'>examen</td>
					<td id='head_details'>Details:</td>
				</tr>
				<tr>
					<td id='form_examen'><input type='hidden' name='examen' id='f_examen' value='' size='40'></td>
					<td id='form_details'><input type='text' name='details' id='details' size='40'></td>
				</tr>
			</table>
			<table>
				<tr>
					<td id='head_lieux'>Lieux</td>
					<td id='head_ordonateur'>Ordonateur</td>
				</tr>
				<tr>		
					<td id='form_lieux'><input type='text' name='lieux' id='lieux' size='40'></td>
					<td id='form_ordonateur'><input type='text' name='ordonateur' id='ordonateur' size='40'></td>
				<tr>
			</table>
			
		</fieldset>
		</td><td valign="top">
		<fieldset id="fs_pré-saisie"><legend>pré-saisie en attente de la convocation officielle</legend>
			<input type="checkbox" id="pre-saisie" name="pre-saisie" value="true">
		</fieldset>
		<fieldset id="fs_gest"><legend>gestionnaire de la convocatio / lieu de retrait</legend>
			<select id="gest" onchange='adaptForm()' name="gest">
					<?php option_liste_gest();?>
			</select>
		</fieldset>
		</td></tr>
		<tr><td valign="bottom" align="right">
		<?php
		if ($nb_conflits >= 1)
			echo "<img src=\"ico/bouton_supprimer.png\" onclick=\"window.open('abs_delete.php?abid=".$abid."','launch','menubar=no, status=no, scrollbars=no, width=400, height=200');
			window.location.replace('absences.php');\" 
			onmouseover=\"surligne('fs_saisie','rouge');surligne('fs_conflits','vert');\"
			onmouseout=\"surligne('fs_saisie','none');surligne('fs_conflits','none');\">";
		?>
		</td></tr>
		<tr><td valign="bottom" align="right">
		<img src="ico/bouton_valider.png" onclick="document.dates.submit()" 
		onmouseover="surligne('fs_saisie','vert');surligne('fs_conflits','vert');"
		onmouseout="surligne('fs_saisie','none');surligne('fs_conflits','none');">
		</td></tr>
		</table>
		
                
	<input type="hidden" name="action" value="<?php echo $action?>">
	<input type="hidden" name="send" value="true">
	</fieldset>
    </form>
</div>

<script type="text/javascript">
		// les fonctions setValue et setSelected font appel à GetElementById et doivent être appelé après la éfinition de id dans le HTML
		function setInput() {
						
			// remplis le formulaire
			//nom
			setSelected('nom', '<?php echo $nom ?>');
			// date
			setSelected('selDay', '<?php echo $array_debut['day'] ?>');
            setSelected('selMonth', '<?php echo (strlen($array_debut['month']) == 1 ? "0" : "") . $array_debut['month'] ?>');
            setSelected('selYear', '<?php echo $array_debut['year'] ?>');
            setSelected('eselDay', '<?php echo $array_fin['day'] ?>');
            setSelected('eselMonth', '<?php echo (strlen($array_debut['month']) == 1 ? "0" : "") . $array_debut['month'] ?>');
            setSelected('eselYear', '<?php echo $array_fin['year'] ?>');
            setSelected('bselHour', '<?php echo $array_debut['hour'] ?>');
            setSelected('bselMin', '<?php echo $array_debut['minute'] ?>');
            setSelected('eselHour', '<?php echo $array_fin['hour'] ?>');
            setSelected('eselMin', '<?php echo $array_fin['minute'] ?>');
			// categorie
			setSelected('categorie', '<?php echo $categorie ?>');
			// lieux
			setValue('lieux', '<?php echo htmlentities(mysql_result(mysql_query("SELECT nom_lieu FROM lieux WHERE LID=".$lid, $ma_base),0,'nom_lieu'), ENT_QUOTES, "UTF-8") ?>');
			// ordonateur
            setValue('ordonateur', '<?php echo htmlentities($ordonateur, ENT_QUOTES, "UTF-8") ?>');
            
            // examen
			setValue('f_examen', '<?php echo htmlentities($examen, ENT_QUOTES, "UTF-8") ?>');
            // details
			setValue('details', '<?php echo htmlentities($details, ENT_QUOTES, "UTF-8") ?>');
            // pré saisie
			document.getElementById('pre-saisie').checked = <?php echo $presaisie == 1 ? "true" : "false" ?>;
			// gestionnaire
			setSelected('gest', '<?php echo $gest ?>');
		}
		function adaptForm ()  {
			mem_lieux = document.getElementById("lieux").value;
			mem_ordonateur = document.getElementById("ordonateur").value;
			mem_examen = document.getElementById("f_examen").value;
			mem_details = document.getElementById("details").value;
			
			if (document.getElementById("categorie").value == "Examen")
				{
				//pas possible de modifier le innerHTML d'une balise <TR> avec IE --> modification individuélle des balise <TD>
				document.getElementById("head_lieux").innerHTML = "Lieux";
				document.getElementById("form_lieux").innerHTML = "<input type='text' size='50' name='lieux' id='lieux' value='"+mem_lieux+"'>";
				document.getElementById("head_ordonateur").innerHTML = "Ordonateur";
				document.getElementById("form_ordonateur").innerHTML = "<input type='text' size='50' name='ordonateur' id='ordonateur' value='"+mem_ordonateur+"'>";
				document.getElementById("head_examen").innerHTML = "Examen";
				document.getElementById("form_examen").innerHTML = "<input type='text' size='50' name='examen'  id='f_examen' value='"+mem_examen+"'>";
				document.getElementById("head_details").innerHTML = "Epreuve";
				document.getElementById("form_details").innerHTML = "<input type='text' size='50' name='details' id='details' value='"+mem_details+"'>";
			}
			else if (document.getElementById("categorie").value == "Stage")
				{
				document.getElementById("head_lieux").innerHTML = "Lieux";
				document.getElementById("form_lieux").innerHTML = "<input type='text' size='50' name='lieux' id='lieux' value='"+mem_lieux+"'>";
				document.getElementById("head_ordonateur").innerHTML = "Ordonateur";
				document.getElementById("form_ordonateur").innerHTML = "<input type='text' size='50' name='ordonateur' id='ordonateur' value='"+mem_ordonateur+"'>";
				document.getElementById("head_examen").innerHTML = "";
				document.getElementById("form_examen").innerHTML = "<input type='hidden' size='50' name='examen'  id='f_examen' value='"+mem_examen+"'>";
				document.getElementById("head_details").innerHTML = "Objet";
				document.getElementById("form_details").innerHTML = "<input type='text' size='50' name='details' id='details' value='"+mem_details+"'>";
			}
			else if (document.getElementById("categorie").value == "Maladie" ||
					document.getElementById("categorie").value == "Sortie/voyage" ||
					document.getElementById("categorie").value == "Enfant malade")
				{
				document.getElementById("head_lieux").innerHTML = "";
				document.getElementById("form_lieux").innerHTML = "<input type='hidden' name='lieux' id='lieux' value='"+mem_lieux+"'>";
				document.getElementById("head_ordonateur").innerHTML = "";
				document.getElementById("form_ordonateur").innerHTML = "<input type='hidden' name='ordonateur' id='ordonateur' value='"+mem_ordonateur+"'>";
				document.getElementById("head_examen").innerHTML = "";
				document.getElementById("form_examen").innerHTML = "<input type='hidden' size='50' name='examen'  id='f_examen' value='"+mem_examen+"'>";
				document.getElementById("head_details").innerHTML = "Details";
				document.getElementById("form_details").innerHTML = "<input type='text' size='50' name='details' id='details' value='"+mem_details+"'>";
			}
			else 
				{
				document.getElementById("head_lieux").innerHTML = "";
				document.getElementById("form_lieux").innerHTML = "<input type='hidden' name='lieux' id='lieux' value='"+mem_lieux+"'>";
				document.getElementById("head_ordonateur").innerHTML = "";
				document.getElementById("form_ordonateur").innerHTML = "<input type='hidden' name='ordonateur' id='ordonateur' value='"+mem_ordonateur+"'>";
				document.getElementById("head_examen").innerHTML = "";
				document.getElementById("form_examen").innerHTML = "<input type='hidden' size='50' name='examen'  id='f_examen' value='"+mem_examen+"'>";
				document.getElementById("head_details").innerHTML = "Details";
				document.getElementById("form_details").innerHTML = "<input type='text' size='50' name='details' id='details' value='"+mem_details+"'>";
			}	
			useBSNns = true;			
			addCompletion('ordonateur','ordonateur');
			addCompletion('details','details');
			addCompletion('f_examen','examen');
			addCompletion('lieux','lieux');
		}
		setInput();
		adaptForm();
        </script>
</body>
</html>
