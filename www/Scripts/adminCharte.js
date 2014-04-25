function adminCharte_ajouterCss(charteid)
{
	var li = document.getElementById("newCss_"+charteid);
	li.style.display = "block";
}

function adminCharte_changerOrdreCss(idCharte, css, ordre)
{
	var xhr = new XMLHttpRequest();
	var	query = '';
	var message = '';
	var done = true;
	
	query += 'action=changerordrecss&charte=' + parseInt(idCharte) + '&css=' + css + '&ordre=' + ordre;
	if (done)
	{
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
								alert("Changement d\'ordre effectué");
								window.location = "/administration/chartes";
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
			xhr.open("POST", '/administration/adminCharte.php', true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(query);
		}
	}
	else
		alert(message);
	return false;
}

function adminCharte_validerCss(idCharte, ordre)
{
	var xhr = new XMLHttpRequest();
	var	query = '';
	var message = '';
	var done = true;
	
	var css = document.getElementById("newCssNomFichier_" + idCharte);
	var user_agent = document.getElementById("user_agent_" + idCharte);
	var fichier = css[css.selectedIndex].value;
	var UAValue = user_agent[user_agent.selectedIndex].value;
	query += 'action=ajoutercss&charte=' + parseInt(idCharte) + '&css=' + fichier + '&ordre=' + ordre + '&user_agent=' + UAValue;
	if (done)
	{
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
								alert("Ajout effectué");
								window.location = "/administration/chartes";
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
			xhr.open("POST", '/administration/adminCharte.php', true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(query);
		}
	}
	else
		alert(message);
	return false;
}

function adminCharte_dupliquerCharte(id)
{
		var xhr = new XMLHttpRequest();
	var	query = '';
	var message = '';
	var done = true;
	var nom = document.getElementById("nomNouvelleCharte_" + id).value;
	query += 'action=dupliquercharte&charte=' + parseInt(id) + '&nomcharte=' + nom;
	if (done)
	{
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
								alert("Ajout effectué");
								window.location = "/administration/chartes";
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
			xhr.open("POST", '/administration/adminCharte.php', true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(query);
		}
	}
	else
		alert(message);
	return false;
}

function adminCharte_supprimerCss(idCharte,css)
{
	var xhr = new XMLHttpRequest();
	var	query = '';
	var message = '';
	var done = true;

	query += 'action=supprimercss&charte=' + parseInt(idCharte) + '&css=' + css;
	if (done)
	{
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
								window.location = "/administration/chartes";
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
			xhr.open("POST", '/administration/adminCharte.php', true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(query);
		}
	}
	else
		alert(message);
	return false;
}

function adminCharte_supprimerCharte(idCharte)
{
	var xhr = new XMLHttpRequest();
	var	query = '';
	var message = '';
	var done = true;

	query += 'action=supprimercharte&charte=' + parseInt(idCharte);
	if (done)
	{
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
								window.location = "/administration/chartes";
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
			xhr.open("POST", '/administration/adminCharte.php', true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(query);
		}
	}
	else
		alert(message);
	return false;
}
