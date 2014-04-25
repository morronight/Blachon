var	adminArticle_closeImagePreviewTimer = null;

function adminArticle_ajouterUnParagraphe(index)
{
	var	htmlContent = '';
	var	paragraphes;
	var	paragraphe;
	var	nouveauParagraphe;
	var	nextParagraphe = null;
	var	i;
	var	iMax = 0;
	var	image;
	var titreParagraphe;
	var texteParagraphe;
	
	paragraphes = document.getElementById('paragraphes');
	if (paragraphes)
	{
		for (i = index; paragraphe = document.getElementById('paragraphe_' + i); i++)
			iMax = i;
		if (iMax > paragraphes.children.length)
			return false;
		for (i = iMax; i >= index; i--)
		{
			paragraphe = document.getElementById('paragraphe_' + i);
			if (i == index)
				nextParagraphe = paragraphe;
			paragraphe.id = 'paragraphe_' + (i + 1);
			titreParagraphe = document.getElementById('paragraphe_titre_' + i);
			titreParagraphe.id = 'paragraphe_titre_' + (i + 1);
			texteParagraphe = document.getElementById('paragraphe_texte_' + i);
			texteParagraphe.id = 'paragraphe_texte_' + (i + 1);
			image = document.getElementById('paragraphe_image_' + i);
			image.id = 'paragraphe_image_' + (i + 1);
			image = document.getElementById('paragraphe_image_' + i + '_left');
			image.id = 'paragraphe_image_' + (i + 1) + '_left';
			image = document.getElementById('paragraphe_image_' + i + '_center');
			image.id = 'paragraphe_image_' + (i + 1) + '_center';
			image = document.getElementById('paragraphe_image_' + i + '_right');
			image.id = 'paragraphe_image_' + (i + 1) + '_right';
		}
		nouveauParagraphe = document.createElement('section');
		if (nouveauParagraphe)
		{
			htmlContent += '<div class="commandes">';
			htmlContent += '<span class="icone importer tableau" title="Importer un tableau" onclick="return adminArticle_displayImport(this.parentNode.parentNode)"></span>';
			htmlContent += '<span class="icone tableau" title="Ajouter un tableau" onclick="return adminArticle_addTableauParagraphe(this.parentNode.parentNode, 3)"></span>';
			htmlContent += '<span class="icone galerie" title="Ajouter une galerie" onclick="return adminArticle_addIllustrationParagraphe(this.parentNode.parentNode, 2)"></span>';
			htmlContent += '<span class="icone photo" title="Ajouter une image/photo" onclick="return adminArticle_addIllustrationParagraphe(this.parentNode.parentNode, 1)"></span>';
			htmlContent += '<span class="icone lien" title="Ajouter un lien" onclick="return adminArticle_displayLien(this.parentNode.parentNode)"></span>';
			htmlContent += '<span class="icone up" title="Monter le paragraphe" onclick="return adminArticle_editTexteMoveUp(this.parentNode.parentNode)"></span>';
			htmlContent += '<span class="icone down" title="Descendre le paragraphe" onclick="return adminArticle_editTexteMoveDown(this.parentNode.parentNode);"></span>';
			htmlContent += '<span class="icone delete" title="Supprimer le paragraphe" onclick="return adminArticle_supprimerUnParagraphe(this.parentNode.parentNode);"></span>';
			htmlContent += '</div>';
			htmlContent += '<div id="fen_import_'+index+'" class="import" style="display:none">';
			htmlContent += '<form id="importForm" action="" method="post" enctype="multipart/form-data">';
			htmlContent += '<span>Veuillez séléctionner un fichier Excel valide (.XLSX) </span>';
			htmlContent += '<input type="file" name="file" id="FileXLSX_'+index+'" onchange="return adminArticle_validerImportParagraphe(this.parentNode.parentNode);"/></span>';
			htmlContent += '<progress id="importProgress" value="0"></progress>';
			htmlContent += '</form>';
			htmlContent += '<div class="commandes">';
			htmlContent += '<span class="icone close" title="Fermer" onclick="return adminArticle_closeImport(this.parentNode.parentNode);"></span>';
			htmlContent += '</div>';
			htmlContent += '</div>';
			htmlContent += '<div id="ajoutLienParagraphe" class="import" style="display:none">';
			htmlContent += '<div class="commandes">';
			htmlContent += '<span class="icone close" title="Fermer" onclick="return adminArticle_closeLien(this.parentNode.parentNode);"></span>';
			htmlContent += '</div>';
			htmlContent += '<span class="liens"><label>Inserer un lien :</label></span>'
			htmlContent += '<div id="typeLienHead">';
			htmlContent += '<label>Creer un lien vers</label>';
			htmlContent += '<select selected="selected" name="selectTypeLien" onchange="adminArticle_selectType(this.value, this.parentNode.parentNode)"/>';
			htmlContent += '<option value=""></option>';
			htmlContent += '<option value="document">Document</option>';
			htmlContent += '<option value="article">Article</option>';
			htmlContent += '<option value="page">Page</option>';
			htmlContent += '<option value="autre">Autre</option>';
			htmlContent += '</select>';
			htmlContent += '</div>';
			htmlContent += '</div>';
			htmlContent += '<textarea id="paragraphe_titre_' + index + '" title="Titre du paragraphe" class="section" placeholder="Titre du paragraphe" onkeyup="adminArticle_editTexteChanged(this);" onpaste="adminArticle_editTexteChanged(this)" onchange="adminArticle_editTexteChanged(this)"></textarea>';
			htmlContent += '<textarea id="paragraphe_texte_' + index + '" title="Paragraphe" class="texte" placeholder="Paragraphe" onkeyup="adminArticle_editTexteMultiChanged(this);" onpaste="adminArticle_editTexteMultiChanged(this)" onchange="adminArticle_editTexteMultiChanged(this)"></textarea>';
			htmlContent += '<div class="sectionBottom"></div>';
			nouveauParagraphe.innerHTML = htmlContent;
			nouveauParagraphe.id = 'paragraphe_' + index;
			nouveauParagraphe.className = "paragraphe";
			if (nextParagraphe)
				paragraphes.insertBefore(nouveauParagraphe, nextParagraphe);
			else
				paragraphes.appendChild(nouveauParagraphe);
			titreParagraphe = document.getElementById('paragraphe_titre_' + index);
			adminArticle_editTexteChanged(titreParagraphe);
			texteParagraphe = document.getElementById('paragraphe_texte_' + index);
			adminArticle_editTexteMultiChanged(texteParagraphe);
			return true;
		}
	}
	return false;
}

function adminArticle_supprimerUnParagraphe(paragraphe)
{
	var paragraphes;
	var	index = -1;
	var	image;
	var inputs;
	var tableau;
	var table;
	var nouveauTableau;
	var nouvelleTable;
	var next;
	var	i;
	var re = /^paragraphe_([0-9]+)$/;
	var reg;
	
	if (!paragraphe || (paragraphe.deleting == true))
		return false;
	reg = paragraphe.id.match(re);
	if (reg && (reg.length > 0))
		index = parseInt(reg[1]);		
	paragraphes = document.getElementById('paragraphes');
	if (paragraphes && paragraphe && (index >= 0))
	{
		paragraphe.deleting = true;
		paragraphes.removeChild(paragraphe);
		for (i = index + 1; paragraphe = document.getElementById('paragraphe_' + i); i++)
		{
			fenImp = paragraphe.querySelector(".import");
			if(fenImp)
			{
				fenImp.id = 'fen_import_' + (i - 1);
				xlsx = fenImp.querySelector('input[type=file]');
				if(xlsx)
					xlsx.id = 'FileXLSX_' + (i - 1);
			}
			
				
			paragraphe.id = 'paragraphe_' + (i - 1);
			titreParagraphe = document.getElementById('paragraphe_titre_' + i);
			titreParagraphe.id = 'paragraphe_titre_' + (i - 1);
			texteParagraphe = document.getElementById('paragraphe_texte_' + i);
			texteParagraphe.id = 'paragraphe_texte_' + (i - 1);
			image = document.getElementById('paragraphe_image_' + i);
			if (image)
				image.id = 'paragraphe_image_' + (i - 1);
			image = document.getElementById('paragraphe_image_' + i + '_left');
			if (image)
				image.id = 'paragraphe_image_' + (i - 1);
			image = document.getElementById('paragraphe_image_' + i + '_center');
			if (image)
				image.id = 'paragraphe_image_' + (i - 1);
			image = document.getElementById('paragraphe_image_' + i + '_right');
			if (image)
				image.id = 'paragraphe_image_' + (i - 1);
			tableau = paragraphe.querySelector("div.tableau");
			if (tableau)
			{
				table = tableau.querySelector("table");
				tableau.removeChild(table);
				paragraphe.removeChild(tableau);
				nouveauTableau = adminArticle_addTableauParagraphe(paragraphe, 0);
				nouvelleTable = nouveauTableau.querySelector("table");
				nouveauTableau.removeChild(nouvelleTable);
				nouveauTableau.appendChild(table);
				table.id = "paragraphe_tableau_" + (i - 1);
				inputs = table.querySelectorAll('input[type="text"][name]');
				adminArticle_editTexteMoveTableauCellules(inputs, i - 1);
			}
		}
		if ((index == 0) && (i == (index + 1)))
			adminArticle_ajouterUnParagraphe(0);
		return true;
	}
	return false;
}

function adminArticle_editParagrapheHook()
{
	var	i;
	var titre;
	var resume;
	var legende;
	var	titreParagraphe;
	var	texteParagraphe;
	var	legendeParagraphe;
	
	titre = document.getElementById('article_titre');
	if (titre)
		adminArticle_editTexteChanged(titre);
	resume = document.getElementById('article_resume');
	if (resume)
		adminArticle_editTexteMultiChanged(resume);
	legende = document.getElementById('article_legende');
	if (legende)
		adminArticle_editTexteMultiChanged(legende);
	for (i = 0; titreParagraphe = document.getElementById('paragraphe_titre_' + i); i++)
	{
		adminArticle_editTexteChanged(titreParagraphe);
		texteParagraphe = document.getElementById('paragraphe_texte_' + i);
		adminArticle_editTexteMultiChanged(texteParagraphe);
		legendeParagraphe = document.getElementById('paragraphe_legende_' + i);
		if (legendeParagraphe)
			adminArticle_editTexteMultiChanged(legendeParagraphe);
	}
}

