<?php
	require_once 'include/camsii/Image.php';
	require_once 'include/camsii/Categorie.php';
	if (!isset($theImage) && isset($request) && isset($request['image']))
		$theImage = Image::Get(intval($request['image']));
	if (isset($theImage) && (($theImage === null) || ($theImage === false)))
		$theImage = new Image();
?>
<script type="text/javascript" src="/Scripts/adminImage.js"></script>
<article id="contenu" class="article">
<aside id="categories">
	<h3>Choisir les catégories du image</h3>
	<ul>
		<?php
			require_once 'include/camsii/Categorie.php';

			$categories = null;
			if ((!isset($theImage) || ($theImage->id === null)) && isset($request) && isset($request['categories']))
				$categories = explode(',', $request['categories']);
			$sortedCategories = Categorie::GetSortedHierachicalListe();
			foreach($sortedCategories as $nom => $categorie)
			{
				if ((strval(intval($nom)) != $nom) && ($nom != ''))
				{
					$classname = '';
					if (isset($theImage) && $theImage->hasCategorie($categorie->id) || (($categories !== null) && (in_array($categorie->id, $categories))))
						$classname = ' class="selected"';
					echo '<li'.$classname.' id="categorie_'.$categorie->id.'" onclick="adminImage_selectCategorie(this, \'categorie\', \''.$categorie->ids.'\', \''.$categorie->sids.'\')" title="'.$nom.'">'.$categorie->nom.'</li>';
				}
			}
		?>
	</ul>
	<div id="newArticle"><a href="/administration/image" onclick="return adminImage_nouvelleImage(this);">Nouvelle image/photo</a></div>
</aside>
	<?php
		include 'adminImageContenu.php';
	?>
</article>
