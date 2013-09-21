function edit_lieu(type, t1, t2, j, lid) {
	//alert(document.getElementById("statut").innerHTML);
	if ((document.getElementById("statut").innerHTML)=="<BR>Validez la modification avant de changer d'onglet<BR>")
	{
	alert("validez la ligne avant de continuer!");
	}
	else
	{
	document.getElementById("statut").innerHTML = "<BR>Validez la modification avant de changer d'onglet<BR>";
    document.getElementById("nom"+j).innerHTML = "<input type='text' size=36 value='"+t1+"' id=in1_"+j+">";
    document.getElementById("adr"+j).innerHTML = "<input type='text' value='"+t2+"' id=in2_"+j+">";
    document.getElementById("save"+j).innerHTML = "<input type='button' value='Valider' onClick=\"javascript:window.location=update_lieu(document.getElementById(\'in1_"+j+"\').value, document.getElementById(\'in2_"+j+"\').value, "+lid+")\";>";
	}
	
}

function update_lieu(n, a, lid)
{
    return "lieux.php?action=2&nom="+n+"&adresse="+a+"&lid="+lid;
}
