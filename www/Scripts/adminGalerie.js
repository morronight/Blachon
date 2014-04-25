function adminGalerie_valider()
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
	var	image;
	var	images = new Array();
	var imagesNodes;
	var	contenu;
	var progress;
	var formData = new FormData();

	progress = document.getElementById("galerieProgress");
	contenu = document.getElementById('contenu');
	id = document.getElementById('galerie_id');
	if (id)
	{
		formData.append("galerie", parseInt(id.value));
		formData.append("action", "modifier");
	}
	else
		formData.append("action", "creer");
	nom = document.getElementById('galerie_nom');
	description = document.getElementById('galerie_description');
	masque = document.getElementById('galerie_masque');
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
	imagesNodes = document.querySelectorAll('#imagesGalerie img[id]');
	for (i = 0; i < imagesNodes.length; i++)
	{
		image = imagesNodes[i];
		reg = image.id.match(/^image_([0-9]+)$/);
		if (reg.length > 0)
			images.push(reg[1]);
	}
	if (images.length > 0)
		formData.append("images",  images.join(','));
	else
		message += 'Aucune image n\'a été indiquée.\n';
	if (description && (description.value != ""))
		formData.append("description", description.value);
	if (masque)
		formData.append("visible",  masque.checked ? 0 : 1);
	if (!id)
	{
		if (!nom || (nom.value.length == 0))
		{
			message += 'Vous devez choisir un nom.\n';
			done = false;
		}
	}
	if (done)
	{
		formData.append("nom", nom.value);
		if (message != "")
			done = confirm(message + "Enregistrer la galerie quand même ?");
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
										window.location = '/administration/galerie?galerie=' + parseInt(id.value);
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
				xhr.open("POST", '/administration/adminGalerie.php', true);
				xhr.setRequestHeader("Cache-Control", "no-cache");
				xhr.send(formData);
			}
		}
	}
	else
		alert(message);
	return false;
}

function adminGalerie_supprimer()
{
	var	id;
	var xhr = new XMLHttpRequest();
	var	query = '';
	var message = '';
	var done = true;

	id = document.getElementById('galerie_id');
	query += 'action=supprimer&galerie=' + parseInt(id.value);
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
								window.location = "/administration/galeries";
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
			xhr.open("POST", '/administration/adminGalerie.php', true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send(query);
		}
	}
	else
		alert(message);
	return false;
}

function adminGalerie_selectCategorie(anchor, categoriePrefix, parentIds, parentSIds)
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

function adminGalerie_action(a)
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

function adminGalerie_updateGalerie(id)
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
							window.location = '/administration/adminGalerie.php?galerie=' + parseInt(id);
							break;
						default:
							alert("Erreur lors de l'envoi (" + xhr.status + ")");
							break;
					}
				}		
			}
			, false
		);
		xhr.open("GET", '/administration/adminGalerie.php?action=recharger&galerie=' + parseInt(id), true);
		xhr.setRequestHeader("Cache-Control", "no-cache");
		xhr.send();
		return false;
	}
	return true;
}

function adminGalerie_nouvelleGalerie(a)
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

function adminGalerie_selectImages()
{
	var	photoSelector;
	var xhr = new XMLHttpRequest();
	var galerie;
	var url;
	var categorie;
	var	categories = new Array();
	var categoriesNodes;

	photoSelector = document.getElementById('photoSelector');
	galerie = document.getElementById('galerie_id');
	if (photoSelector && xhr)
	{
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
							photoSelector.innerHTML = xhr.responseText;
							photoSelector.style.display = "block";
							break;
						case 500:
							if (galerie)
								window.location = '/administration/galerie?galerie=' + parseInt(galerie.value);
							else
								window.location = '/administration/galerie';
							break;
						default:
							alert("Erreur lors de l'envoi (" + xhr.status + ")");
							break;
					}
				}		
			}
			, false
		);
		photoSelector.style.display = "none";
		url = "/administration/adminGalerie.php?action=searchimages";
		//if (galerie)
			//url += '&galerie=' + parseInt(galerie.value);
		if (categories.length > 0)
			url += '&categories=' + categories.join(',');
		xhr.open("GET", url, true);
		xhr.setRequestHeader("Cache-Control", "no-cache");
		xhr.send();
		return false;
	}
	return true;
}

function adminGalerie_closeSelectionImages()
{
	var	photoSelector;

	photoSelector = document.getElementById('photoSelector');
	if (photoSelector)
		photoSelector.style.display = "none";
}

function adminGalerie_selectPhoto(id, w, h)
{
	var imagesGalerie;
	var img;
	var div;
	var height;
	var width;
	
	img = document.getElementById('image_' + parseInt(id));
	if (img)
		return;
	imagesGalerie = document.getElementById('imagesGalerie');
	if (imagesGalerie)
	{
		width = 200;
		height = 200;
		if ((h > 0) && (w > 0))
		{
			height = parseInt(1. * h * width / w);
			if (height > 200)
			{
				height = 200;
				width = parseInt(1. * w * height / h);
			}
		}
		div = document.createElement('div');
		if (div)
		{
			div.className = "imageGalerie";
			div.innerHTML = '<img id="image_' + parseInt(id) + '" src="/Images/I' + parseInt(id) + '_' + width + '_' + height + '"/><span class="icone delete" title="Supprimer de la galerie" onclick="return adminGalerie_supprimeImageGalerie(' + parseInt(id) + ');"></span>';
			imagesGalerie.appendChild(div);
		}
	}
}

function adminGalerie_supprimeImageGalerie(id)
{
	var img;
	var imagesGalerie;

	img = document.getElementById('image_' + parseInt(id));
	imagesGalerie = document.getElementById('imagesGalerie');
	if (img && imagesGalerie)
		imagesGalerie.removeChild(img.parentNode);
	return false;
}