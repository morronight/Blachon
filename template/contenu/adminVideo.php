<?php
	require_once 'include/camsii/Video.php';
	require_once 'include/camsii/Categorie.php';
	if (!isset($theVideo) && isset($request) && isset($request['video']))
		$theVideo = Video::Get(intval($request['video']));
	if (isset($theVideo) && (($theVideo === null) || ($theVideo === false)))
		$theVideo = new Video();
?>
<script type="text/javascript" src="/Scripts/adminVideo.js"></script>
<article id="contenu" class="article">
<aside id="categories">
	<h3>Choisir les catégories de la video</h3>
	<ul>
		<?php
			$categories = null;
			if ((!isset($theVideo) || ($theVideo->id === null)) && isset($request) && isset($request['categories']))
				$categories = explode(',', $request['categories']);
			$sortedCategories = Categorie::GetSortedHierachicalListe();
			foreach($sortedCategories as $nom => $categorie)
			{
				if ((strval(intval($nom)) != $nom) && ($nom != ''))
				{
					$classname = '';
					if (isset($theVideo) && $theVideo->hasCategorie($categorie->id) || (($categories !== null) && (in_array($categorie->id, $categories))))
						$classname = ' class="selected"';
					echo '<li'.$classname.' id="categorie_'.$categorie->id.'" onclick="adminVideo_selectCategorie(this, \'categorie\', \''.$categorie->ids.'\', \''.$categorie->sids.'\')" title="'.$nom.'">'.$categorie->nom.'</li>';
				}
			}
		?>
	</ul>
	<div id="newArticle"><a href="/administration/video" onclick="return adminVideo_nouvelleVideo(this);">Nouvelle video</a></div>
</aside>
	<?php
		include 'adminVideoContenu.php';
	?>
</article>
