<?php
    require_once("./menu.php");
    $menu = affiche_menu();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>Lieux</title>
    <link href="absences.css" rel="stylesheet" type="text/css">
    <link href="css/menu.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="js/edit_lieu.js"></script>
    <script type="text/javascript" src="js/add_lieu.js"></script>
</head>
<body>
<?php
    echo $menu;
    include('db_fonction.php');
    $ma_base = connect_db();
    if(ISSET($_GET['action']))
    {
        if ($_GET['action'] == 1)
        {
            $query = "DELETE FROM lieux WHERE LID=" . $_GET['id'];
            mysql_query($query, $ma_base);
        }
        elseif ($_GET['action'] == 2)
        {
            $query = "UPDATE lieux SET nom_lieu='".mysql_real_escape_string($_GET['nom'])."', adresse='".mysql_real_escape_string($_GET['adresse']). "' WHERE LID=".$_GET['lid'];
            mysql_query($query, $ma_base);

        }
        elseif ($_GET['action'] == 3)
        {
            $myquery = "INSERT INTO lieux(LID, nom_lieu, adresse) VALUES('','".mysql_real_escape_string($_GET['nom'])."','".mysql_real_escape_string($_GET['adresse'])."')";
            mysql_query($myquery, $ma_base);

        }
    }
    $query = "SELECT * FROM lieux WHERE interne = 0 AND LID > 0 ORDER BY nom_lieu";
    $liste_lieux = mysql_query($query, $ma_base);
?>
<div id="statut"></div>
<br><br>
<div class='corps'>
    <!--Affichage tableau-->
	<table border=1 width=700px cellpadding=0 cellspacing=0>
        <tr>
            <th width = "70px">&nbsp;</th>
            <th width = "310px">Nom</th>
            <th width = "410px">Adresse</th>
            <th width = "200px"><input type='button' onClick="javascript:add_lieu()" value='Ajouter un lieu'></th>
        </tr>
        <tr id='addLieu'>
        </tr>
        <?php
            for($j=0;$j<mysql_num_rows($liste_lieux);$j++)
            {
                $bgColor = ($j % 2) ? "#CCFFCC" : "#66FFFF";
        ?>
        <tr>
            <?php
                $nom = mysql_result($liste_lieux,$j,"nom_lieu");
                $adresse = mysql_result($liste_lieux,$j,"adresse");
                $lid = mysql_result($liste_lieux,$j,"LID"); 
            ?>
            <td>
            <input border=0 src="ico/supp.gif" type=image onClick="javascript:window.location='lieux.php?action=1&id=<?php echo $lid ?>';" align="middle" > 
            <input border=0 src="ico/edit.gif" type=image onClick="javascript:edit_lieu('text','<?php echo addslashes($nom) ?>','<?php echo addslashes($adresse) ?>', '<?php echo $j?>', <?php echo $lid ?>);" align="middle" > 
            </td>
            <td id="nom<?php echo $j?>" BGCOLOR=<?php echo $bgColor ?>> <?php echo $nom; ?></td>
            <?php 
                if ($adresse == '')
                    echo "<td id=adr$j BGCOLOR=$bgColor>&nbsp;</td>";
                else
                    echo "<td id=adr$j BGCOLOR=$bgColor>$adresse</td>";
            ?>
            <td id="save<?php echo $j?>"></td>
        </tr>
        <?php
            }
        ?>
    </table>
</div>
</body>
</html>

