function adminImage_valider()
{
	var	id;
	var	legende;
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
	var nbFile = 0;
	var i;
	var msg;

	fichier = document.getElementById('ajoutImageBrowse');
	progress = document.getElementById("imageProgress");
	contenu = document.getElementById('contenu');
	id = document.getElementById('image_id');
	msg = document.getElementById('message');
	if (id)
	{
		formData.append("image", parseInt(id.value));
		formData.append("action", "modifier");
	}
	else
		formData.append("action", "creer");
	legende = document.getElementById('image_legende');
	masque = document.getElementById('image_masque');
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
	if (legende && (legende.value != ""))
		formData.append("legende", legende.value);
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
	if (fichier && (fichier.files.length > 0))	
	{
		for (i = 0; i < fichier.files.length; i++)
		{
			if ((fichier.files[i].type == "image/png") || (fichier.files[i].type == "image/jpeg") || (fichier.files[i].type == "image/gif"))
				nbFile++;
		}
		if (nbFile == 0)
		{
			message += 'Seuls les images PNG, JPG ou GIF sont autorisées.\n';
			done = false;
		}
		else
		{
			if (nbFile != fichier.files.length)
				message += 'Seuls les images PNG, JPG ou GIF sont autorisées, seuls ceux-ci seront transmis.\n';
		}
	}
	if (done)
	{
		if (message != "")
			done = confirm(message + "Enregistrer l'image quand même ?");
		if (done)
		{
			if (fichier && (fichier.files.length > 0))
			{
				if (progress)
				{
					progress.min = 0;
					progress.value = 0;
					progress.max = 0;
					progress.style.display = 'block';
					xhr.upload.addEventListener("progress", function (ev) { progress.value = ev.loaded; }, false);
				}
				nbFile = 0;
				for (i = 0; i < fichier.files.length; i++)
				{
					file = fichier.files[i];
					if ((file.type == "image/png") || (file.type == "image/jpeg") || (file.type == "image/gif"))
					{
						if (progress)
							progress.max += file.size;
						formData.append("Filedata" + nbFile, file);
						nbFile++;
					}
				}
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
										msg = null;
										contenu.innerHTML = xhr.responseText;
										message = "Enregistrement effectué";
										alert(message);
									}
									else
									{
										alert("Enregistrement effectué");
										window.location = '/administration/image?image=' + parseInt(id.value);
									}
									break;
								case 500:
									if (xhr.responseText.length > 0)
										message = xhr.responseText;
									else
										message = 'Impossible d\'enregistrer les modifications.';
									if (msg)
										msg.innerHTML = message;
									else
										alert(message);
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
						if (msg)
							msg.innerHTML = ev;
						else
							alert(ev);
						if (progress)
							progress.style.display = 'none';
					}
					, false
				);
				xhr.open("POST", '/administration/adminImage.php', true);
				xhr.setRequestHeader("Cache-Control", "no-cache");
				if (msg)
					msg.innerHTML = "Envoi en cours...";
				xhr.send(formData);
			}
		}
	}
	else
		alert(message);
	return false;
}

function adminImage_supprimer()
{
	var	id;
	var xhr = new XMLHttpRequest();
	var	query = '';
	var message = '';
	var done = true;

	id = document.getElementById('image_id');
	query += 'action=supprimer&image=' + parseInt(id.value);
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
								window.location = "/administration/images";
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
			xhr.open("POST", '/administration/adminImage.php', true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(query);
		}
	}
	else
		alert(message);
	return false;
}

function adminImage_selectCategorie(anchor, categoriePrefix, parentIds, parentSIds)
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

function adminImage_action(a)
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

function adminImage_updateImage(id)
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
							window.location = '/administration/adminImage.php?image=' + parseInt(id);
							break;
						default:
							alert("Erreur lors de l'envoi (" + xhr.status + ")");
							break;
					}
				}		
			}
			, false
		);
		xhr.open("GET", '/administration/adminImage.php?action=recharger&image=' + parseInt(id), true);
		xhr.setRequestHeader("Cache-Control", "no-cache");
		xhr.send();
		return false;
	}
	return true;
}

function adminImage_nouvelleImage(a)
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

function adminImage_selectFile()
{
	var photo;
	var preview;
	var fr;
	var re = /\.(?:png|gif|jpe?g)$/i;
	var name;
	var checkbox;
	var	message;
	
	message = document.getElementById('message');
	photo = document.getElementById('ajoutImageBrowse');
	preview = document.getElementById('previewImage');
	if (message)
		message.innerHTML = "Modifications non enregistrées.";
	if (photo)
	{
		if (photo.files && (photo.files.length > 0))
			name = photo.files[0].name;
		else
			name = photo.value;
		if (photo.files && (photo.files.length == 1))
		{
			if (re.test(name))
			{
				if (typeof FileReader !== 'undefined')
				{
					fr = new FileReader();
					fr.file = photo.files[0];
					fr.onload = function(ev)
					{
						var f = ev.target.file;

						if (preview)
							preview.src = ev.target.result;
					};
					fr.readAsDataURL(photo.files[0]);
				}
			}
			else
				alert("Les fichiers acceptés sont les images \".png\", \".jpg\" ou \".gif\"");
		}
		else
		{
			preview.src = "/Images/noImg.png";
			if (photo.files && (photo.files.length > 1) && (message))
				message.innerHTML += "<br>" + photo.files.length + " images a envoyer.";
		}
	}
}
