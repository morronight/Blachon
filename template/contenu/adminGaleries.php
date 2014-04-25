<?php
	require_once 'include/camsii/Categorie.php';

	function afficheFiltre($idCategories = null, $brouillons = false, $archives = false, $filtre = null, $masques = false)
	{
		$categories = Categorie::GetListe();
		echo '<div class="filtres"><h3>Filtres</h3>';
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
		echo '<div id="newArticle"><a href="/administration/galerie" onclick="return adminGaleries_nouvelleGalerie(this);">Nouvelle galerie</a></div>';
		echo '</div></div>';
	}
?>
<article id="contenu" class="articles">
	<div id="breadcrumb">
		<a href="/administration"><span class="icone adminHome" title="Accueil administration"></span> Administration</a> &gt; Liste des galeries
	</div>
	<?php
		include("template/template_galerie.php");
		$categories = null;
		if (!isset($theGaleries) && isset($request) && isset($request['categories']) && ($request['categories'] != ''))
		{
			$categories = explode(',', $request['categories']);
			$theGaleries = Galerie::Search($categories, false, false, null, true);
		}
		afficheFiltre($categories);
		if (!isset($theGaleries))
			$theGaleries = afficheGaleriesAdmin(null, null, false, false, null, true);
		else
			afficheGaleriesAdmin($theGaleries, null, false, false, null, true);
	?>
</article>