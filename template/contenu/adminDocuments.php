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
				echo '<span id="categorie_'.intval($categorie->id).'" class="selected" onclick="adminDocuments_update(this)">'.$categorie->nom.'</span>';
			else
				echo '<span id="categorie_'.intval($categorie->id).'" onclick="adminDocuments_update(this)">'.$categorie->nom.'</span>';
		}
		echo '<input type="button" value="Toutes" onclick="adminDocuments_selectCategories(1)"/>';
		echo '<input type="button" value="Aucune" onclick="adminDocuments_selectCategories(0)"/>';
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
		echo '<div>Recherche <input type="text" id="seach" name="seach" value="'.$searchClause.'" onchange="adminDocuments_update()" onkeyup="adminDocuments_update()"/><br>';
		echo '<input type="checkbox" id="brouillons" name="brouillons"'.$brouillonsClause.' onchange="adminDocuments_update()"/>Brouillons';
		echo '<input type="checkbox" id="archives" name="archives"'.$archivesClause.' onchange="adminDocuments_update()"/>Archives';
		echo '<div id="newArticle"><a href="/administration/document" onclick="return adminDocuments_nouveauDocument(this);">Nouveau document</a></div>';
		echo '</div></div>';
	}
?>
<article id="contenu" class="articles">
	<div id="breadcrumb">
		<a href="/administration"><span class="icone adminHome" title="Accueil administration"></span> Administration</a> &gt; Liste des documents
	</div>
	<?php
		include("template/template_document.php");
		include("include/camsii/Document.php");
		$categories = null;
		if (!isset($theDocuments) && isset($request) && isset($request['categories']))
		{
			$categories = explode(',', $request['categories']);
			$theDocuments = Document::Search($categories);
		}
		afficheFiltre($categories);
		if (!isset($theDocuments))
			$theDocuments = afficheDocumentsAdmin();
		else
			afficheDocumentsAdmin($theDocuments);
	?>
</article>