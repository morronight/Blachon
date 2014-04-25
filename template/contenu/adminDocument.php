<?php
	require_once 'include/camsii/Document.php';
	require_once 'include/camsii/Categorie.php';
	if (!isset($theDocument) && isset($request) && isset($request['document']))
		$theDocument = Document::Get(intval($request['document']));
	if (isset($theDocument) && (($theDocument === null) || ($theDocument === false)))
		$theDocument = new Document();
?>
<script type="text/javascript" src="/Scripts/adminDocument.js"></script>
<article id="contenu" class="article">
<aside id="categories">
	<h3>Choisir les catégories du document</h3>
	<ul>
		<?php
			require_once 'include/camsii/Categorie.php';

			$categories = null;
			if ((!isset($theDocument) || ($theDocument->id === null)) && isset($request) && isset($request['categories']))
				$categories = explode(',', $request['categories']);
			$sortedCategories = Categorie::GetSortedHierachicalListe();
			foreach($sortedCategories as $nom => $categorie)
			{
				if ((strval(intval($nom)) != $nom) && ($nom != ''))
				{
					$classname = '';
					if (isset($theDocument) && $theDocument->hasCategorie($categorie->id) || (($categories !== null) && (in_array($categorie->id, $categories))))
						$classname = ' class="selected"';
					echo '<li'.$classname.' id="categorie_'.$categorie->id.'" onclick="adminDocument_selectCategorie(this, \'categorie\', \''.$categorie->ids.'\', \''.$categorie->sids.'\')" title="'.$nom.'">'.$categorie->nom.'</li>';
				}
			}
		?>
	</ul>
</aside>
	<?php
		include 'adminDocumentContenu.php';
	?>
</article>
