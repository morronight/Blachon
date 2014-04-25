function adminBandeau_onTexteChange()
{
	var bandeau_texte;
	var bandeau_document;
	var bandeau_article;
	
	bandeau_texte = document.getElementById("bandeau_texte");
	bandeau_document = document.getElementById("bandeau_document");
	bandeau_article = document.getElementById("bandeau_article");
	if (bandeau_texte && bandeau_document && bandeau_article)
	{
		if (bandeau_texte.value != "")
		{
			bandeau_document.value = "";
			bandeau_article.value = "";
		}
	}
}

function adminBandeau_onDocumentChange()
{
	var bandeau_texte;
	var bandeau_document;
	var bandeau_article;
	
	bandeau_texte = document.getElementById("bandeau_texte");
	bandeau_document = document.getElementById("bandeau_document");
	bandeau_article = document.getElementById("bandeau_article");
	if (bandeau_texte && bandeau_document && bandeau_article)
	{
		if (bandeau_document.value != "")
		{
			bandeau_texte.value = "";
			bandeau_article.value = "";
		}
	}
}

function adminBandeau_onArticleChange()
{
	var bandeau_texte;
	var bandeau_document;
	var bandeau_article;
	
	bandeau_texte = document.getElementById("bandeau_texte");
	bandeau_document = document.getElementById("bandeau_document");
	bandeau_article = document.getElementById("bandeau_article");
	if (bandeau_texte && bandeau_document && bandeau_article)
	{
		if (bandeau_article.value != "")
		{
			bandeau_texte.value = "";
			bandeau_document.value = "";
		}
	}
}

function adminBandeau_valider()
{
	var bandeau_texte;
	var bandeau_document;
	var bandeau_article;
	var bandeau_actif;
	var xhr = new XMLHttpRequest();
	var formData = new FormData();
	
	bandeau_texte = document.getElementById("bandeau_texte");
	bandeau_document = document.getElementById("bandeau_document");
	bandeau_article = document.getElementById("bandeau_article");
	bandeau_actif = document.getElementById("bandeau_actif");
	if (bandeau_texte && bandeau_document && bandeau_actif && bandeau_article)
	{
		formData.append("action", "modifier");
		formData.append("texte", bandeau_texte.value);
		formData.append("document", bandeau_document.value);
		formData.append("article", bandeau_article.value);
		formData.append("actif", bandeau_actif.value);		
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
							if (contenu)
								contenu.innerHTML = xhr.responseText;
							if(bandeau_article.value != '') 
								bandeau_texte.value = '';
							alert("Enregistrement effectué");
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
		xhr.open("POST", "/adminBandeau.php", true);
		xhr.setRequestHeader("Cache-Control", "no-cache");
		xhr.send(formData);
	}
}