function adminArticle_addOrRemoveParagrapheHook(input)
{
	var	i;
	var	paragraphe;
	var	titreParagraphe;
	var	texteParagraphe;
	var	texteParagrapheNext;
	var re = /^paragraphe_(?:texte|titre)_([0-9]+)$/;
	var id = 0;
	var reg;
	
	if (input)
		reg = input.id.match(re);
	if (input && reg && (reg.length > 0))
	{
		id = parseInt(reg[1]);
		titreParagraphe = document.getElementById('paragraphe_titre_' + id);
		texteParagraphe = document.getElementById('paragraphe_texte_' + id);
		texteParagrapheNext = document.getElementById('paragraphe_texte_' + (id + 1));
		if (!texteParagrapheNext && ((titreParagraphe.value != '') || (texteParagraphe.value != '')))
			adminArticle_ajouterUnParagraphe(id + 1);
		if (texteParagrapheNext && (titreParagraphe.value == '') && (texteParagraphe.value == ''))
		{
			paragraphe = document.getElementById('paragraphe_' + id);
			adminArticle_supprimerUnParagraphe(paragraphe);
		}
	}
	else
	{
		for (i = 0; titreParagraphe = document.getElementById('paragraphe_titre_' + i); i++)
		{
			texteParagraphe = document.getElementById('paragraphe_texte_' + i);
			texteParagrapheNext = document.getElementById('paragraphe_texte_' + (i + 1));		
			if (!texteParagrapheNext && ((titreParagraphe.value != '') || (texteParagraphe.value != '')))
				adminArticle_ajouterUnParagraphe(i + 1);
			if (texteParagrapheNext && (titreParagraphe.value == '') && (texteParagraphe.value == ''))
			{
				paragraphe = document.getElementById('paragraphe_' + i);
				adminArticle_supprimerUnParagraphe(paragraphe);
				i--;
			}
		}
	}
}

function adminArticle_valider()
{
	var	id;
	var	titre;
	var	resume;
	var legende;
	var signature;
	var	image;
	var	imageId;
	var galerie;
	var tableau;
	var cellule;
	var cellules;
	var	titreParagraphe;
	var	texteParagraphe;
	var	imageParagraphe;
	var galerieParagraphe;
	var legendeParagraphe;
	var tableauParagraphe;
	var celluleParagraphe;
	var cellulesParagraphe;
	var	i;
	var j;
	var message = '';
	var	nbParagraphe = 0;
	var done = true;
	var	categorie;
	var	categories = new Array();
	var categoriesNodes;
	var xhr = new XMLHttpRequest();
	var formData = new FormData();
	var contenu;

	contenu = document.getElementById('contenu');
	adminArticle_editParagrapheHook();
	formData.append("charte", 1);
	id = document.getElementById('article_id');
	if (parseInt(id.value))
	{
		formData.append("action", "modifier");
		formData.append("article", parseInt(id.value));
	}
	else
		formData.append("action", "creer");
	titre = document.getElementById('article_titre');
	resume = document.getElementById('article_resume');
	signature = document.getElementById('article_signature');
	image = document.getElementById('article_image');
	galerie = document.querySelector(".articleHead .illustration .galerieId");
	tableau = document.getElementById("tableau_article");
	if (titre.value == '')
	{
		message += 'Vous devez indiquer un titre pour l\'article.\n';
		done = false;
	}
	formData.append("titre", titre.value.replace(/\s+$/, '').replace(/^\s+/, ''));
	if (resume.value != '')
		formData.append("resume", resume.value.replace(/\s+$/, '').replace(/^\s+/, ''));
	if (signature.value != '')
		formData.append("signature", signature.value.replace(/\s+$/, '').replace(/^\s+/, ''));
	if (image && (image.src != "") && (imageId = image.src.match(/\bI([0-9]+)(?:_[0-9]*(?:_[0-9]*)?)?\b/)) && (imageId.length == 2))
	{
		legende = document.getElementById('article_legende');
		if (legende)
			formData.append("legende", legende.value.replace(/\s+$/, '').replace(/^\s+/, ''));
		formData.append("image", imageId[1]);
		if (/\bfloatLeft\b/.test(image.offsetParent.className))
			formData.append("position", 0);
		if (/\bfloatCenter\b/.test(image.offsetParent.className))
			formData.append("position", 2);
		if (/\bfloatRight\b/.test(image.offsetParent.className))
			formData.append("position", 1);
		formData.append("largeur", parseInt(image.width));
		formData.append("hauteur", parseInt(image.height));
	}
	if (galerie && galerie.value != "")
		formData.append("galerie", parseInt(galerie.value));
	if (tableau)
	{
		cellules = tableau.querySelectorAll("td input[type=\"text\"]");
		for (i = 0; i < cellules.length; i++)
		{
			cellule = cellules[i];
			formData.append(cellule.name, cellule.value.replace(/\s+$/, '').replace(/^\s+/, ''));
		}
	}
	for (i = 0; titreParagraphe = document.getElementById('paragraphe_titre_' + i); i++)
	{
		texteParagraphe = document.getElementById('paragraphe_texte_' + i);
		imageParagraphe = document.getElementById('paragraphe_image_' + i);
		galerieParagraphe = titreParagraphe.parentNode.querySelector(".illustration .galerieId");
		tableauParagraphe = document.getElementById("paragraphe_tableau_" + i);
		if (titreParagraphe.value != '')
			formData.append("paragraphe_titre_" + i, titreParagraphe.value.replace(/\s+$/, '').replace(/^\s+/, ''));
		if (texteParagraphe.value != '')
		{
			formData.append("paragraphe_texte_" + i, texteParagraphe.value.replace(/\s+$/, '').replace(/^\s+/, ''));
			if (imageParagraphe && (imageParagraphe.src != "") && (imageId = imageParagraphe.src.match(/\bI([0-9]+)(?:_[0-9]*(?:_[0-9]*)?)?\b/)) && (imageId.length == 2))
			{
				legendeParagraphe = document.getElementById("paragraphe_legende_" + i);
				if (legendeParagraphe)
					formData.append("paragraphe_legende_" + i, legendeParagraphe.value.replace(/\s+$/, '').replace(/^\s+/, ''));
				formData.append("paragraphe_image_" + i, imageId[1]);
				if (/\bfloatLeft\b/.test(imageParagraphe.offsetParent.className))
					formData.append("paragraphe_position_" + i, 0);
				if (/\bfloatCenter\b/.test(imageParagraphe.offsetParent.className))
					formData.append("paragraphe_position_" + i, 2);
				if (/\bfloatRight\b/.test(imageParagraphe.offsetParent.className))
					formData.append("paragraphe_position_" + i, 1);
				formData.append("paragraphe_largeur_" + i, parseInt(imageParagraphe.width));
				formData.append("paragraphe_hauteur_" + i, parseInt(imageParagraphe.height));
			}
			if (galerieParagraphe && galerieParagraphe.value != "")
				formData.append("paragraphe_galerie_" + i, parseInt(galerieParagraphe.value));
			if (tableauParagraphe)
			{
				cellulesParagraphe = tableauParagraphe.querySelectorAll("td input[type=\"text\"]");
				for (j = 0; j < cellulesParagraphe.length; j++)
				{
					celluleParagraphe = cellulesParagraphe[j];
					formData.append(celluleParagraphe.name, celluleParagraphe.value.replace(/\s+$/, '').replace(/^\s+/, ''));
				}
			}
			nbParagraphe++;
		}
		else
		{
			if (titreParagraphe.value != '')
			{
				message += 'Chaque paragraphe doit avoir du texte.\n';
				done = false;
			}
		}
	}
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
		formData.append("categories", categories.join(','));
	else
		message += 'Aucune catégorie n\'a été indiquée.\n';
	if (done)
	{
		if (message != "")
			done = confirm(message + "Enregistrer l'article quand même ?");
		if (done)
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
								if (contenu)
									contenu.innerHTML = xhr.responseText;
								adminArticle_editParagrapheHook();
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
			xhr.open("POST", "/administration/adminArticle.php", true);
			xhr.setRequestHeader("Cache-Control", "no-cache");
			xhr.send(formData);
		}
	}
	else
		alert(message);
	return false;
}

function adminArticle_montrerAideMemoire()
{
	var	aideEditArticle;
	
	aideEditArticle = document.getElementById('aideArticle');
	if (aideEditArticle)
		aideEditArticle.style.display = 'block';
	return false;
}

function adminArticle_masquerAideMemoire()
{
	var	aideEditArticle;
	
	aideEditArticle = document.getElementById('aideArticle');
	if (aideEditArticle)
		aideEditArticle.style.display = 'none';
	return false;
}

function adminArticle_selectCategorie(anchor, categoriePrefix, parentIds, parentSIds)
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

function adminArticle_supprimerIllustration(illustration)
{
	if (illustration)
		illustration.parentNode.removeChild(illustration);
}

function adminArticle_alignerIllustration(illustration, align)
{
	if (illustration)
	{
		illustration.classList.remove('floatLeft');
		illustration.classList.remove('floatRight');
		illustration.classList.remove('floatCenter');
		switch(align)
		{
			case 0:
				illustration.classList.add('floatLeft');
				break;
			case 1:
				illustration.classList.add('floatRight');
				break;
			case 2:
			default:
				illustration.classList.add('floatCenter');
				break;
		}
	}
}

function adminArticle_editTexteMoveDown(paragraphe)
{
	var index = -1;
	var re = /^paragraphe_([0-9]+)$/;
	var reg;
	
	if (!paragraphe)
		return false;
	reg = paragraphe.id.match(re);
	if (reg && (reg.length > 0))
		index = parseInt(reg[1]);			
	if (index >= 0)
		adminArticle_editTexteMove(index, index + 1);
}

function adminArticle_editTexteMoveUp(paragraphe)
{
	var index = -1;
	var re = /^paragraphe_([0-9]+)$/;
	var reg;
	
	if (!paragraphe)
		return false;
	reg = paragraphe.id.match(re);
	if (reg && (reg.length > 0))
		index = parseInt(reg[1]);			
	if (index > 0)
		adminArticle_editTexteMove(index, index - 1);
}

