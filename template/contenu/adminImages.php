<?php
	require_once 'include/camsii/Categorie.php';

	function afficheFiltre($idCategories = null, $brouillons = false, $archives = false, $filtre = null)
	{
		$categories = Categorie::GetListe();
		echo '<div class="filtres"><h3>Filtres</h3>';
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
		echo '<div id="newArticle"><a href="/administration/image" onclick="return adminImages_nouvelleImage(this);">Nouvelle image/photo</a></div>';
		echo '</div></div>';
	}
	
?>
<article id="contenu" class="articles">
	<div id="breadcrumb">
		<a href="/administration"><span class="icone adminHome" title="Accueil administration"></span> Administration</a> &gt; Liste des images
	</div>
	<?php
		include("template/template_image.php");
		$categories = null;
		if (!isset($theImages) && isset($request) && isset($request['categories']))
		{
			$categories = explode(',', $request['categories']);
			$theImages = Image::Search($categories);
		}
		afficheFiltre($categories);
		if (!isset($theImages))
			$theImages = afficheImagesAdmin();
		else
			afficheImagesAdmin($theImages);
	?>
</article>