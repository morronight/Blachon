function adminArticles_action(a,article)
{
	var xhr = new XMLHttpRequest();
	var categorie;
	var	categories = new Array();
	var categoriesNodes;
	var query = '';
	var datepublication = document.getElementById("plannif_"+article).value;
	if(datepublication)
		a = a+"&publication="+datepublication;
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
	xhr.addEventListener
	(
		'readystatechange'
		, function()
		{
			if (xhr.readyState == 4)
			{
				switch (xhr.status)
				{
					default:
						window.location = '/administration/articles' + query;
						break;
				}
			}		
		}
		, false
	);
	xhr.open("GET", a, true);
	xhr.setRequestHeader("Cache-Control", "no-cache");
	xhr.send();
	return false;
}

function adminArticles_update(obj)
{
	var liste = document.getElementById("liste");
	var	archives = document.getElementById("archives");
	var	brouillons = document.getElementById("brouillons");
	var filtre = document.getElementById("seach");
	var url = '/administration/searchArticles.php?';
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

function adminArticles_selectCategories(val)
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
	adminArticles_update(null);
}

function adminArticles_nouvelArticle(a)
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