function adminArticle_editTexteMove(index, indexNext)
{
	var texteParagraphe;
	var titreParagraphe;
	var illustrationParagraphe;
	var tableauParagraphe;
	var texteParagrapheNext;
	var titreParagrapheNext;
	var illustrationParagrapheNext;
	var tableauParagrapheNext;
	var text;
	var noImg = false;
	var noImgNext = false;
	var mode = 1;
	var modeNext = 1;
	var noTableau = false;
	var noTableauNext = false;
	
	if ((index >= 0) && (indexNext >= 0))
	{
		titreParagraphe = document.getElementById('paragraphe_titre_' + index);
		titreParagrapheNext = document.getElementById('paragraphe_titre_' + indexNext);
		if (titreParagraphe && titreParagrapheNext)
		{
			text = titreParagraphe.value;
			titreParagraphe.value = titreParagrapheNext.value;
			titreParagrapheNext.value = text;
		}
		texteParagraphe = document.getElementById('paragraphe_texte_' + index);
		texteParagrapheNext = document.getElementById('paragraphe_texte_' + indexNext);
		if (texteParagraphe && texteParagrapheNext)
		{
			text = texteParagraphe.value;
			texteParagraphe.value = texteParagrapheNext.value;
			texteParagrapheNext.value = text;
		}
		illustrationParagraphe = document.querySelector("#paragraphe_" + index + " .illustration");
		illustrationParagrapheNext = document.querySelector("#paragraphe_" + indexNext + " .illustration");
		if (illustrationParagraphe && illustrationParagrapheNext)
		{
			if (illustrationParagraphe.classList.contains("modeGalerie"))
				mode = 2;
			if (illustrationParagrapheNext.classList.contains("modeGalerie"))
				modeNext = 2;
		}
		if (illustrationParagraphe && !illustrationParagrapheNext)
		{
			if (illustrationParagraphe.classList.contains("modeGalerie"))
				mode = 2;
			illustrationParagrapheNext = adminArticle_createIllustrationParagraphe(document.getElementById('paragraphe_' + indexNext), indexNext, mode);
			noImgNext = true;
		}
		if (!illustrationParagraphe && illustrationParagrapheNext)
		{
			if (illustrationParagrapheNext.classList.contains("modeGalerie"))
				modeNext = 2;
			illustrationParagraphe = adminArticle_createIllustrationParagraphe(document.getElementById('paragraphe_' + index), index, modeNext);
			noImg = true;
		}
		if (illustrationParagraphe && illustrationParagrapheNext)
		{
			text = illustrationParagraphe.className;
			illustrationParagraphe.className = illustrationParagrapheNext.className;
			illustrationParagrapheNext.className = text;
			if ((mode == 1) || (modeNext == 1))
				adminArticle_editTexteMoveImage(illustrationParagraphe, illustrationParagrapheNext, index, indexNext, noImg, noImgNext);
			if ((mode == 2) || (modeNext == 2))
				adminArticle_editTexteMoveGalerie(illustrationParagraphe, illustrationParagrapheNext, index, indexNext);
		}
		tableauParagraphe = document.querySelector("#paragraphe_" + index + " > div.tableau");
		tableauParagrapheNext = document.querySelector("#paragraphe_" + indexNext + " > div.tableau");
		if (tableauParagraphe && !tableauParagrapheNext)
		{
			tableauParagrapheNext = adminArticle_addTableauParagraphe(document.getElementById('paragraphe_' + indexNext), 0);
			noTableauNext = true;
		}
		if (!tableauParagraphe && tableauParagrapheNext)
		{
			tableauParagraphe = adminArticle_addTableauParagraphe(document.getElementById('paragraphe_' + index), 0);
			noTableau = true;
		}
		if (tableauParagraphe && tableauParagrapheNext)
			adminArticle_editTexteMoveTableau(tableauParagraphe, tableauParagrapheNext, index, indexNext, noTableau, noTableauNext);
	}
}

function adminArticle_editTexteMoveTableau(tableauParagraphe, tableauParagrapheNext, index, indexNext, noTableau, noTableauNext)
{
	var tableParagraphe;
	var tableParagrapheNext;
	var text;
	var next;

	tableParagraphe = tableauParagraphe.querySelector('table');
	tableParagrapheNext = tableauParagrapheNext.querySelector('table');
	if (tableParagraphe && tableParagrapheNext)
	{
		text = tableParagraphe.className;
		tableParagraphe.className = tableParagrapheNext.className;
		tableParagrapheNext.className = text;
		text = tableParagraphe.summary;
		tableParagraphe.summary = tableParagrapheNext.summary;
		tableParagrapheNext.summary = text;
		text = tableParagraphe.id;
		tableParagraphe.id = tableParagrapheNext.id;
		tableParagrapheNext.id = text;
		next = tableParagraphe.nextElementSibling;
		tableauParagraphe.removeChild(tableParagraphe);
		tableauParagrapheNext.insertBefore(tableParagraphe, tableParagrapheNext);
		tableauParagrapheNext.removeChild(tableParagrapheNext);
		if (next)
			tableauParagraphe.insertBefore(tableParagrapheNext, next);
		else
			tableauParagraphe.appendChild(tableParagrapheNext);
		inputs = tableParagraphe.querySelectorAll('input[type="text"][name]');
		adminArticle_editTexteMoveTableauCellules(inputs, indexNext);
		inputsNext = tableParagrapheNext.querySelectorAll('input[type="text"][name]');
		adminArticle_editTexteMoveTableauCellules(inputsNext, index);
	}
	if (noTableau && tableauParagrapheNext)
		tableauParagrapheNext.parentNode.removeChild(tableauParagrapheNext);
	if (noTableauNext && tableauParagraphe)
		tableauParagraphe.parentNode.removeChild(tableauParagraphe);
}

function adminArticle_editTexteMoveTableauCellules(inputs, index)
{
	var re = /^paragraphe_cellule_[0-9]+_([0-9]+)_([0-9]+)$/;
	var prefix = "paragraphe_cellule_";
	var ligne;
	var colonne;
	var reg;
	var i;
	
	if (inputs)
	{
		for (i = 0; i < inputs.length; i++)
		{
			reg = inputs[i].name.match(re);
			if (reg && (reg.length > 0))
			{
				ligne = parseInt(reg[1]);
				colonne = parseInt(reg[2]);
				inputs[i].name = prefix + index + "_" + ligne + "_" + colonne;
			}
		}
	}
}

function adminArticle_editTexteMoveGalerie(illustrationParagraphe, illustrationParagrapheNext, index, indexNext)
{
	var inputParagraphe;
	var inputParagrapheNext;
	var text;
	var legende;
	var imgs;
	var imgsNext;

	inputParagraphe = illustrationParagraphe.querySelector('.galerieId');
	inputParagrapheNext = illustrationParagrapheNext.querySelector('.galerieId');
	if (inputParagraphe && !inputParagrapheNext)
	{
		legende = illustrationParagrapheNext.querySelector("textarea:last-of-type");
		inputParagrapheNext = document.createElement("input");		
		if (inputParagrapheNext)
		{
			inputParagrapheNext.type = "hidden";
			inputParagrapheNext.className = "galerieId";
			if (legende)
				illustrationParagrapheNext.insertBefore(inputParagrapheNext, legende);
			else
				illustrationParagrapheNext.appendChild(inputParagrapheNext);
		}
	}
	if (!inputParagraphe && inputParagrapheNext)
	{
		legende = illustrationParagraphe.querySelector("textarea:last-of-type");
		inputParagraphe = document.createElement("input");		
		if (inputParagraphe)
		{
			inputParagraphe.type = "hidden";
			inputParagraphe.className = "galerieId";
			if (legende)
				illustrationParagraphe.insertBefore(inputParagraphe, legende);
			else
				illustrationParagraphe.appendChild(inputParagraphe);
		}
	}
	if (inputParagraphe && inputParagrapheNext)
	{
		text = inputParagraphe.value;
		inputParagraphe.value = inputParagrapheNext.value;
		inputParagrapheNext.value = text;
	}
	imgs = illustrationParagraphe.querySelectorAll(".imageGalerie");
	if (imgs)
	{
		for (i = (imgs.length - 1); i >= 0; i--)
			illustrationParagraphe.removeChild(imgs[i]);
	}
	imgsNext = illustrationParagrapheNext.querySelectorAll(".imageGalerie");
	if (imgsNext)
	{
		for (i = (imgsNext.length - 1); i >= 0; i--)
			illustrationParagrapheNext.removeChild(imgsNext[i]);
	}
	if (imgs && (imgs.length > 0))
	{
		legende = illustrationParagrapheNext.querySelector("textarea:last-of-type");
		for (i = 0; i < imgs.length; i++)
		{
			if (legende)
				illustrationParagrapheNext.insertBefore(imgs[i], legende);
			else
				illustrationParagrapheNext.appendChild(imgs[i]);
		}
	}
	else
		illustrationParagrapheNext.removeChild(inputParagrapheNext);
	if (imgsNext && (imgsNext.length > 0))
	{
		legende = illustrationParagraphe.querySelector("textarea:last-of-type");
		for (i = 0; i < imgsNext.length; i++)
		{
			if (legende)
				illustrationParagraphe.insertBefore(imgsNext[i], legende);
			else
				illustrationParagraphe.appendChild(imgsNext[i]);
		}
	}
	else
		illustrationParagraphe.removeChild(inputParagraphe);
}

function adminArticle_editTexteMoveImage(illustrationParagraphe, illustrationParagrapheNext, index, indexNext, noImg, noImgNext)
{
	var imgParagraphe;
	var imgParagrapheNext;
	var text;
	var width;
	var height;

	imgParagraphe = document.getElementById('paragraphe_image_' + index);
	imgParagrapheNext = document.getElementById('paragraphe_image_' + indexNext);
	if (imgParagraphe && !imgParagrapheNext)
	{
		illustrationParagrapheNext = adminArticle_createIllustrationParagraphe(document.getElementById('paragraphe_' + indexNext), indexNext, 1);
		imgParagrapheNext = document.getElementById('paragraphe_image_' + indexNext);
		noImgNext = true;
	}
	if (!imgParagraphe && imgParagrapheNext)
	{
		illustrationParagraphe = adminArticle_createIllustrationParagraphe(document.getElementById('paragraphe_' + index), index, 1);
		imgParagraphe = document.getElementById('paragraphe_image_' + index);
		noImg = true;
	}
	if (imgParagraphe && imgParagrapheNext)
	{
		width = imgParagraphe.offsetWidth;
		height = imgParagraphe.offsetHeight;
		imgParagraphe.width = imgParagrapheNext.offsetWidth;
		imgParagraphe.height = imgParagrapheNext.offsetHeight;
		imgParagrapheNext.width = width;
		imgParagrapheNext.height = height;
		text = imgParagraphe.src;
		imgParagraphe.src = imgParagrapheNext.src;
		imgParagrapheNext.src = text;
		text = imgParagraphe.className;
		imgParagraphe.className = imgParagrapheNext.className;
		imgParagrapheNext.className = text;
	}
	if (noImg && imgParagrapheNext)
		illustrationParagrapheNext.removeChild(imgParagrapheNext);
	if (noImgNext && imgParagraphe)
		illustrationParagraphe.removeChild(imgParagraphe);
}

