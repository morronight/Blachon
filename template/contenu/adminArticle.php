<?php
	require_once 'include/camsii/Article.php';
	
	if (!isset($article) && isset($request) && isset($request['article']))
		$article = Article::Get(intval($request['article']));
	if (!isset($article) || ($article === null) || ($article === false))
		$article = new Article();
	if (isset($_SERVER['HTTP_USER_AGENT']) && (preg_match('/Firefox\/(?:[3-9]|1[0-7])/', $_SERVER['HTTP_USER_AGENT']) > 0))
		echo '<script type="text/javascript" src="/Scripts/html5slider.js"></script>';
?>
<!--div id="photoEditor"></div-->
<!--div id="galerieEditor"></div-->
<article id="contenu" class="article" onclick="adminArticle_editParagrapheHook()">
<aside id="categories">
		<h3>Choisir les catégories de l'article</h3>
		<ul>
			<?php
				require_once 'include/camsii/Categorie.php';

				$categories = null;
				if (($article->id === null) && isset($request) && isset($request['categories']))
					$categories = explode(',', $request['categories']);
				$sortedCategories = Categorie::GetSortedHierachicalListe();
				foreach($sortedCategories as $nom => $categorie)
				{
					if ((strval(intval($nom)) != $nom) && ($nom != ''))
					{
						$classname = '';
						if ($article->hasCategorie($categorie->id) || (($categories !== null) && (in_array($categorie->id, $categories))))
							$classname = ' class="selected"';
						echo '<li'.$classname.' id="categorie_'.$categorie->id.'" onclick="adminArticle_selectCategorie(this, \'categorie\', \''.$categorie->ids.'\', \''.$categorie->sids.'\')" title="'.$nom.'">'.$categorie->nom.'</li>';
					}
				}
			?>
		</ul>
	</aside>
	<?php
		include 'adminArticleContenu.php';
	?>	
</article>
<?php
	include 'aideArticle.php';
?>
<script type="text/javascript">
	adminArticle_editParagrapheHook();
</script>
