<?php
	require_once 'include/camsii/Galerie.php';
	function afficheGaleriesAdmin($galeries = null, $idCategories = null, $brouillons = false, $archives = false, $filtre = null, $visible = true)
	{
		if ($galeries === null)
			$galeries = Galerie::Search($idCategories, $brouillons, $archives, $filtre, $visible);
		if ($galeries !== null)
		{
			echo '<div id="liste" class="liste">';
			if (count($galeries) > 0)
			{
				foreach ($galeries as $galerie)
				{
					if (is_a($galerie, 'Galerie'))
					{
						echo '<div class="listeGaleries">';
						$images = $galerie->GetImages();
						if (($images !== null) && (count($images) > 0))
						{
							$count = 0;
							foreach ($images as $image)
							{
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
								echo '<img src="/Images/I'.intval($image->id).'_'.$width.'_'.$height.'"/>';
								$count++;
								if ($count >= 3)
									break;
							}
						}
						$titre = htmlentities($galerie->nom, ENT_NOQUOTES, 'UTF-8');
						echo '<p><a href="/administration/galerie?galerie='.intval($galerie->id).'">'.$titre.'</a></p>';
						echo '</div>';
					}
				}
				echo '<div class="sectionBottom"></div>';
			}
			else
			{
				if ($idCategories === null)
					echo '<span class="message">Aucune galerie trouvée. Sélectionnez une catégorie.</span>';
				else
					echo '<span class="message">Aucune galerie trouvée.</span>';
			}
			echo '</div>';
		}
	}
	
	function afficheGaleries($galeries = null, $idCategories = null, $filtre = null)
	{
		if ($galeries === null)
		{
			if ($idCategories === null)
				$galeries = Galerie::GetListe(null, true);
			else
				$galeries = Galerie::Search($idCategories, false, false, $filtre);
		}
		if ($galeries !== null)
		{
			echo '<div id="liste" class="liste">';
			if (count($galeries) > 0)
			{
				foreach ($galeries as $galerie)
				{
					if (is_a($galerie, 'Galerie'))
					{
						echo '<div class="listeGaleries">';
						$titre = htmlentities($galerie->nom, ENT_NOQUOTES, 'UTF-8');
						echo '<h2>'.$titre.'</h2><a href="/galeries/'.Formatage::Lien($galerie->nom).'">';
						$images = $galerie->GetImages();
						if (($images !== null) && (count($images) > 0))
						{
							$count = 0;
							foreach ($images as $image)
							{
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
								echo '<span><img src="/Images/I'.intval($image->id).'_'.$width.'_'.$height.'"/></span>';
								$count++;
								if ($count >= 3)
									break;
							}
						}
						echo '</a></div>';
					}
				}
				echo '<div class="sectionBottom"></div>';
			}
			else
				echo 'Aucune galerie trouvée';
			echo '</div>';
		}
	}
	
	function afficheGalerie($galerie)
	{
		if (is_a($galerie, 'Galerie'))
		{
			$titre = htmlentities($galerie->nom, ENT_NOQUOTES, 'UTF-8');
			echo '<h1>'.$titre.'</h1>';
			$images = $galerie->GetImages();
			if (($images !== null) && (count($images) > 0))
			{
				foreach ($images as $image)
				{
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
					$legende = '';
					if ($image->legende !== null)
						$legende = htmlentities($image->legende, ENT_NOQUOTES, 'UTF-8');
					echo '<span>';
					echo '<img id="image_'.intval($image->id).'" src="'.Configuration::$Static['url'].'/Images/I'.intval($image->id).'_'.$width.'_'.$height.'" alt="'.$legende.'" title="'.$legende.'" onclick="return galerie_selectPhoto('.intval($image->id).')"/>';
					echo '</span>';
				}
			}
		}
	}
	
	function afficheGaleriesAdminArticle($galeries = null, $idCategories = null, $brouillons = false, $archives = false, $filtre = null)
	{
		if ($galeries === null)
			$galeries = Galerie::Search($idCategories, $brouillons, $archives, $filtre);
		if ($galeries !== null)
		{
			echo '<div class="commandes">';
			afficheFiltreAdminGalerie($idCategories);
			echo '<div id="iconeClose">';
			echo '<span class="icone close" title="Fermer" onclick="return adminArticle_closeSelectionGalerie();"></span>';
			echo '</div>';
			echo '</div>';
			echo '<div id="liste_galerie" class="liste">';
			foreach ($galeries as $galerie)
			{
				if (is_a($galerie, 'Galerie'))
				{
					$ids = array();
					$images = $galerie->GetImages();
					if (($images !== null) && (count($images) > 0))
					{
						foreach ($images as $image)
							$ids[] = intval($image->id);
					}
					echo '<div class="listeGaleries" onclick="adminArticle_useGalerie('.intval($galerie->id).', \''.implode(',', $ids).'\')">';
					$titre = htmlentities($galerie->nom, ENT_NOQUOTES, 'UTF-8');
					echo '<h2>'.$titre.' <span>'.$galerie->GetDatePublication().'</span></h2>';
					if (($images !== null) && (count($images) > 0))
					{
						$count = 0;
						foreach ($images as $image)
						{
							$width = 150;
							$filepath = $image->GetFilePath();
							if (is_file($filepath))
							{
								list($w, $h) = getimagesize($filepath);
								$height = intval(1. * $h * $width / $w);
								if ($height > 150)
								{
									$height = 150;
									$width = intval(1.* $w * $height / $h);
								}
							}
							echo '<img src="/Images/I'.intval($image->id).'_'.$width.'_'.$height.'"/>';
							$count++;
							if ($count >= 3)
								break;
						}
					}
					echo '<div class="sectionBottom"></div></div>';
				}
			}
			echo '</div>'.PHP_EOL;
		}
	}
	
	function afficheImagesGalerie($images = null, $idCategories = null, $brouillons = false, $archives = false, $filtre = null)
	{
		if ($images === null)
			$images = Image::Search($idCategories, $brouillons, $archives, $filtre);
		if ($images !== null)
		{
			?>
				<div id="liste_photo" class="liste">
				<div class="commandes">
					<span class="icone close" title="Fermer" onclick="return adminGalerie_closeSelectionImages();"></span>
				</div>
			<?php
			if (count($images) > 0)
			{
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
							echo '<img src="'.Configuration::$Static['url'].'/Images/I'.intval($image->id).'_'.$width.'_'.$height.'" alt="'.$titre.'" onclick="adminGalerie_selectPhoto('.intval($image->id).', '.intval($w).', '.intval($h).')"/>';
						}
						echo '</div>';
					}
				}
			}
			else
			{
				echo 'Aucune image/photo pour ces critères.';
				if ($idCategories === null)
					echo ' Choisisez au moins une catégorie.';
			}
			echo '</div>'.PHP_EOL;
		}
	}

	
	function afficheFiltreAdminGalerie($idCategories = null, $brouillons = false, $archives = false, $filtre = null, $masques = false)
	{
		$categories = Categorie::GetListe();
		echo '<div class="filtres">';
		echo '<div id="categories" class="categories">';
		foreach ($categories as $categorie)
		{
			if (($idCategories !== null) && (in_array($categorie->id, $idCategories)))
				echo '<span id="categorie_'.intval($categorie->id).'" class="selected" onclick="adminGaleries_update(this)">'.$categorie->nom.'</span>';
			else
				echo '<span id="categorie_'.intval($categorie->id).'" onclick="adminGaleries_update(this)">'.$categorie->nom.'</span>';
		}
		echo '<input type="button" value="Toutes" onclick="adminGaleries_selectCategories(1)"/>';
		echo '<input type="button" value="Aucune" onclick="adminGaleries_selectCategories(0)"/>';
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
		$masquesClause = '';
		if ($masques === true)
			$masquesClause = ' checked="checked"';
		echo '<div>Recherche <input type="text" id="seach" name="seach" value="'.$searchClause.'" onchange="adminGaleries_update()" onkeyup="adminGaleries_update()"/><br>';
		echo '<input type="checkbox" id="brouillons" name="brouillons"'.$brouillonsClause.' onchange="adminGaleries_update()"/>Brouillons';
		echo '<input type="checkbox" id="archives" name="archives"'.$archivesClause.' onchange="adminGaleries_update()"/>Archives';
		echo '<input type="checkbox" id="masques" name="masques"'.$masquesClause.' onchange="adminGaleries_update()"/>Masquées';
		echo '</div></div>';
	}
?>