function adminArticle_editTexteChanged(input)
{
	if (input)
	{
		input.value = input.value.replace(/\s+/g, ' ').replace(/^\s+/, '');
		input.style.height = '0px';
		if (input.offsetHeight != input.scrollHeight)
			input.style.height = input.scrollHeight + 'px';
		adminArticle_addOrRemoveParagrapheHook(input);
	}
	return true;
}

function adminArticle_editTexteMultiChanged(input)
{
	if (input)
	{
		input.value = input.value.replace(/^\s+/, '');
		input.style.height = '0px';
		if (input.offsetHeight != input.scrollHeight)
			input.style.height = input.scrollHeight + 'px';
		adminArticle_addOrRemoveParagrapheHook(input);
	}
	return true;
}

function adminArticle_editPhotoClose()
{
	var photoEditor = document.getElementById('photoEditor');
	var photoEdite = document.getElementById('photoEdite');
	var photoSelect = document.getElementById('photoSelect');
	var target;
	
	if (photoEdite)
	{
		photoEdite.offsetParent.className = "floatCenter";
		photoEdite.src = "/Images/noImg.png";
	}
	if (photoEditor)
	{
		photoEditor.className = "";
		target = document.getElementById(photoEditor.getAttribute('data-target'));
		photoEditor.setAttribute('data-target', "");
	}
	if (photoSelect)
		photoSelect.style.display = "none";
	if (target && (target.offsetParent.className == ""))
	{
		target.style.width = "100%";
		target.style.height = "10px";
		target.offsetParent.className = "";
	}
}

function adminArticle_editPhoto(anchor)
{
	var photoEditor = document.getElementById('photoEditor');
	if(!photoEditor)
	{
		var body = document.getElementsByTagName('body');
		var photoEditor = document.createElement('div');
		photoEditor.id='photoEditor';
		body[0].appendChild(photoEditor);
		photoEditor = document.getElementById('photoEditor');
	}
	var photoEdite = document.getElementById('photoEdite');
	var photoAligns = photoEditor.getElementsByTagName('input');
	var parent = anchor;
	var top;
	var left;
	var texte;
	if (photoEditor && photoEdite && parent)
	{
		photoEditor.setAttribute('data-target', anchor.id);
		photoEditor.className = "hide";
		top = anchor.offsetTop;
		left = anchor.offsetLeft;
		while (parent.offsetParent && (parent != parent.offsetParent) && (parent.id !== "contenu"))
		{
			parent = parent.offsetParent;
			top += parent.offsetTop;
			left += parent.offsetLeft;
		}
		if (parent.id == "contenu")
		{
			top -= parent.offsetTop;
			left -= parent.offsetLeft;
		}
		photoEdite.src = "";
		photoEditor.style.top = Math.max(0, top - 10) + "px";
		photoEditor.style.left = "5px";
		photoEditor.style.minWidth = anchor.offsetWidth + "px";
		photoEdite.style.maxWidth = "100%";
		if ((anchor.src != "") && (anchor.src != window.location))
			photoEdite.src = anchor.src;
		photoEditor.setAttribute('data-photo', photoEdite.src);
		photoEditor.className = "show";
		photoEdite.className = anchor.className;
		photoEdite.offsetParent.className = anchor.offsetParent.className;
		photoEdite.classList.remove("vide");
		for (var i = 0; i < photoAligns.length; i++)
		{
			if ((photoAligns[i].name == 'photoAlign') && (photoAligns[i].value == anchor.offsetParent.className) || ((anchor.offsetParent.className == "") && (photoAligns[i].value == "floatCenter")))
			{
				adminArticle_editPhotoChangeAlign(photoAligns[i]);
				break;
			}
		}
		if ((anchor.src == "") || (anchor.src == window.location) || anchor.src.match(/\/Images\/noImg\.png$/))
			adminArticle_changePhoto();
	}
}

function adminArticle_editPhotoChangeAlign(anchor)
{
	var photoEditor = document.getElementById('photoEditor');
	var photoEdite = document.getElementById('photoEdite');
	var photoAligns = document.getElementsByName('photoAlign');
	var target = document.getElementById(photoEditor.getAttribute('data-target'));
	
	for(var i = 0; i < photoAligns.length; i++)
		photoAligns[i].checked = false;
	if (anchor)
	{
		anchor.checked = true;
		switch(anchor.value)
		{
		case "floatLeft":
			photoEdite.offsetParent.className = "floatLeft";
			photoEdite.style.width = "214px";
			break;
		case "floatCenter":
			photoEdite.offsetParent.className = "floatCenter";
			photoEdite.style.width = "642px";
			break;
		case "floatRight":
			photoEdite.offsetParent.className = "floatRight";
			photoEdite.style.width = "214px";
			break;
		}
		if (target)
		{
			if ((photoEdite.src != "") && (photoEdite.src != window.location))
			{
				target.offsetParent.className = photoEdite.offsetParent.className;
				target.style.width = photoEdite.offsetWidth + 'px';
				target.style.height = photoEdite.offsetHeight + 'px';
			}
		}
	}
}

function adminArticle_selectImage(illustration)
{
	var	photoSelector;
	var xhr = new XMLHttpRequest();
	var article;
	var url;
	var categorie;
	var	categories = new Array();
	var categoriesNodes;
	
	photoSelector = document.getElementById('photoEditor');
	if(!photoSelector)
	{
		var body = document.getElementsByTagName('body');
		var photoEditor = document.createElement('div');
		photoEditor.id='photoEditor';
		body[0].appendChild(photoEditor);
		photoSelector = document.getElementById('photoEditor');
	}

	article = document.getElementById('article_id');
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
							//photoSelector.style.top = (illustration.offsetTop + illustration.scrollTop) + 'px';
							photoSelector.illustration = illustration;
							adminArticle_initPhotoEditor();
							break;
						case 500:
							alert("Erreur lors de la recherche de photos/images");
							break;
						default:
							alert("Erreur lors de l'envoi (" + xhr.status + ")");
							break;
					}
				}		
			}
			, false
		);
		photoSelector.illustration = null;
		photoSelector.style.display = "none";
		url = "/administration/adminArticle.php?action=searchimages";
		if (article && (article.value != ''))
			url += '&article=' + parseInt(article.value);
		if (categories.length > 0)
			url += '&categories=' + categories.join(',');
		xhr.open("GET", url, true);
		xhr.setRequestHeader("Cache-Control", "no-cache");
		xhr.send();
		return false;
	}
	return true;
}

function adminArticle_ImageUpdate(obj)
{
	var liste = document.getElementById("liste");
	var	archives = document.getElementById("archives");
	var	brouillons = document.getElementById("brouillons");
	var categorieIds = "";
	var filtre = document.getElementById("seach");
	var url = '/administration/adminArticle.php?action=updatesearchimages';
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
						adminArticle_initPhotoEditor();
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

function adminArticle_selectCategoriesImages(val)
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
	adminArticle_ImageUpdate(null);
}

function adminArticle_closeSelectionImages()
{
	var	photoSelector;
	
	var contenu = document.getElementById('contenu');
	var header = document.getElementById('header');
	header.style.display = 'block';
	contenu.style.position = 'relative';
	photoSelector = document.getElementById('photoEditor');
	if (photoSelector)
		photoSelector.style.display = "none";
}

function adminArticle_displayImport(section)
{
	var fen;
	fen = section.querySelector('.import');
	if(fen)
		fen.style.display = "block";		
}

function adminArticle_closeImport(fenPar)
{
	var fen;
	fen = document.getElementById('fen_import_head');
	if(fenPar)
		fenPar.style.display = "none";
	else
		fen.style.display = "none";	
}

function adminArticle_selectGalerie(illustration)
{
	var	galerieSelector;
	var xhr = new XMLHttpRequest();
	var article;
	var url;
	var categorie;
	var	categories = new Array();
	var categoriesNodes;

	galerieSelector = document.getElementById('galerieEditor');
	if(!galerieSelector)
	{
		var body = document.getElementsByTagName('body');
		var galerieEditor = document.createElement('div');
		galerieEditor.id='galerieEditor';
		body[0].appendChild(galerieEditor);
		galerieSelector = document.getElementById('galerieEditor');
	}
	galerieSelector.style.display='block';
	article = document.getElementById('article_id');
	if (galerieEditor && xhr)
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
							galerieEditor.innerHTML = xhr.responseText;
							galerieEditor.style.display = "block";
							//galerieEditor.style.top = (illustration.offsetTop + illustration.scrollTop) + 'px';
							galerieEditor.illustration = illustration;
							break;
						case 500:
							alert("Erreur lors de la recherche de photos/images");
							break;
						default:
							alert("Erreur lors de l'envoi (" + xhr.status + ")");
							break;
					}
				}		
			}
			, false
		);
		galerieEditor.illustration = null;
		galerieEditor.style.display = "none";
		url = "/administration/adminArticle.php?action=searchgaleries";
		if (article && (article.value != ''))
			url += '&article=' + parseInt(article.value);
		if (categories.length > 0)
			url += '&categories=' + categories.join(',');
		xhr.open("GET", url, true);
		xhr.setRequestHeader("Cache-Control", "no-cache");
		xhr.send();
		return false;
	}
	return true;
}

function adminArticle_closeSelectionGalerie()
{
	var	galerieEditor;
	var contenu = document.getElementById('contenu');
	var header = document.getElementById('header');
	header.style.display = 'block';
	contenu.style.position = 'relative';
	galerieEditor = document.getElementById('galerieEditor');
	if (galerieEditor)
		galerieEditor.style.display = "none";
}

