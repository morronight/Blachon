<?php 
	function selectCategorieArt($idCatArt, $selected = null)
		{
			$articles = Article::GetListe($idCatArt);
			foreach($articles as $article)
			{
				if($selected == null)
				{
					?><option value="<?php echo $article->GetLien(); ?>" /><?php echo $article->titre; ?></option><?php 
				}
				else
				{ 
					if($selected == $article->GetLien())
					{ 
						?><option selected="selected" value="<?php echo $article->GetLien(); ?>" /><?php echo $article->titre; ?></option><?php 
					}
					else
					{ 
						?><option value="<?php echo $article->GetLien(); ?>" /><?php echo $article->titre; ?></option><?php 
					}
				}
			}
		}
		
		function selectCategorieDoc($idCatDoc, $selected = null)
		{
			$documents = Document::Search($idCatDoc,false,false);
			foreach($documents as $document)
			{
				if($selected === null)
				{
					?><option value="<?php echo $document->GetLien(); ?>" /><?php echo $document->nom; ?></option><?php 
				}
				else
				{ 
					if($selected == $document->GetLien())
					{	
						?><option selected="selected" value="<?php echo $document->GetLien(); ?>" /><?php echo $document->nom; ?></option><?php 
					}
					else
					{ 
						?><option value="<?php echo $document->GetLien(); ?>" /><?php echo $document->nom; ?></option><?php
					}
				}
			} 
		}
		
		function selectPages($selected = null)
		{
			$pages = Page::GetListe();
			foreach($pages as $page)
			{
				if(($page->is_admin != 1) && ($page->short_path != null) && ($page->short_path != ''))
				{	
					if($selected == $page->short_path)
					{
						?><option selected="selected" value="<?php echo $page->short_path; ?>" /><?php echo $page->short_path; ?></option><?php 
					}
					else
					{ 
						?><option value="<?php echo $page->short_path; ?>" /><?php echo $page->short_path; ?></option><?php 
					}
				}
			}
		}
			
			function listeCatsDocs()
			{
				$documents = Document::GetCategoriesNonVide();
				$idsDoc = array();
				$i = 0;
				foreach($documents as $catsD)
				{
					$idsDoc[$i] = $catsD->id_categorie;
					$i++;
				}
				$categoriesDoc = Categorie::GetListe($idsDoc);
				return $categoriesDoc;
			}
			
			function listeCatsArts()
			{
				$listeCat = Categorisation::GetListeNonVide();
				$idsArt = array();
				$t = 0;
				foreach($listeCat as $catsA)
				{
					$idsArt[$t] = $catsA->id_categorie;
					$t++;
				}
				$categoriesArt = Categorie::GetListe($idsArt);
				return $categoriesArt;
			}
			
			
	function affTypeLien($selected = null)
	{
		?>
			<span class="liens"><label style="font-weight:bold;">Inserer un lien :</label></span>
			<div id="typeLienHead" class="liens">
				<label>Creer un lien vers : </label>
				<select class="selectType" name="selectTypeLien" onchange="adminArticle_selectType(this.value,this.parentNode.parentNode)">
					<?php
						$selectedVal = '';
						if (isset($selected))
						{
							switch($selected)
							{
								case 'art':
									$selectedVal = 'article';
									break;
								case 'doc':
									$selectedVal = 'document';
									break;
								default:
									$selectedVal = $selected;
									break;
							}
						}
						$options = array('rien' => '', 'document' => 'Document', 'article' => 'Article', 'page' => 'Page', 'autre' => 'Autre');
						foreach ($options as $value => $option)
						{
							$sel = '';
							if ($selectedVal == $value)
								$sel = ' selected="selected"';
							echo '<option'.$sel.' value="'.$value.'">'.$option.'</option>';
						}
					?>
				</select>
			</div>
		<?php
	}
	
	function affTextLien($libelle = null,$texte = null)
	{
		?>
			<div id="textLien" class="liens" >
				<label>En affichant le texte : </label>
				<?php 
				if($libelle === null)
				{
					?><input type="text" class="ajouterTextLien" placeholder=<?php echo $texte; ?> /><?php
				}
				else
				{
					?><input type="text" class="ajouterTextLien" value=<?php echo $libelle; ?> /><?php
				}   ?>
			</div>
	<?php
	}
	
	function affListeCatArt($catId = null)
	{
		?>
		<div class="liens" id="divCatArt" >
			<label>Dans la categorie : </label>
			<select class="catA" name="categorieA" onchange="adminArticle_choixCategorie(this.value, this.name, this.parentNode.parentNode.parentNode)" />
				<option selected="selected" value=""></option>
					<?php 
					$categoriesA = listeCatsArts();
					foreach($categoriesA as $categorieA)
					{
						if($catId != null && $catId != "")
						{
							$ids = explode(",", $catId);
							if($categorieA->id == $ids[0])
							{
								?><option selected="selected" value="<?php echo $categorieA->id; ?>" /><?php echo $categorieA->nom; ?></option><?php 
							}
							else
							{
								?><option value="<?php echo $categorieA->id; ?>" /><?php echo $categorieA->nom; ?></option><?php 
							}
						} 
						else
						{
							?><option value="<?php echo $categorieA->id; ?>" /><?php echo $categorieA->nom; ?></option><?php 
						}
					} ?>
			</select>
		</div>
	<?php
	}
	
	function affListeArt($texte = null)
	{
		?>
		<div class="liens" style="display:none" id="divArticle">
			<label>Vers l'article : </label>
			<select class="selectArts" id="listeArt" />
				<option value=""></option>
			</select>
			<?php 
			if($texte === null)
				affTextLien(null,"Article"); 
			else
				affTextLien($texte); ?>
		</div>
	<?php
	
	}
	
		function affListeArtId($texte = null, $catId = null, $selected = null)
	{
			if($catId != null && $catId != "")
				$ids = explode(",", $catId); ?>
			<div class="liens" id="divArticle">
			<label>Vers l'article : </label>
				<select class="selectArts" id="listeArt" />
					<option value=""></option>
						<?php selectCategorieArt($ids[0],$selected); ?>
				</select>
			<?php 
			if($texte === null)
				affTextLien(null,"Article"); 
			else
				affTextLien($texte); ?>
			</div>
	<?php
	}
	
	function affListeCatDoc($catId = null)
	{
		?>
		<div class="liens" id="divCatDoc" >
			<label>Dans la categorie : </label>
			<select class="catD" name="categorieD" onchange="adminArticle_choixCategorie(this.value,this.name, this.parentNode.parentNode.parentNode)" />
				<option selected="selected" value=""></option>
				<?php 
				$categoriesD = listeCatsDocs();
				foreach($categoriesD as $categorieD)
				{
					if($catId != null && $catId != "")
					{
						$ids = explode(",", $catId);
						if($categorieD->id == $ids[0])
						{
							?><option selected="selected" value="<?php echo $categorieD->id; ?>" /><?php echo $categorieD->nom; ?></option><?php
						}
						else
						{
						 
							?><option value="<?php echo $categorieD->id; ?>" /><?php echo $categorieD->nom; ?></option><?php
						}
					} 
					else
					{
							?><option value="<?php echo $categorieD->id; ?>" /><?php echo $categorieD->nom; ?></option><?php
					}
				} ?>
			</select>
		</div>
	<?php
	}
	function affListeDoc($texte = null)
	{
		?>
		<div class="liens" style="display:none" id="divDocument">
			<label>Vers le document : </label>
			<select class="selectDocs" id="listeDoc" />
				<option value=""></option>
			</select>
			<?php
			if($texte === null)
				affTextLien(null,"Document");
			else
				affTextLien($texte); ?>
		</div>
	<?php

	}
	
	function affListeDocId($texte = null, $catId = null, $selected = null)
	{	
		if($catId != null && $catId != "")
				$ids = explode(",", $catId);	?>
		<div class="liens" id="divDocument">
			<label>Vers le document : </label>
			<select class="selectDocs" id="listeDoc" />
				<option value=""></option>
					<?php selectCategorieDoc($ids[0],$selected); ?>
			</select>
			<?php
			if($texte === null)
				affTextLien(null,"Document");
			else
				affTextLien($texte); ?>
		</div>
		<?php

	}
	
	function affLienInterne($libelle = null, $texte = null)
	{
		?>
		<div class="liens" >
			<label>Vers la page : </label>
			<select class="pages" name="pages" id="listePages"/><?php
			if($libelle===null)
			{ 
				?><option value=""></option><?php
			}
				selectPages($libelle);
			?>
			</select>
			<?php 
			if($texte == null)
				affTextLien(null,"Titre"); 
			else
				affTextLien($texte); 
			?>
		</div>
		<?php
	}
	
	function affLienExterne($libelle = null, $texte = null)
	{
		?>
		<div class="liens" >
			<label>Vers : </label><?php 
			if($libelle === null)
			{
			
				?><input type="text" class="lienExterne" /><?php 
			}
			else
			{
				?><input type="text" class="lienExterne" value= '<?php echo $libelle; ?>'"/><?php
			} 
			if($texte === null)
				affTextLien(null,"Titre");
			else	
				affTextLien($texte);?>
		</div>
		<?php
	}
	
	
	
	function affOutils()
	{
		?>
			<div class="valider">
				<span class="icone valider" name="valider" value="Ajouter le lien" onclick="return adminArticle_validerAjoutLien(this.parentNode.parentNode, '<?php echo Configuration::$Url; ?>');"/>
			</div>
		<?php
	}
	
	function affCommandes()
	{
		?>
			<div class="commandes">
				<span class="icone close" title="Fermer" onclick="return adminArticle_closeLien(this.parentNode.parentNode);"></span>
			</div>
		<?php 
	}
	
	function affFctsLien($selected = null, $libelle = null, $texte = null, $catsId = null, $nomPage = null)
	{
		affCommandes();
		affTypeLien($selected);
		if (isset($selected))
		{
			switch($selected)
			{
			case 'art':
				affListeCatArt($catsId);
				if($catsId === null || $catsId == '')
					affListeArt($texte);
				else
					affListeArtId($texte,$catsId,$nomPage);
				break;
			case 'doc':
				affListeCatDoc($catsId);
				if($catsId === null || $catsId == '')
					affListeDoc($texte);
				else
					affListeDocId($texte,$catsId,$nomPage);
				break;
			case 'page':
				affLienInterne($libelle,$texte);
				break;
			case 'autre':
				affLienExterne($libelle,$texte);
				break;
			}
		}
		affOutils();
	}
?>