function adminDocument_valider()
{
	var	id;
	var	nom;
	var	description;
	var	masque;
	var xhr = new XMLHttpRequest();
	var message = '';
	var done = true;
	var	categorie;
	var	categories = new Array();
	var categoriesNodes;
	var	contenu;
	var file;
	var fichier;
	var progress;
	var formData = new FormData();

	fichier = document.getElementById('ajoutDocumentBrowse');
	progress = document.getElementById("documentProgress");
	contenu = document.getElementById('contenu');
	id = document.getElementById('document_id');
	if (id)
	{
		formData.append("document", parseInt(id.value));
		formData.append("action", "modifier");
	}
	else
		formData.append("action", "creer");
	nom = document.getElementById('document_nom');
	description = document.getElementById('document_description');
	masque = document.getElementById('document_masque');
	if (nom.value == '')
	{
		message += 'Vous devez indiquer un nom pour le document.\n';
		done = false;
	}
	formData.append("nom", nom.value.replace(/\s+$/i, '').replace(/^\s+/i, ''));
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
		if (!fichier || (fichier.files.length == 0))
		{
			message += 'Vous devez choisir un fichier.\n';
			done = false;
		}
	}
	if (fichier && (fichier.files.length > 0) && (fichier.files[0].type != "application/pdf"))
	{
		message += 'Seuls les fichiers PDF sont autorisés.\n';
		done = false;
	}
	if (done)
	{
		if (message != "")
			done = confirm(message + "Enregistrer le document quand même ?");
		if (done)
		{
			if (fichier && (fichier.files.length > 0))
			{
				file = fichier.files[0];
				if (progress)
				{
					progress.min = 0;
					progress.value = 0;
					progress.max = file.size;
					progress.style.display = 'block';
					xhr.upload.addEventListener("progress", function (ev) { progress.value = ev.loaded; }, false);
				}
				formData.append("Filedata", file);
			}
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
										window.location = '/administration/document?document=' + parseInt(id.value);
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
				xhr.open("POST", '/administration/adminDocument.php', true);
				xhr.setRequestHeader("Cache-Control", "no-cache");
				xhr.send(formData);
			}
		}
	}
	else
		alert(message);
	return false;
}

function adminDocument_supprimer()
{
	var	id;
	var xhr = new XMLHttpRequest();
	var	query = '';
	var message = '';
	var done = true;

	id = document.getElementById('document_id');
	query += 'action=supprimer&document=' + parseInt(id.value);
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
								window.location = "/administration/documents";
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
			xhr.open("POST", '/administration/adminDocument.php', true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(query);
		}
	}
	else
		alert(message);
	return false;
}

function adminDocument_selectCategorie(anchor, categoriePrefix, parentIds, parentSIds)
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

function adminDocument_action(a)
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

function adminDocument_updateDocument(id)
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
							window.location = 'adminDocument.php?document=' + parseInt(id);
							break;
						default:
							alert("Erreur lors de l'envoi (" + xhr.status + ")");
							break;
					}
				}		
			}
			, false
		);
		xhr.open("GET", '/administration/adminDocument.php?action=recharger&document=' + parseInt(id), true);
		xhr.setRequestHeader("Cache-Control", "no-cache");
		xhr.send();
		return false;
	}
	return true;
}