function adminArticle_usePhoto(id, w, h)
{
	var illustration;
	var img;
	var	photoSelector;
	var galerie;
	var i;
	var input;
	var contenu = document.getElementById('contenu');
	var header = document.getElementById('header');
	header.style.display = 'block';
	contenu.style.position = 'relative';
	photoSelector = document.getElementById('photoEditor');
	if (photoSelector && photoSelector.illustration)
		illustration = photoSelector.illustration;
	if (illustration)
	{
		illustration.classList.remove("modeGalerie");
		galerie = illustration.querySelectorAll(".imageGalerie");
		if (galerie)
		{
			for (i = galerie.length - 1; i >= 0; i--)
				illustration.removeChild(galerie[i]);
		}		
		input = illustration.querySelector(".galerieId");
		if (input)
			illustration.removeChild(input);
		img = illustration.querySelector(".illustrationImage");
		if (img)
		{
			img.src = "/Images/I" + parseInt(id);
			img.removeAttribute("height");
			img.removeAttribute("width");
			photoSelector.style.display = "none";
			illustration.classList.add("modeImage");
		}
	}
}

function adminArticle_useGalerie(id, imagesIds)
{
	var illustration;
	var img;
	var	galerieEditor;
	var imgsIds;
	var i;
	var span;
	var galerie;
	var input;
	var contenu = document.getElementById('contenu');
	var header = document.getElementById('header');
	header.style.display = 'block';
	contenu.style.position = 'relative';

	galerieEditor = document.getElementById('galerieEditor');
	if (galerieEditor && galerieEditor.illustration)
		illustration = galerieEditor.illustration;
	if (illustration)
	{
		img = illustration.querySelector(".illustrationImage");
		if (img)
			illustration.removeChild(img);
		galerie = illustration.querySelectorAll(".imageGalerie");
		if (galerie)
		{
			for (i = galerie.length - 1; i >= 0; i--)
				illustration.removeChild(galerie[i]);
		}
		legende = illustration.querySelector("textarea:last-of-type");
		input = illustration.querySelector(".galerieId");
		if (!input)
		{
			input = document.createElement("input");
			if (input)
			{
				input.type = "hidden";
				input.className = "galerieId";
				input.value = parseInt(id);
				if (legende)
					illustration.insertBefore(input, legende);
				else
					illustration.appendChild(input);
			}
		}
		else
			input.value = parseInt(id);
		imagesIds = imagesIds.split(",");
		for (i = 0; i < imagesIds.length; i++)
		{
			span = document.createElement("span");
			if (span)
			{
				span.className = "imageGalerie";
				span.innerHTML = '<img id="image_' + parseInt(imagesIds[i]) + '" src="/Images/I' + parseInt(imagesIds[i]) + '"/>';
				if (legende)
					illustration.insertBefore(span, legende);
				else
					illustration.appendChild(span);
			}
		}
		illustration.classList.remove("modeImage");
		illustration.classList.add("modeGalerie");
		galerieEditor.style.display = "none";
	}
}

function adminArticle_resizeImage(illustration, width)
{
	var img;

	if (illustration)
	{
		img = illustration.querySelector(".illustrationImage");
		if (img)
		{
			img.removeAttribute("height");
			img.width = width;
		}
	}
}

function adminArticle_addIllustration(head)
{
	var contenu = document.getElementById('contenu');
	var header = document.getElementById('header');
	header.style.display = 'none';
	contenu.style.position = 'fixed';
	var illustration;
	if (head)
	{
		illustration = adminArticle_createIllustration(head);
		if (illustration)
			adminArticle_selectImage(illustration);
	}
}

function adminArticle_addIllustrationParagraphe(paragraphe, mode)
{
	var illustration;
	var re = /^paragraphe_([0-9]+)$/;
	var reg;
	var contenu = document.getElementById('contenu');
	var header = document.getElementById('header');
	header.style.display = 'none';
	contenu.style.position = 'fixed';
	
	if (paragraphe)
	{
		reg = paragraphe.id.match(re);
		if (reg && (reg.length > 0))
			index = parseInt(reg[1]);			
		if (index >= 0)
		{
			illustration = adminArticle_createIllustrationParagraphe(paragraphe, index, mode);
			if (illustration)
			{
				switch(mode)
				{
				case 1:
					adminArticle_selectImage(illustration);
					break;
				case 2:
					adminArticle_selectGalerie(illustration);
					break;
				}
			}
		}
	}
}

function adminArticle_createIllustrationParagraphe(paragraphe, index, mode)
{
	var illustration;
	var paragrapheTexte;
	var legende;
	var img;
	
	if (paragraphe && (index >= 0))
	{
		illustration = paragraphe.querySelector(".illustration");
		if (!illustration)
		{
			illustration = document.createElement("div");
			paragrapheTexte = document.getElementById("paragraphe_texte_" + index);
			if (paragrapheTexte)
			{
				illustration.className = "floatCenter illustration";
				illustration.innerHTML = '<div class="commandes">'
					+ '<input type="range" class="imageSize" min="50" max="642" step="1" value="300" onchange="return adminArticle_resizeImage(this.parentNode.parentNode, this.value);"/>'
					+ '<span class="icone galerie" title="Changer de galerie" onclick="return adminArticle_selectGalerie(this.parentNode.parentNode);"></span>'
					+ '<span class="icone photo" title="Changer de photo/image" onclick="return adminArticle_selectImage(this.parentNode.parentNode);"></span>'
					+ '<span class="icone gaucheToute" title="Aligner à gauche" onclick="return adminArticle_alignerIllustration(this.parentNode.parentNode, 0);"></span>'
					+ '<span class="icone centre" title="Center" onclick="return adminArticle_alignerIllustration(this.parentNode.parentNode, 2);"></span>'
					+ '<span class="icone droiteToute" title="Aligner à droite" onclick="return adminArticle_alignerIllustration(this.parentNode.parentNode, 1);"></span>'
					+ '<span class="icone delete" title="Supprimer l\'illustration" onclick="return adminArticle_supprimerIllustration(this.parentNode.parentNode);"></span>'
					+ '</div>';
				switch(mode)
				{
				case 1:
					illustration.classList.add("modeImage");
					illustration.innerHTML += '<img class="illustrationImage" id="paragraphe_image_' + index + '" onclick="adminArticle_editPhoto(this)" src="/Images/noImg.png"/>';
					break;
				case 2:
					illustration.classList.add("modeGalerie");
					illustration.mode = "galerie";
					break;
				}
				illustration.innerHTML += '<textarea id="paragraphe_legende_' + index + '" title="Légende" placeholder="Légende" class="legende" onkeyup="adminArticle_editTexteMultiChanged(this);" onpaste="adminArticle_editTexteMultiChanged(this)" onchange="adminArticle_editTexteMultiChanged(this)"></textarea>';
				paragraphe.insertBefore(illustration, paragrapheTexte);
				return illustration;
			}
		}
		else
		{
			switch(mode)
			{
			case 1:
				img = illustration.querySelector(".illustrationImage");
				if (!img)
				{
					img = document.createElement("img");
					if (img)
					{
						img.id = "paragraphe_image_" + index;
						img.onclick = adminArticle_editPhoto(img);
						img.className = "illustrationImage";
						img.src = "/Images/noImg.png";
						legende = paragraphe.querySelector("#paragraphe_legende_" + index);
						if (legende)
							illustration.insertBefore(img, legende);
						else
							illustration.appendChild(img);
					}
				}
				break;
			}
			return illustration;
		}
	}
	return false;
}

function adminArticle_createIllustration(head)
{
	var illustration;
	var resume;
	var legende;
	var img;
	
	if (head)
	{
		illustration = head.querySelector(".illustration");
		if (!illustration)
		{
			illustration = document.createElement("div");
			resume = document.getElementById("article_resume");
			if (resume)
			{
				illustration.className = "floatCenter illustration modeImage";
				illustration.innerHTML = '<div class="commandes">'
					+ '<input type="range" class="imageSize" min="50" max="642" step="1" value="300" onchange="return adminArticle_resizeImage(this.parentNode.parentNode, this.value);"/>'
					+ '<span class="icone galerie" title="Changer de galerie" onclick="return adminArticle_selectGalerie(this.parentNode.parentNode);"></span>'
					+ '<span class="icone photo" title="Changer de photo/image" onclick="return adminArticle_selectImage(this.parentNode.parentNode);"></span>'
					+ '<span class="icone gaucheToute" title="Aligner à gauche" onclick="return adminArticle_alignerIllustration(this.parentNode.parentNode, 0);"></span>'
					+ '<span class="icone centre" title="Center" onclick="return adminArticle_alignerIllustration(this.parentNode.parentNode, 2);"></span>'
					+ '<span class="icone droiteToute" title="Aligner à droite" onclick="return adminArticle_alignerIllustration(this.parentNode.parentNode, 1);"></span>'
					+ '<span class="icone delete" title="Supprimer l\'illustration" onclick="return adminArticle_supprimerIllustration(this.parentNode.parentNode);"></span>'
					+ '</div>';
				illustration.innerHTML += '<img class="illustrationImage" id="article_image" onclick="adminArticle_editPhoto(this)" src="/Images/noImg.png"/>';
				illustration.innerHTML += '<textarea id="article_legende" title="Légende" placeholder="Légende" class="legende" onkeyup="adminArticle_editTexteMultiChanged(this);" onpaste="adminArticle_editTexteMultiChanged(this)" onchange="adminArticle_editTexteMultiChanged(this)"></textarea>';
				head.insertBefore(illustration, resume);
				return illustration;
			}
		}
		else
		{
			img = illustration.querySelector(".illustrationImage");
			if (!img)
			{
				img = document.createElement("img");
				if (img)
				{
					img.id = "article_image";
					img.onclick = adminArticle_editPhoto(img);
					img.className = "illustrationImage";
					img.src = "/Images/noImg.png";
					legende = head.querySelector("#article_legende");
					if (legende)
						illustration.insertBefore(img, legende);
					else
						illustration.appendChild(img);
				}
			}
			return illustration;
		}
	}
	return false;
}

function adminArticle_supprimerTableau(tableau)
{
	if (tableau)
		tableau.parentNode.removeChild(tableau);
}

function adminArticle_ajouterLigneTableau(tableau, prefix)
{
	var table;
	var row;
	var i;
	var cols;
	var rows;

	if (tableau)
	{
		table = tableau.querySelector("table");
		if (table)
		{
			rows = table.querySelectorAll("tr").length;
			cols = table.querySelectorAll("tr:first-of-type > td").length;
			if (cols == 0)
				cols = 3;
			row = table.insertRow(-1);
			for (i = 0; i < cols; i++)
				adminArticle_creerCellule(row, rows + 1, i + 1, prefix);
		}
	}
}

