<?php
	require_once 'include/camsii/Image.php';
	require_once 'include/camsii/Categorie.php';

	function afficheImagesAdmin($images = null, $idCategories = null, $brouillons = false, $archives = false, $filtre = null)
	{
		if ($images === null)
			$images = Image::Search($idCategories, $brouillons, $archives, $filtre);
		if ($images !== null)
		{
			echo '<div id="liste" class="liste">';
			if (count($images) > 0)
			{
				foreach ($images as $image)
				{
					if (is_a($image, 'Image'))
					{
						echo '<div class="listeImages">';
						$width = 200;						
						$filepath = $image->GetFilePath();
						if (is_file($filepath))
						{
							list($w, $h) = getimagesize($filepath);
							$height = intval(1. * $h * $width / $w);
							if ($height > 200)
							{
								$height = 200;
								$width = intval(1.* $w * $height / $h);
							}
						}
						if (($image->legende !== null) && ($image->legende != ''))
						{
							$titre = htmlentities($image->legende, ENT_NOQUOTES, 'UTF-8');
							echo '<img src="'.Configuration::$Static['url'].'/Images/I'.intval($image->id).'_'.$width.'_'.$height.'" alt="'.$titre.'" title="'.$titre.'"/>';
							echo '<p><a href="/administration/image?image='.intval($image->id).'">'.$titre.'</a></p>';
						}
						else
						{
							$titre = '';
							echo '<img src="'.Configuration::$Static['url'].'/Images/I'.intval($image->id).'_'.$width.'_'.$height.'"/>';
							echo '<p><a href="/administration/image?image='.intval($image->id).'"><i>Modifier</i></a></p>';
						}
						echo '</div>';
					}
				}
				echo '<div class="sectionBottom"></div>';
			}
			else
			{
				if ($idCategories === null)
					echo '<span class="message">Aucune image trouvé. Sélectionnez une catégorie.</span>';
				else
					echo '<span class="message">Aucune image trouvé.</span>';
			}
			echo '</div>';
		}
	}
	
	function afficheImagesAdminArticle($images = null, $idCategories = null, $brouillons = false, $archives = false, $filtre = null)
	{
		if ($images === null)
			$images = Image::Search($idCategories, $brouillons, $archives, $filtre);
		if ($images !== null)
		{
			echo '<div class="commandes">';
			afficheFiltreAdminArticle($idCategories);
			echo '<div id="iconeClose">';
			echo '<span class="icone close" title="Fermer" onclick="return adminArticle_closeSelectionImages();"></span>';
			echo '</div>';
			echo '</div>';
			echo '<div id="liste" class="liste">';
			foreach ($images as $image)
			{
				if (is_a($image, 'Image'))
				{
					echo '<div class="listePhotos">';
					$titre = htmlentities($image->legende, ENT_NOQUOTES, 'UTF-8');
					echo '<h2>'.$titre.' <span>'.$image->GetDatePublication().'</span></h2>';
					$filepath = $image->GetFilePath();
					if (is_file($filepath))
					{
						$width = 150;
						list($w, $h) = getimagesize($filepath);
						$height = intval(1. * $h * $width / $w);
						if ($height > 150)
						{
							$height = 150;
							$width = intval(1.* $w * $height / $h);
						}
						echo '<img src="'.Configuration::$Static['url'].'/Images/I'.intval($image->id).'_'.$width.'_'.$height.'" alt="'.$titre.'" onclick="adminArticle_usePhoto('.intval($image->id).', '.intval($w).', '.intval($h).')"/>';
					}
					echo '<div class="sectionBottom"></div></div>';
				}
			}
			echo '</div>'.PHP_EOL;
		}
	}
	
	function afficheFiltreAdmin($idCategories = null, $brouillons = false, $archives = false, $filtre = null)
	{
		$categories = Categorie::GetListe();
		echo '<div class="filtres">';
		echo '<div id="categories" class="categories">';
		foreach ($categories as $categorie)
		{
			if (($idCategories !== null) && (in_array($categorie->id, $idCategories)))
				echo '<span id="categorie_'.intval($categorie->id).'" class="selected" onclick="adminImages_update(this)">'.$categorie->nom.'</span>';
			else
				echo '<span id="categorie_'.intval($categorie->id).'" onclick="adminImages_update(this)">'.$categorie->nom.'</span>';
		}
		echo '<input type="button" value="Toutes" onclick="adminImages_selectCategories(1)"/>';
		echo '<input type="button" value="Aucune" onclick="adminImages_selectCategories(0)"/>';
		echo '</div>';
		$brouillonsClause = '';
		if ($brouillons === true)
			$brouillonsClause = ' checked="checked"';
		$archivesClause = '';
		if ($archives === true)
			$archivesClause = ' checked="checked"';
		$searchClause = '';
		if ($filtre !== null)
			$searchClause = htmlentities($filtre, ENT_COMPAT, 'utf8');
		echo '<div>Recherche <input type="text" id="seach" name="seach" value="'.$searchClause.'" onchange="adminImages_update()" onkeyup="adminImages_update()"/><br>';
		echo '<input type="checkbox" id="brouillons" name="brouillons"'.$brouillonsClause.' onchange="adminImages_update()"/>Brouillons';
		echo '<input type="checkbox" id="archives" name="archives"'.$archivesClause.' onchange="adminImages_update()"/>Archives';
		echo '</div></div>';
	}
	
	function afficheFiltreAdminArticle($idCategories = null, $brouillons = false, $archives = false, $filtre = null)
	{
		$categories = Categorie::GetListe();
		echo '<div class="filtres">';
		echo '<div id="categories" class="categories">';
		foreach ($categories as $categorie)
		{
			if (($idCategories !== null) && (in_array($categorie->id, $idCategories)))
				echo '<span id="categorie_'.intval($categorie->id).'" class="selected" onclick="adminArticle_ImageUpdate(this)">'.$categorie->nom.'</span>';
			else
				echo '<span id="categorie_'.intval($categorie->id).'" onclick="adminArticle_ImageUpdate(this)">'.$categorie->nom.'</span>';
		}
		echo '<input type="button" value="Toutes" onclick="adminArticle_selectCategoriesImages(1)"/>';
		echo '<input type="button" value="Aucune" onclick="adminArticle_selectCategoriesImages(0)"/>';
		echo '</div>';
		$brouillonsClause = '';
		if ($brouillons === true)
			$brouillonsClause = ' checked="checked"';
		$archivesClause = '';
		if ($archives === true)
			$archivesClause = ' checked="checked"';
		$searchClause = '';
		if ($filtre !== null)
			$searchClause = htmlentities($filtre, ENT_COMPAT, 'utf8');
		echo '<div>Recherche <input type="text" id="seach" name="seach" value="'.$searchClause.'" onchange="adminArticle_ImageUpdate()" onkeyup="adminArticle_ImageUpdate()"/><br>';
		echo '<input type="checkbox" id="brouillons" name="brouillons"'.$brouillonsClause.' onchange="adminArticle_ImageUpdate()"/>Brouillons';
		echo '<input type="checkbox" id="archives" name="archives"'.$archivesClause.' onchange="adminArticle_ImageUpdate()"/>Archives';
		echo '</div></div>';
	}
	
	function afficheImagesAdminArticleSF($images = null, $idCategories = null, $brouillons = false, $archives = false, $filtre = null)
	{
		if ($images === null)
			$images = Image::Search($idCategories, $brouillons, $archives, $filtre);
		if ($images !== null)
		{
			echo '<div id="liste" class="liste">';
			foreach ($images as $image)
			{
				if (is_a($image, 'Image'))
				{
					echo '<div class="listePhotos">';
					$titre = htmlentities($image->legende, ENT_NOQUOTES, 'UTF-8');
					echo '<h2>'.$titre.' <span>'.$image->GetDatePublication().'</span></h2>';
					$filepath = $image->GetFilePath();
					if (is_file($filepath))
					{
						$width = 150;
						list($w, $h) = getimagesize($filepath);
						$height = intval(1. * $h * $width / $w);
						if ($height > 150)
						{
							$height = 150;
							$width = intval(1.* $w * $height / $h);
						}
						echo '<img src="'.Configuration::$Static['url'].'/Images/I'.intval($image->id).'_'.$width.'_'.$height.'" alt="'.$titre.'" onclick="adminArticle_usePhoto('.intval($image->id).', '.intval($w).', '.intval($h).')"/>';
					}
					echo '<div class="sectionBottom"></div></div>';
				}
			}
			echo '</div>'.PHP_EOL;
		}
	}
?>