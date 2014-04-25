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
				echo '<span id="categorie_'.intval($categorie->id).'" class="selected" onclick="adminVideos_update(this)">'.$categorie->nom.'</span>';
			else
				echo '<span id="categorie_'.intval($categorie->id).'" onclick="adminVideos_update(this)">'.$categorie->nom.'</span>';
		}
		echo '<input type="button" value="Toutes" onclick="adminVideos_selectCategories(1)"/>';
		echo '<input type="button" value="Aucune" onclick="adminVideos_selectCategories(0)"/>';
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
		echo '<div>Recherche <input type="text" id="seach" name="seach" value="'.$searchClause.'" onchange="adminVideos_update()" onkeyup="adminVideos_update()"/><br>';
		echo '<input type="checkbox" id="brouillons" name="brouillons"'.$brouillonsClause.' onchange="adminVideos_update()"/>Brouillons';
		echo '<input type="checkbox" id="archives" name="archives"'.$archivesClause.' onchange="adminVideos_update()"/>Archives';
		echo '<input type="checkbox" id="masques" name="masques"'.$masquesClause.' onchange="adminVideos_update()"/>Masquées';
		echo '<div id="newArticle"><a href="/administration/video" onclick="return adminVideos_nouvelleVideo(this);">Nouvelle video</a></div>';
		echo '</div></div>';
	}
	
?>
<article id="contenu" class="articles">
	<div id="breadcrumb">
		<a href="/administration"><span class="icone adminHome" title="Accueil administration"></span> Administration</a> &gt; Liste des vidéos
	</div>
	<?php
		include("template/template_video.php");
		$categories = null;
		if (!isset($theVideos) && isset($request) && isset($request['categories']) && ($request['categories'] != ''))
		{
			$categories = explode(',', $request['categories']);
			$theVideos = Video::Search($categories, false, false, null, true);
		}
		afficheFiltre($categories);
		if (!isset($theVideos))
			$theVideos = afficheVideosAdmin(null, null, false, false, null, true);
		else
			afficheVideosAdmin($theVideos, null, false, false, null, true);
	?>
</article>