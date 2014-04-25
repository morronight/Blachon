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
				echo '<span id="categorie_'.intval($categorie->id).'" class="selected" onclick="adminArticles_update(this)">'.$categorie->nom.'</span>';
			else
				echo '<span id="categorie_'.intval($categorie->id).'" onclick="adminArticles_update(this)">'.$categorie->nom.'</span>';
		}
		echo '<input type="button" value="Toutes" onclick="adminArticles_selectCategories(1)"/>';
		echo '<input type="button" value="Aucune" onclick="adminArticles_selectCategories(0)"/>';
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
		echo '<div>Recherche <input type="text" id="seach" name="seach" value="'.$searchClause.'" onchange="adminArticles_update()" onkeyup="adminArticles_update()"/><br>';
		echo '<input type="checkbox" id="brouillons" name="brouillons"'.$brouillonsClause.' onchange="adminArticles_update()"/>Brouillons';
		echo '<input type="checkbox" id="archives" name="archives"'.$archivesClause.' onchange="adminArticles_update()"/>Archives';
		echo '<div id="newArticle"><a href="/administration/article" onclick="return adminArticles_nouvelArticle(this);">Créer un article</a></div>';
		echo '</div></div>';
	}
	
?>
<article id="contenu" class="articles">
	<div id="breadcrumb">
		<a href="/administration"><span class="icone adminHome" title="Accueil administration"></span> Administration</a> &gt; Liste des articles
	</div>
	<?php
		include("template/template_general.php");
		$categories = null;
		if (isset($request) && isset($request['categories']))
			$categories = explode(',', $request['categories']);
		afficheFiltre($categories);
		if (!isset($theArticles))
			$theArticles = afficheArticlesAdmin(null, $categories);
		else
			afficheArticlesAdmin($theArticles);
	?>
	</div>
</article>