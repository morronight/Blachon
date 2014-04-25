function identification()
{
	var xhr = new XMLHttpRequest();
	var	key = document.getElementById('key');
	var	message = document.querySelector('.message');
	var	password = document.getElementById('password');
	var	cipher = document.getElementById('cipher');
	var identifiant = document.getElementById('identifiant');
	var query;
	var pwd;
	var formulaire = document.getElementById('identification');
	
	if (cipher && key && password && identifiant)
	{
		pwd = SHA256(identifiant.value + password.value);
		cipher.value = SHA256(key.value + pwd);
		password.value = '';
		if (xhr && (typeof FormData !== 'undefined') && formulaire)
		{
			if (message)
				message.innerHTML = "Identification en cours...";
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
								if (message)
									message.innerHTML = xhr.responseText;
								window.location = '/administration';
								return false;
								break;
							case 401:
							case 403:
								if (message)
									message.innerHTML = xhr.responseText;
								return false;
								break;
							case 500:
								if (message)
									message.innerHTML = "Erreur lors du chargement de la page.";
								else
									alert("Erreur lors du chargement de la page.");
								break;
							default:
								if (message)
									message.innerHTML = "Erreur lors de l'envoi (" + xhr.status + ")";
								else
									alert("Erreur lors de l'envoi (" + xhr.status + ")");
								break;
						}
					}		
				}
				, false
			);
			if (xhr.upload)
			{
				xhr.upload.addEventListener
				(
					"error",
					function (ev)
					{
						if (message)
							message.innerHTML = ev;
						else
							alert(ev);
					},
					false
				);
			}
			xhr.open("POST", "/identification.php", true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			query = new FormData(formulaire);
			query.append("methode", "ajax");
			xhr.send(query);
			return false;
		}
		return true;
	}
	return false;
}

function changeMotDePasse()
{
	var xhr = new XMLHttpRequest();
	var	message = document.querySelector('.message');
	var	password = document.getElementById('motdepasse');
	var	password2 = document.getElementById('motdepasse2');
	var mail = document.getElementById('mail');
	var action = document.getElementById('action');
	var query;
	var cipher;
	var formulaire = document.getElementById('utilisateurForm');
	
	if (password && password2)
	{
		if (password.value != password2.value)
		{
			if (message)
				message.innerHTML = "Les 2 mots de passes doivent être identiques";
			else
				alert("Les 2 mots de passes doivent être identiques");			
			return false;
		}
	}
	if (!mail)
	{
		if (message)
			message.innerHTML = "Action non autorisée.";
		else
			alert("Action non autorisée.");
		return false;
	}
	if (password && action && mail)
	{
		cipher = SHA256(mail.value + password.value);
		if (xhr && (typeof FormData !== 'undefined') && formulaire)
		{
			password2.value = '';
			password.value = '';
			if (message)
				message.innerHTML = "Changement du mot de passe en cours...";
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
								if (message)
									message.innerHTML = xhr.responseText;
								window.location = '/administration';
								return false;
								break;
							case 500:
								if (message)
									message.innerHTML = xhr.responseText;
								else
									alert(xhr.responseText);
								break;
							default:
								if (message)
									message.innerHTML = "Erreur lors de l'envoi (" + xhr.status + ")";
								else
									alert("Erreur lors de l'envoi (" + xhr.status + ")");
								break;
						}
					}		
				}
				, false
			);
			if (xhr.upload)
			{
				xhr.upload.addEventListener
				(
					"error",
					function (ev)
					{
						if (message)
							message.innerHTML = ev;
						else
							alert(ev);
					},
					false
				);
			}
			xhr.open("POST", "/administration/adminUtilisateur.php", true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			query = new FormData(formulaire);
			query.append("cipher", cipher);
			query.append("methode", "ajax");
			xhr.send(query);
			return false;
		}
		return true;
	}
	return false;
}