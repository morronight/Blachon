function adminImages_action(a)
{
	var action = new Request
	({
		url: a.href,
		method: 'get',
		onSuccess: function(responseText)
		{
			window.location = window.location;
		},
		onFailure: function()
		{
			window.location = window.location;
		}
	});
	action.send();
	return false;
}

function adminImages_update(obj)
{
	var liste = document.getElementById("liste");
	var	archives = document.getElementById("archives");
	var	brouillons = document.getElementById("brouillons");
	var categorieIds = "";
	var filtre = document.getElementById("seach");
	var url = '/administration/searchImages.php?';
	var categorie;
	var	categories = new Array();
	var categoriesNodes;
	var xhr = new XMLHttpRequest();

	if (!liste)
		return;
	if (brouillons && brouillons.checked)
		url += "&brouillons=1";
	if (archives && archives.checked)
		url += "&archives=1";
	if (obj && (obj.tagName == 'SPAN'))
	{
		if (obj.className == "selected")
			obj.className = "";
		else
			obj.className = "selected";
	}
	categoriesNodes = document.querySelectorAll('#categories span');
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
		url += '&categories=' + categories.join(',');
	if (filtre && (filtre.value != ""))
		url += "&filtre=" + escape(filtre.value);
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
						liste.innerHTML = xhr.responseText;
						liste.className = 'liste';
						adminImages_initPhotoEditor();
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
	liste.className = 'liste loading';
	xhr.open("GET", url, true);
	xhr.setRequestHeader("Cache-Control", "no-cache");
	xhr.send();
}

function adminImages_selectCategories(val)
{
	var categoriesNodes;
	var categorie;
	
	categoriesNodes = document.querySelectorAll('#categories span');
	for (i = 0; i < categoriesNodes.length; i++)
	{
		categorie = categoriesNodes[i];
		if (val == 1)
			categorie.className = "selected";
		else
			categorie.className = "";
	}
	adminImages_update(null);
}

function adminImages_nouvelleImage(a)
{
	var categorie;
	var	categories = new Array();
	var categoriesNodes;
	var query = '';
	
	categoriesNodes = document.querySelectorAll('#categories span');
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

function adminImages_updatePagePhotoEditor()
{
	var main;
	var liste;
	var overflow;
	var fichePlat;
	var restore = false;
	var paddingTop = 0;

	main = document.getElementById('photoEditor');
	liste = document.getElementById('liste');
	if (main)
	{
		paddingTop = parseInt(window.getComputedStyle(main, null).getPropertyValue("padding-top"));
		if (isNaN(paddingTop))
			paddingTop = 0;
		if (main.scrollHeight == paddingTop)
		{
			overflow = main.style.overflowY;
			main.style.overflowY = "scroll";
			restore = true;
		}
		if (liste)
			main.style.height = (liste.offsetHeight)+ 220 + 'px';
		if (restore)
			main.style.overflowY = overflow;
	}
}

function adminImages_initPhotoEditor()
{
	window.onresize = adminArticle_updatePagePhotoEditor;
	//adminArticle_updatePagePhotoEditor();
}