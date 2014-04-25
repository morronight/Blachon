function adminVideo_valider()
{
	var	id;
	var	url;
	var	description;
	var	masque;
	var xhr = new XMLHttpRequest();
	var message = '';
	var done = true;
	var	categorie;
	var	categories = new Array();
	var categoriesNodes;
	var	contenu;
	var progress;
	var formData = new FormData();

	progress = document.getElementById("videoProgress");
	contenu = document.getElementById('contenu');
	id = document.getElementById('video_id');
	if (id)
	{
		formData.append("video", parseInt(id.value));
		formData.append("action", "modifier");
	}
	else
		formData.append("action", "creer");
	url = document.getElementById('video_url');
	description = document.getElementById('video_description');
	masque = document.getElementById('video_masque');
	categoriesNodes = document.querySelectorAll('#categories li');
	for (i = 0; i < categoriesNodes.length; i++)
	{
		categorie = categoriesNodes[i];
		if (categorie.className == 'selected')
		{
			reg = categorie.id.match(/^categorie_([0-9]+)$/);
			if (reg.length > 0)
				categories.push(reg[1]);
		}
	}
	if (categories.length > 0)
		formData.append("categories",  categories.join(','));
	else
		message += 'Aucune catégorie n\'a été indiquée.\n';
	if (description && (description.value != ""))
		formData.append("description", description.value);
	if (masque)
		formData.append("visible",  masque.checked ? 0 : 1);
	if (!id)
	{
		if (!url || (url.value.length == 0))
		{
			message += 'Vous devez indiquer l\'adresse d\'une vidéo.\n';
			done = false;
		}
	}
	if (done)
	{
		formData.append("url", url.value);
		if (message != "")
			done = confirm(message + "Enregistrer la vidéo quand même ?");
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
										window.location = '/administration/video?video=' + parseInt(id.value);
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
				xhr.open("POST", '/administration/adminVideo.php', true);
				xhr.setRequestHeader("Cache-Control", "no-cache");
				xhr.send(formData);
			}
		}
	}
	else
		alert(message);
	return false;
}

function adminVideo_supprimer()
{
	var	id;
	var xhr = new XMLHttpRequest();
	var	query = '';
	var message = '';
	var done = true;

	id = document.getElementById('video_id');
	query += 'action=supprimer&video=' + parseInt(id.value);
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
								window.location = "/administration/videos";
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
			xhr.open("POST", '/administration/adminVideo.php', true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(query);
		}
	}
	else
		alert(message);
	return false;
}

function adminVideo_selectCategorie(anchor, categoriePrefix, parentIds, parentSIds)
{
	var	i;
	var parentIds = parentIds.split(',');
	var parentSIds = parentSIds.split(',');
	var	categorie;

	if (anchor.className == "selected")
	{
		anchor.className = "";
		for(i = 0; i < parentSIds.length; i++)
		{
			categorie = document.getElementById(categoriePrefix + '_' + parentSIds[i]);
			if (categorie)
				categorie.className = "";
		}
	}
	else
	{
		anchor.className = "selected";
		for(i = 0; i < parentIds.length; i++)
		{
			categorie = document.getElementById(categoriePrefix + '_' + parentIds[i]);
			if (categorie)
				categorie.className = "selected";
		}
	}
}

function adminVideo_action(a)
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

function adminVideo_nouvelleVideo(a)
{
	var categorie;
	var	categories = new Array();
	var categoriesNodes;
	var query = '';
	
	categoriesNodes = document.querySelectorAll('#categories li');
	for (i = 0; i < categoriesNodes.length; i++)
	{
		categorie = categoriesNodes[i];
		if (categorie.className == 'selected')
		{
			reg = categorie.id.match(/^categorie_([0-9]+)$/);
			if (reg.length > 0)
				categories.push(reg[1]);
		}
	}
	if (categories.length > 0)
		query += '?categories=' + categories.join(',');
	window.location = a.href + query;
	return false;
}
