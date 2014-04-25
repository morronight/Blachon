<?php
	require_once 'include/camsii/Galerie.php';
	require_once 'include/camsii/Categorie.php';
	if (!isset($theGalerie) && isset($request) && isset($request['galerie']))
		$theGalerie = Galerie::Get(intval($request['galerie']));
	if (isset($theGalerie) && (($theGalerie === null) || ($theGalerie === false)))
		$theGalerie = new Galerie();
?>
<script type="text/javascript" src="/Scripts/adminGalerie.js"></script>
<article id="contenu" class="article">
	<aside id="categories">
	<h3>Choisir les catégories auxquelles la galerie appartient :</h3>
	<ul>
		<?php
			$categories = null;
			if ((!isset($theGalerie) || ($theGalerie->id === null)) && isset($request) && isset($request['categories']))
				$categories = explode(',', $request['categories']);
			$sortedCategories = Categorie::GetSortedHierachicalListe();
			foreach($sortedCategories as $nom => $categorie)
			{
				if ((strval(intval($nom)) != $nom) && ($nom != ''))
				{
					$classname = '';
					if (isset($theGalerie) && $theGalerie->hasCategorie($categorie->id) || (($categories !== null) && (in_array($categorie->id, $categories))))
						$classname = ' class="selected"';
					echo '<li'.$classname.' id="categorie_'.$categorie->id.'" onclick="adminGalerie_selectCategorie(this, \'categorie\', \''.$categorie->ids.'\', \''.$categorie->sids.'\')" title="'.$nom.'">'.$categorie->nom.'</li>';
				}
			}
		?>
	</ul>
	</aside>
	<?php
		include 'adminGalerieContenu.php';
	?>
	<!--div id="newArticle"><a href="/administration/galerie" onclick="return adminGalerie_nouvelleGalerie(this);">Nouvelle galerie</a></div-->
	
</article>