function adminArticle_ajouterColonneTableau(tableau, prefix)
{
	var table;
	var rows;
	var i;

	if (tableau)
	{
		table = tableau.querySelector("table");
		if (table)
		{
			rows = table.querySelectorAll("tr");
			cols = table.querySelectorAll("tr:first-of-type > td").length;
			for (i = 0; i < rows.length; i++)
				adminArticle_creerCellule(rows[i], i + 1, cols + 1, prefix, table);
		}
	}
}

function adminArticle_creerCellule(row, ligne, colonne, prefix, table)
{
	var cell;
	
	if (row)
	{
		cell = row.insertCell(-1);
		if (cell)
		{
			if(ligne==1 || colonne==1)
			{
				if(colonne==1 && ligne !== 1)
					cell.className="enteteLigne";
				if(ligne==1 && colonne !== 1)
					cell.id="enteteColonne";

				if((ligne+colonne) !== 2)
				{
					
					cmd = '<div class="commandes">';
					
					if(ligne != 1)
					{
						cmd += '<input type="hidden" name="cellule_entete_'+(ligne-1)+'_'+(colonne-1)+'" class="idligne">';
						cmd += '<span class="icone up" title="Monter la ligne" onclick="return adminArticle_monterLigne(this.parentNode.parentNode);"></span>';
						cmd += '<span class="icone down" title="Descendre la ligne" onclick="return adminArticle_descendreLigne(this.parentNode.parentNode);"></span>';
						cmd += '<span class="icone deleteLigne" title="Supprimer la ligne" onclick="return adminArticle_supprimerLigne(this.parentNode.parentNode);"></span>';
					}
					if(colonne != 1)
					{
						cmd += '<input type="hidden" name="cellule_entete_'+(ligne-1)+'_'+(colonne-1)+'" class="idcolonne">';
						cmd += "<span class='icone gauche' title='Reculer la colonne' onclick='return adminArticle_reculerColonne(this.parentNode.parentNode);'></span>";
						cmd += "<span class='icone droite' title='Avancer la colonne' onclick='return adminArticle_avancerColonne(this.parentNode.parentNode);'></span>";
						cmd += "<span class='icone deleteColonne' title='Supprimer la colonne' onclick='return adminArticle_supprimerColonne(this.parentNode.parentNode);'></span>";
					}
					cmd += '</div>';
					cell.innerHTML = cmd;
				}
				//+ '<input type="text" name="' + prefix + '_' + ligne + '_' + colonne + '" value=""/>';
			}
			else
			{
				function mouseOver(){"document.getElementById('entCols_"+(colonne-1)+"').style.display='block'"}
				cell.innerHTML += '<input type="text" name="' + prefix + '_' + (ligne-1) + '_' + (colonne-1) + '" value=""/>';
				cell.addEventListener("mouseover", mouseOver(), true);
			}
		}
	}
}

function adminArticle_addTableau(head)
{
	var tableau;
	var resume;
	
	if (head)
	{
		resume = head.querySelector("#article_resume");
		tableau = head.querySelector("div.tableau");
		if (!tableau)
		{
			tableau = document.createElement("div");
			if (tableau)
			{
				tableau.className = "tableau";
				tableau.innerHTML = '<table id="tableau_article"></table>'
					+ '<div class="commandes"><span class="icone addLigne" title="Ajouter une ligne" onclick="return adminArticle_ajouterLigneTableau(this.parentNode.parentNode, \'cellule\');"></span>'
					+ '<span class="icone addColonne" title="Ajouter une colonne" onclick="return adminArticle_ajouterColonneTableau(this.parentNode.parentNode, \'cellule\');"></span>'
					+ '<span class="icone delete" title="Supprimer le tableau" onclick="return adminArticle_supprimerTableau(this.parentNode.parentNode);"></span>'
					+ '</div>'
				if (resume)
					head.insertBefore(tableau, resume);
				else
					head.appendChild(tableau);
				adminArticle_ajouterLigneTableau(tableau, "cellule");
				adminArticle_ajouterLigneTableau(tableau, "cellule");
				adminArticle_ajouterLigneTableau(tableau, "cellule");
			}
		}
	}
}

function adminArticle_addTableauParagraphe(paragraphe, nouvellesLignes)
{
	var tableau = null;
	var texte;
	var prefix = "paragraphe_cellule_";	
	var re = /^paragraphe_([0-9]+)$/;
	var id = 0;
	var reg;
	var i;
	
	if (paragraphe)
	{
		reg = paragraphe.id.match(re);
		if (reg && (reg.length > 0))
			id = parseInt(reg[1]);
		texte = paragraphe.querySelector(".texte");
		tableau = paragraphe.querySelector("div.tableau");
		if (!tableau)
		{
			tableau = document.createElement("div");
			if (tableau)
			{
				prefix += id;
				tableau.className = "tableau";
				tableau.innerHTML = '<div class="commandes"><span class="icone addLigne" title="Ajouter une ligne" onclick="return adminArticle_ajouterLigneTableau(this.parentNode.parentNode, \'' + prefix + '\');"></span>'
					+ '<span class="icone addColonne" title="Ajouter une colonne" onclick="return adminArticle_ajouterColonneTableau(this.parentNode.parentNode, \'' + prefix + '\');"></span>'
					+ '<span class="icone delete" title="Supprimer le tableau" onclick="return adminArticle_supprimerTableau(this.parentNode.parentNode);"></span>'
					+ '</div>'
					+ '<table id="paragraphe_tableau_' + id + '"></table>';
				if (texte)
					paragraphe.insertBefore(tableau, texte);
				else
					paragraphe.appendChild(tableau);
				for (i = 0; i < nouvellesLignes; i++)
					adminArticle_ajouterLigneTableau(tableau, prefix);
			}
		}
	}
	return tableau;
}

function adminArticle_supprimerLigne(cellule)
{
	var re = /^(paragraphe_cellule_[0-9]+|cellule_entete|cellule)_([0-9]+)_([0-9]+)$/;
	var prefix;
	var ligne;
	var colonne;
	var reg;
	var inputs;
	var i;
	var tr;
	var table;
	
	if (cellule)
	{
		reg = cellule.querySelector('input[type="hidden"][name]').name.match(re);
		if(!reg)
			reg = cellule.querySelector('input[type="text"][name]').name.match(re);
		if (reg && (reg.length > 0))
		{
			prefix = reg[1];
			ligne = parseInt(reg[2]);
			colonne = parseInt(reg[3]);
		}
		tr = cellule.parentNode;
		table = tr.parentNode;
		table.removeChild(tr);	
		inputs = table.querySelectorAll('input[type="text"][name]');
		hidden = table.querySelectorAll('.idligne');
		test2 = cellule.nextSibling;
		montd = test2.querySelector('input[type="text"][name]').name.match(re);

		if(montd)
			prefixcellule = montd[1];
		if (inputs)
		{
			for (i = 0; i < inputs.length; i++)
			{
				reg = inputs[i].name.match(re);
				if (reg && (reg.length > 0))
				{
					if (ligne < parseInt(reg[2]))
					{
						inputs[i].name = prefixcellule + "_" + (parseInt(reg[2]) - 1) + "_" + parseInt(reg[3]);
					}
				}
			}
		}
		if (hidden)
		{
			for (i = 0; i < hidden.length; i++)
			{
				reg = hidden[i].name.match(re);
				if (reg && (reg.length > 0))
				{
					if (ligne < parseInt(reg[2]))
					{
						hidden[i].name = "cellule_entete_" + (parseInt(reg[2]) - 1) + "_0";
					}
				}
			}
		}
	}
}

function adminArticle_supprimerColonne(cellule)
{
	var re = /^(paragraphe_cellule_[0-9]+|cellule_entete|cellule)_([0-9]+)_([0-9]+)$/;
	var prefix;
	var ligne;
	var colonne;
	var reg;
	var inputs;
	var i;
	var tr;
	var table;
	var td;
	var tabprefix = cellule.parentNode.parentNode.parentNode.id;
	if (cellule)
	{
		reg = cellule.querySelector('input[type="hidden"][name]').name.match(re);
		if(!reg)
			reg = cellule.querySelector('input[type="text"][name]').name.match(re);
		if (reg && (reg.length > 0))
		{
			prefix = reg[1];
			ligne = parseInt(reg[2]);
			colonne = parseInt(reg[3]);
		}
		
		table = document.getElementById(tabprefix);
		inputs = table.querySelectorAll('input[type="text"][name]');
		hidden = table.querySelectorAll('.idcolonne');
		test = cellule.parentNode;
		test2 = test.nextSibling;
		montr = test2.querySelector('input[type="text"][name]').name.match(re);
		if(montr)
			prefixcellule = montr[1];
		if (inputs)
		{
			for (i = 0; i < inputs.length; i++)
			{
				reg = inputs[i].name.match(re);
				if (reg && (reg.length > 0))
				{
					if (colonne == parseInt(reg[3]))
					{
						inputs[i].parentNode.parentNode.removeChild(inputs[i].parentNode);
						
					}
					if (colonne < parseInt(reg[3]))
					{
						inputs[i].name = prefixcellule + "_" + parseInt(reg[2]) + "_" + (parseInt(reg[3]) - 1);
						hidden[parseInt(reg[3])-1].name = "cellule_entete_1_" + (parseInt(reg[3])-1);
						hidden[parseInt(reg[3])-1].parentNode.id = "cellule_entCols_"+ (parseInt(reg[3])-1);
						hidden[parseInt(reg[3])-1].parentNode.parentNode.className = "colonne_"+ (parseInt(reg[3])-1);
					}
						
				}
			}
			if(cellule.parentNode)
			{
				td=cellule;
				tr = td.parentNode;
				tr.removeChild(td);
			}
		}
	}
}

function adminArticle_monterLigne(cellule)
{
	var re = /^(paragraphe_cellule_[0-9]+|cellule_entete|cellule)_([0-9]+)_([0-9]+)$/;
	var prefix;
	var ligne;
	var colonne;
	var reg;
	var inputs;
	var i;
	var tr;
	var trPrev;
	var table;
	
	if (cellule)
	{
		reg = cellule.querySelector('input[type="hidden"][name]').name.match(re);
		if(!reg)
			reg = cellule.querySelector('input[type="text"][name]').name.match(re);
		if (reg && (reg.length > 0))
		{
			prefix = reg[1];
			ligne = parseInt(reg[2]);
			colonne = parseInt(reg[3]);
		}
		tr = cellule.parentNode;
		table = tr.parentNode;
		trPrev = tr.previousElementSibling;
		if (trPrev && (ligne>1))
		{
			table.removeChild(tr);
			table.insertBefore(tr, trPrev);
			inputs = table.querySelectorAll('input[type="text"][name]');
			if (inputs)
				adminArticle_switchLignes(inputs, ligne, ligne - 1,table);
		}
	}
}

