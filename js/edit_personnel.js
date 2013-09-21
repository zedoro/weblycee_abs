function edit_personnel(type, t1, t2, t3, t4, j, id) {
    document.getElementById("nom"+j).innerHTML = "<input type='text' value='"+t1+"' id=nom_"+j+">";
    addCompletion('nom_'+j,'nom');
    document.getElementById("prenom"+j).innerHTML = "<input type='text' value='"+t2+"' id=prenom_"+j+">";
    addCompletion('prenom_'+j,'prenom');
    document.getElementById("poste"+j).innerHTML = "<input type='text' value='"+t3+"' id=poste_"+j+">";
    addCompletion('poste_'+j,'poste');
    document.getElementById("discipline"+j).innerHTML = "<input type='text' value='"+t4+"' id=discipline_"+j+">";
    addCompletion('discipline_'+j,'discipline');
    document.getElementById("save"+j).innerHTML = "<input type='button' value='Valider' onClick=\"javascript:window.location=update_personnel(document.getElementById(\'nom_"+j+"\').value, document.getElementById(\'prenom_"+j+"\').value, document.getElementById(\'poste_"+j+"\').value, document.getElementById(\'discipline_"+j+"\').value, "+id+")\";>";
}

function update_personnel(n, p, po, d, id)
{
    return "personnel.php?action=2&nom="+n+"&prenom="+p+"&poste="+po+"&discipline="+d+"&id="+id;
}
