function adminUtilisateur_valider()
{
	var	id;
	var	mail;
	var	pseudo;
	var xhr = new XMLHttpRequest();
	var message = '';
	var done = true;
	var	contenu;
	var progress;
	var formData = new FormData();

	progress = document.getElementById("utilisateurProgress");
	contenu = document.getElementById('contenu');
	id = document.getElementById('utilisateur_id');
	if (id)
	{
		formData.append("utilisateur", parseInt(id.value));
		formData.append("action", "modifier");
	}
	else
		formData.append("action", "creer");
	mail = document.getElementById('utilisateur_mail');
	pseudo = document.getElementById('utilisateur_pseudo');
	if (pseudo && (pseudo.value != ""))
		formData.append("pseudo", pseudo.value);
	if (!id)
	{
		if (!mail || (mail.value.length == 0))
		{
			message += 'Vous devez indiqué une adresse mail.\n';
			done = false;
		}
	}
	if (done)
	{
		formData.append("mail", mail.value);
		if (message != "")
			done = confirm(message + "Enregistrer l'utilisateur quand même ?");
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
									if (progress)
										progress.style.display = 'none';
									if (contenu)
									{
										contenu.innerHTML = xhr.responseText;
										alert("Enregistrement effectué");
									}
									else
									{
										alert("Enregistrement effectué");
										window.location = '/administration/utilisateur?utilisateur=' + parseInt(id.value);
									}
									break;
								case 500:
									if (xhr.responseText.length > 0)
										alert(xhr.responseText);
									else
										alert('Impossible d\'enregistrer les modifications.');
									if (progress)
										progress.style.display = 'none';
									break;
								default:
									alert("Erreur lors de l'envoi (" + xhr.status + ")");
									if (progress)
										progress.style.display = 'none';
									break;
							}
						}		
					}
					, false
				);
				xhr.upload.addEventListener
				(
					"error"
					, function (ev)
					{
						alert(ev);
						if (progress)
							progress.style.display = 'none';
					}
					, false
				);
				xhr.open("POST", '/administration/adminUtilisateur.php', true);
				xhr.setRequestHeader("Cache-Control", "no-cache");
				xhr.send(formData);
			}
		}
	}
	else
		alert(message);
	return false;
}

function adminUtilisateur_supprimer()
{
	var	id;
	var xhr = new XMLHttpRequest();
	var	query = '';
	var message = '';
	var done = true;

	id = document.getElementById('utilisateur_id');
	query += 'action=supprimer&utilisateur=' + parseInt(id.value);
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

function adminUtilisateur_action(a)
{
	var	contenu;
	var xhr = new XMLHttpRequest();

	contenu = document.getElementById('contenu');
	if (contenu && xhr)
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
							contenu.innerHTML = xhr.responseText;
							break;
						case 500:
							window.location = window.location;
							break;
						default:
							alert("Erreur lors de l'envoi (" + xhr.status + ")");
							break;
					}
				}		
			}
			, false
		);
		xhr.open("GET", a.href, true);
		xhr.setRequestHeader("Cache-Control", "no-cache");
		xhr.send();
		return false;
	}
	return true;
}

function adminUtilisateur_updateUtilisateur(id)
{
	var	contenu;
	var xhr = new XMLHttpRequest();

	contenu = document.getElementById('contenu');
	if (contenu && xhr)
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
							contenu.innerHTML = xhr.responseText;
							break;
						case 500:
							window.location = 'adminUtilisateur.php?utilisateur=' + parseInt(id);
							break;
						default:
							alert("Erreur lors de l'envoi (" + xhr.status + ")");
							break;
					}
				}		
			}
			, false
		);
		xhr.open("GET", '/administration/adminUtilisateur.php?action=recharger&utilisateur=' + parseInt(id), true);
		xhr.setRequestHeader("Cache-Control", "no-cache");
		xhr.send();
		return false;
	}
	return true;
}