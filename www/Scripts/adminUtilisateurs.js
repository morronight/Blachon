function adminUtilisateurs_ajouterUtilisateur()
{
	window.location = "/administration/utilisateur";
	return false;
}

function adminUtilisateurs_modifierUtilisateur(id)
{
	if (parseInt(id) > 0)
		window.location = "/administration/utilisateur?utilisateur=" + parseInt(id);
	return false;
}

function adminUtilisateurs_supprimerUtilisateur(id)
{
	var xhr = new XMLHttpRequest();
	var	query = '';
	var message = '';
	var done = true;

	query += 'action=supprimer&utilisateur=' + parseInt(id);
	if (done)
	{
		//if (confirm(query))
		if (xhr)
		{
			xhr.addEventListener
			(
				'readystatechange'
				, function()
				{
					if (xhr.readyState == 4)
					{
						switch (xhr.status)
						{
							case 200:
								alert("Suppression effectué");
								window.location = "/administration/utilisateurs";
								break;
							case 500:
								if (xhr.responseText.length > 0)
									alert(xhr.responseText);
								else
									alert('Impossible d\'enregistrer les modifications.');
								break;
							default:
								alert("Erreur lors de l'envoi (" + xhr.status + ")");
								break;
						}
					}		
				}
				, false
			);
			xhr.open("POST", '/administration/adminUtilisateur.php', true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(query);
		}
	}
	else
		alert(message);
	return false;
}

function adminUtilisateurs_resetMotDePasse(id)
{
	var xhr = new XMLHttpRequest();
	var	query = '';
	var message = '';
	var done = true;

	query += 'action=resetMotDePasse&utilisateur=' + parseInt(id);
	if (done)
	{
		//if (confirm(query))
		if (xhr)
		{
			xhr.addEventListener
			(
				'readystatechange'
				, function()
				{
					if (xhr.readyState == 4)
					{
						switch (xhr.status)
						{
							case 200:
								alert("Demande de nouveau mot de passe envoyée.");
								break;
							case 500:
								if (xhr.responseText.length > 0)
									alert(xhr.responseText);
								else
									alert('Impossible d\'enregistrer les modifications.');
								break;
							default:
								alert("Erreur lors de l'envoi (" + xhr.status + ")");
								break;
						}
					}		
				}
				, false
			);
			xhr.open("POST", '/administration/adminUtilisateur.php', true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(query);
		}
	}
	else
		alert(message);
	return false;
}