function adminArticle_descendreLigne(cellule)
{
	var re = /^(paragraphe_cellule_[0-9]+|cellule_entete|cellule)_([0-9]+)_([0-9]+)$/;
	var prefix;
	var ligne;
	var colonne;
	var reg;
	var inputs;
	var i;
	var tr;
	var table;
	var trNext;
	
	if (cellule)
	{
		reg = cellule.querySelector('input[type="hidden"][name]').name.match(re);
		if(!reg)
			reg = cellule.querySelector('input[type="text"][name]').name.match(re);
		if (reg && (reg.length > 0))
		{
			prefix = reg[1];
			ligne = parseInt(reg[2]);
			colonne = parseInt(reg[3]);
		}
		tr = cellule.parentNode;
		table = tr.parentNode;
		trNext = tr.nextElementSibling;
		if (trNext)
		{
			table.removeChild(trNext);
			table.insertBefore(trNext, tr);
			inputs = table.querySelectorAll('input[type="text"][name]');
			if (inputs)
				adminArticle_switchLignes(inputs, ligne, ligne + 1,table);
		}
	}
}

function adminArticle_switchLignes(inputs, ligne1, ligne2,table)
{
	var re = /^(paragraphe_cellule_[0-9]+|cellule_entete|cellule)_([0-9]+)_([0-9]+)$/;
	var prefix;
	var ligne;
	var colonne;
	var reg;
	var inputs;
	var i;
	hidden = table.querySelectorAll('.idligne');
	ent1 = hidden[ligne1-1];
	ent2 = hidden[ligne2-1];
	ent1.name = "cellule_entete_"+ligne1+"_1";
	ent2.name = "cellule_entete_"+ligne2+"_1";
	if (inputs && (ligne1 != ligne2))
	{
		for (i = 0; i < inputs.length; i++)
		{
			reg = inputs[i].name.match(re);
			if (reg && (reg.length > 0))
			{
				prefix = reg[1];
				ligne = parseInt(reg[2]);
				colonne = parseInt(reg[3]);
				if (ligne == ligne1)
					inputs[i].name = prefix + "_" + ligne2 + "_" + colonne;
				else
				{
					if (ligne == ligne2)
						inputs[i].name = prefix + "_" + ligne1 + "_" + colonne;
				}
			}
		}
	}
}

function adminArticle_reculerColonne(cellule)
{
	var re = /^(paragraphe_cellule_[0-9]+|cellule_entete|cellule)_([0-9]+)_([0-9]+)$/;
	var prefix;
	var ligne;
	var colonne;
	var reg;
	var inputs;
	var i;
	var tr;
	var prev;
	var table;
	
	if (cellule)
	{
		reg = cellule.querySelector('input[type="hidden"][name]').name.match(re);
		if (reg && (reg.length > 0))
		{
			prefix = reg[1];
			ligne = parseInt(reg[2]);
			colonne = parseInt(reg[3]);
		}
			table = cellule.parentNode.parentNode.parentNode.parentNode;
			if(table)
				inputs = table.querySelectorAll('input[type="text"][name]');
			if (inputs)
				adminArticle_switchColonnes(inputs, colonne, colonne - 1);
	}
}

function adminArticle_avancerColonne(cellule)
{
	var re = /^(paragraphe_cellule_[0-9]+|cellule_entete)_([0-9]+)_([0-9]+)$/;
	var prefix;
	var ligne;
	var colonne;
	var reg;
	var inputs;
	var i;
	var tr;
	var table;
	var next;
	
	if (cellule)
	{
		reg = cellule.querySelector('input[type="hidden"][name]').name.match(re);
		if(!reg)
			reg = cellule.querySelector('input[type="text"][name]').name.match(re);
		if (reg && (reg.length > 0))
		{
			prefix = reg[1];
			ligne = parseInt(reg[2]);
			colonne = parseInt(reg[3]);
		}
		table = cellule.parentNode.parentNode.parentNode;
		if(table)	
			inputs = table.querySelectorAll('input[type="text"][name]');
		if (inputs)
			adminArticle_switchColonnes(inputs, colonne, colonne + 1);
	}
}

function adminArticle_switchColonnes(inputs, colonne1, colonne2)
{
	var re = /^(paragraphe_cellule_[0-9]+|cellule_entete|cellule)_([0-9]+)_([0-9]+)$/;
	var prefix;
	var ligne;
	var colonne;
	var reg;
	var inputs;
	var i;
	var cellule;
	var cellule2;
	var tr;
	var next1;
	var input2;
	
	if (inputs && (colonne1 != colonne2))
	{
		if (colonne1 < colonne2)
			adminArticle_switchColonnes(inputs, colonne2, colonne1);
		else
		{
			for (i = 0; i < inputs.length; i++)
			{
				reg = inputs[i].name.match(re);
				if (reg && (reg.length > 0))
				{
					prefix = reg[1];
					ligne = parseInt(reg[2]);
					colonne = parseInt(reg[3]);
					if (colonne == colonne1)
					{
						cellule = inputs[i].parentNode;
						next = cellule.nextElementSibling;
						tr = cellule.parentNode;
						input2 = tr.querySelector("input[type='text'][name='" + prefix + "_" + ligne + "_" + colonne2 + "']");
						if (input2)
						{
							cellule2 = input2.parentNode;
							tr.removeChild(cellule);
							tr.insertBefore(cellule, cellule2);
							tr.removeChild(cellule2);
							if (next)
								tr.insertBefore(cellule2, next);
							else
								tr.appendChild(cellule2);
							inputs[i].name = prefix + "_" + ligne + "_" + colonne2;
							input2.name = prefix + "_" + ligne + "_" + colonne1;
						}
					}
				}
			}
		}
	}
}

function adminArticle_validerImportHead()
{

	var xhr = new XMLHttpRequest();
	var formData = new FormData();
	var message = '';
	var done = true;
	var file;
	var fichier;
	var progress;
	var articleId = document.getElementById('article_id').value;
	var divtable;
	var head = document.querySelector('.articleHead');

	divtable = head.querySelector(".tableau");
	fichier = document.getElementById('FileXLSX');
	progress = document.getElementById('documentProgress');
	formData.append("action", "import");

	if (articleId)
		formData.append("article", articleId);

	if (fichier && (fichier.files.length > 0) && (fichier.files[0].type != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"))
	{
		message += 'Seuls les fichiers XLSX sont autorisés.\n';
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
				formData.append("file", file);
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

									resume = head.querySelector("#article_resume");
									divtable = head.querySelector("div.tableau");
									if (!divtable)
									{
										divtable = document.createElement("div");
										divtable.className = "tableau";
										head.insertBefore(divtable, resume);
									}

									if (divtable)
									{
										
										divtable.innerHTML = "";
										tabA = document.createElement("div");
										tabA.className = 'tableauAjax';
										head.insertBefore(tabA, divtable);
										tabA.innerHTML = xhr.responseText;
										divtableNew = tabA.querySelector("div.tableau");
										if(divtableNew)
											divtable.innerHTML = divtableNew.innerHTML;
										head.removeChild(tabA);
										document.getElementById('fen_import_head').style.display = 'none';
									}
									
									break;
								case 500:
									if (xhr.responseText.length > 0)
										alert("Veuillez choisir un fichier valide");
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
				xhr.open("POST", '/administration/adminArticle.php', true);
				xhr.setRequestHeader("Cache-Control", "no-cache");
				xhr.send(formData);
			}
		}
	}
	else
		alert(message);
	return false;
}

function adminArticle_validerImportParagraphe(fenPar)
{

	var xhr = new XMLHttpRequest();
	var formData = new FormData();
	var message = '';
	var done = true;
	var file;
	var fichier;
	var progress;
	var articleId = document.getElementById('article_id').value;
	var divtable;
	var re = /^paragraphe_[0-9]+|fen_import_([0-9]+)$/;

	reg = fenPar.id.match(re);
	if (reg && (reg.length > 0))
		index = parseInt(reg[1]);
	
	var paragraphe = document.querySelector('#paragraphe_'+index);
	fichier = document.getElementById('FileXLSX_'+index);
	progress = document.getElementById('documentProgress');
	formData.append("action", "import");

	if (articleId)
		formData.append("article", articleId);

		formData.append("paragraphe", index);
	if (fichier && (fichier.files.length > 0) && (fichier.files[0].type != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"))
	{
		message += 'Seuls les fichiers XLSX sont autorisés.\n';
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
				formData.append("file", file);
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

									resume = paragraphe.querySelector("#paragraphe_texte_"+index);
									divtable = paragraphe.querySelector("div.tableau");
									if (!divtable)
									{
										divtable = document.createElement("div");
										divtable.className = "tableau";
										paragraphe.insertBefore(divtable, resume);
									}

									if (divtable)
									{
										
										divtable.innerHTML = "";
										tabA = document.createElement("div");
										tabA.className = 'tableauAjax';
										paragraphe.insertBefore(tabA, divtable);
										tabA.innerHTML = xhr.responseText;
										divtableNew = tabA.querySelector("div.tableau");
										if(divtableNew)
											divtable.innerHTML = divtableNew.innerHTML;
										paragraphe.removeChild(tabA);
										document.getElementById('fen_import_'+index).style.display = 'none';
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
				xhr.open("POST", '/administration/adminArticle.php', true);
				xhr.setRequestHeader("Cache-Control", "no-cache");
				xhr.send(formData);
			}
		}
	}
	else
		alert(message);
	return false;
}

function getSelection(textarea) {
		if (textarea.setSelectionRange)
			return textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
		}
		
function getSelectionStart(textarea){
 	if ( typeof textarea.selectionStart != 'undefined' )
 		return textarea.selectionStart;
}

function getSelectionEnd(textarea) {
	if ( typeof textarea.selectionEnd != 'undefined' )
 		return textarea.selectionEnd;
 }
 
function setCaretPos(start, end, textarea) {
 	end = end || start; 	textarea.focus();
 	if (textarea.setSelectionRange)
 		textarea.setSelectionRange(start, end);
 	else if (document.selection) {
 		var range = textarea.createTextRange();
 		range.moveStart('character', start);
 		range.moveEnd('character', - textarea.value.length + end);
 		range.select();
 	}
 } 
 

function replaceSelection(str, keep, textarea)
{
 	textarea.focus();
 	var start = getSelectionStart(textarea);
 	var stop = getSelectionEnd(textarea);
 	var end = start + str.length;
 	var scrollPos = textarea.scrollTop;
 	textarea.value = textarea.value.substring(0, start) + str + textarea.value.substring(stop);
 	if ( keep ) setCaretPos(start, end, textarea);
 	else setCaretPos(end,'null',textarea);
 	textarea.scrollTop = scrollPos;
 } 

function adminArticle_validerAjoutLien(divLien, URLSite)
{
	var titreArt;
	var titreDoc;
	var lienPage;
	var lienExterne;
	var formatLienArt = URLSite+'/articles/';
	var formatLienDoc = URLSite+'/documents/';
	var selectArt = divLien.querySelector('#listeArt');
	var selectDoc = divLien.querySelector('#listeDoc');
	var selectPage = divLien.querySelector('#listePages');
	var inputExterne = divLien.querySelector('.lienExterne');
	if(selectArt && (selectArt.value != ''))
		titreArt = selectArt.value;
	if(selectDoc && (selectDoc.value != ''))
		titreDoc = selectDoc.value;
	if(selectPage && (selectPage.value != ''))
		lienPage = selectPage.value;
	if(inputExterne && (inputExterne.value != ''))
		lienExterne = inputExterne.value;
	

	var texteLienFac = divLien.querySelector('.ajouterTextLien');

	var articleHead = divLien.parentNode;
	var resumeHead = articleHead.querySelector('#article_resume');
	if(resumeHead)
	{
		if(titreArt)
		{
			if(texteLienFac.value != '')
			{
				resumeHead.innerHTML = replaceSelection(formatLienArt+titreArt+'"'+texteLienFac.value+'"','true',resumeHead);
				texteLienFac.value = '';
			}
			else
				resumeHead.innerHTML = replaceSelection(formatLienArt+titreArt,'true',resumeHead);
			selectArt.childNodes[1].value = '';
			divLien.innerHTML = '';
		}
		if(titreDoc)
		{
			if(texteLienFac.value != '')
			{
				resumeHead.innerHTML = replaceSelection(formatLienDoc+titreDoc+'"'+texteLienFac.value+'"','true',resumeHead);
				texteLienFac.value = '';
			}
			else
				resumeHead.innerHTML = replaceSelection(formatLienDoc+titreDoc,'true',resumeHead);
				
			selectDoc.childNodes[1].value = '';
		}
		if(lienExterne)
		{
			if(texteLienFac.value != '')
			{
				resumeHead.innerHTML = replaceSelection(lienExterne+'"'+texteLienFac.value+'"','true',resumeHead);
				texteLienFac.value = '';

			}
			else 
				resumeHead.innerHTML = replaceSelection(lienExterne,'true',resumeHead);
				
			inputExterne.value = '';
		}
		if(lienPage)
		{
			if(texteLienFac.value != '')
			{
				resumeHead.innerHTML = replaceSelection(URLSite+'/'+lienPage+'"'+texteLienFac.value+'"','true',resumeHead);
				texteLienFac.value = '';

			}
			else 
				resumeHead.innerHTML = replaceSelection(URLSite+'/'+lienPage,'true',resumeHead);
				
			lienPage.value = '';
		}
	}
	else
	{
		var textParagraphe = divLien.parentNode.querySelector('.texte');
		if(textParagraphe)
		{
		
			if(titreArt)
			{
				if(texteLienFac.value != '')
				{
					textParagraphe.innerHTML = replaceSelection(formatLienArt+titreArt+'"'+texteLienFac.value+'"','true',textParagraphe);
					texteLienFac.value = '';
				}
				else
					textParagraphe.innerHTML = replaceSelection(formatLienArt+titreArt,'true',textParagraphe);
				selectArt.childNodes[1].value = '';
			}
			if(titreDoc)
			{
				if(texteLienFac.value != '')
				{
					textParagraphe.innerHTML = replaceSelection(formatLienDoc+titreDoc+'"'+texteLienFac.value+'"','true',textParagraphe);
					texteLienFac.value = '';
				}
				else
					textParagraphe.innerHTML = replaceSelection(formatLienDoc+titreDoc,'true',textParagraphe);
					
				selectDoc.childNodes[1].value = '';
			}
			if(lienExterne)
			{
				if(texteLienFac.value != '')
				{
					textParagraphe.innerHTML = replaceSelection(lienExterne+'"'+texteLienFac.value+'"','true',textParagraphe);
					texteLienFac.value = '';

				}
				else 
					textParagraphe.innerHTML = replaceSelection(lienExterne,'true',textParagraphe);
					
				//inputLienExt.value = '';
			}
			if(lienPage)
			{
				if(texteLienFac.value != '')
				{
					textParagraphe.innerHTML = replaceSelection(URLSite+'/'+lienPage+'"'+texteLienFac.value+'"','true',textParagraphe);
					texteLienFac.value = '';

				}
				else 
					textParagraphe.innerHTML = replaceSelection(URLSite+'/'+lienPage,'true',textParagraphe);
					
				lienPage.value = '';
			}
		}
	}
	divLien.style.display = 'none';
}

function adminArticle_selectType(type,fenPar)
{
	var xhr = new XMLHttpRequest();
	var formData = new FormData();


	if(type)
		formData.append("selectType", type);


	formData.append("action", "selecttype");
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
									fenPar.innerHTML = xhr.responseText;
									var divA = fenPar.querySelector("#divCatArt");
									var divD = fenPar.querySelector("#divCatDoc");
									if(type == 'article')
										divA.style.display = 'block';
									if(type == 'document')
										divD.style.display = 'block';
									break;
								case 500:
									break;
								default:
									break;
							}
						}		
					}
					, false
				);
				xhr.open("POST", '/administration/adminArticle.php', true);
				xhr.setRequestHeader("Cache-Control", "no-cache");
				xhr.send(formData);
			}
	return false;
}

function adminArticle_choixCategorie(idCat, selectName, fenPar)
{
	var xhr = new XMLHttpRequest();
	var formData = new FormData();
	var Arts = fenPar.querySelector('#listeArt');
	var Docs = fenPar.querySelector('#listeDoc');

	if(selectName == 'categorieA')
		formData.append("selectCatIdA", idCat);
	else
		formData.append("selectCatIdD", idCat);
	formData.append("action", "selectcategorie");
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
								if(selectName == 'categorieA')
								{
									if(Arts)
									{
										Arts.innerHTML = xhr.responseText;
										if(idCat != '')
											Arts.parentNode.style.display = 'block';
										else
											Arts.parentNode.style.display = 'none';
									}
								}
								if(selectName == 'categorieD')
								{
									if(Docs)
									{
										Docs.innerHTML = xhr.responseText;
										if(idCat != '')
											Docs.parentNode.style.display = 'block';
										else
											Docs.parentNode.style.display = 'none';
									}
								}
								else
									break;
								case 500:
									break;
								default:
									break;
							}
						}		
					}
					, false
				);
				xhr.open("POST", '/administration/adminArticle.php', true);
				xhr.setRequestHeader("Cache-Control", "no-cache");
				xhr.send(formData);
			}
	return false;
}

function adminArticle_closeLien(fenPar)
{
	var fen;
	fen = document.getElementById('ajoutLienHead');
	if(fenPar)
		fenPar.style.display = "none";
	else
		fen.style.display = "none";	
}

function adminArticle_displayLien(fenPar)
{
	var xhr = new XMLHttpRequest();
	var formData = new FormData();
	
	if(fenPar)
	{
		var textarea = fenPar.querySelector('.texte');
		var selection = getSelection(textarea);
		if(selection)
			formData.append("selection", selection);

		var lienP = fenPar.querySelector('#ajoutLienParagraphe');
		lienP.innerHTML = '';
	}	
	else
	{
		var textarea = document.getElementById('article_resume');
		var selection = getSelection(textarea);
		if(selection)
			formData.append("selection", selection);

		var divLien = document.getElementById('ajoutLienHead');
		divLien.innerHTML = '';

	}
	formData.append("action", "selectionlien");
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
									if(fenPar)
									{
										lienP.innerHTML = xhr.responseText;
										lienP.style.display = 'block';
									}
									if(divLien)
									{
										divLien.innerHTML = xhr.responseText;
										divLien.style.display = 'block';
									}
									break;
								case 500:
									break;
								default:
									break;
							}
						}		
					}
					, false
				);
				xhr.open("POST", '/administration/adminArticle.php', true);
				xhr.setRequestHeader("Cache-Control", "no-cache");
				xhr.send(formData);
			}
	return false;
}

function adminArticle_updatePagePhotoEditor()
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
			main.style.height = (liste.offsetHeight) + 220 + 'px';
		if (restore)
			main.style.overflowY = overflow;
	}
}

function adminArticle_initPhotoEditor()
{
	window.onresize = adminArticle_updatePagePhotoEditor;
	adminArticle_updatePagePhotoEditor();
}

function adminArticle_detectIpad()
{
	var menuonline;
	var y;

	menuonline = document.getElementById("menuonline");
	if (navigator.userAgent.match(/iPad/i) != null)
	{
		clients_classListAdd(menuonline, "ipad");
		addEventListener('touchstart', inertialScrollYStart, false);
		addEventListener('touchmove', inertialScrollYScroll, false);
		addEventListener('touchstop', inertialScrollYStop, false);
		window.scroll(0, 0);
		function inertialScrollYStart(event)
		{
			var evt;

			if (event.touches && event.touches[0])
				evt = event.touches[0];
			else
				evt = event;
			client_inertialScrollY = 0;
			y = evt.pageY;
		}
		function inertialScrollYScroll(event)
		{
			var evt;
			
			if (event.touches && event.touches[0])
				evt = event.touches[0];
			else
				evt = event;
			client_inertialScrollY = evt.pageY - y;
			if (typeof carteClient_onScroll == 'function')
				carteClient_onScroll();
		}
		function inertialScrollYStop(event)
		{
			client_inertialScrollY = 0;
			y = 0;
		}
	}
}
