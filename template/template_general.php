<?php
	// Gestion d'articles \\
	
	require_once 'include/camsii/Tableau.php';
	require_once 'include/camsii/Article.php';
	require_once 'include/camsii/Formatage.php';

	function afficheArticle($article)
	{
		ob_start();
		if (is_a($article, 'Article'))
		{
			$fils = '';
			$categories = $article->BuildAriane();
			if ($categories !== null)
			{
				foreach($categories as $categorie)
					$fils .= ' &gt; <a href="/categories/'.Formatage::Lien($categorie->nom).'">'.htmlentities($categorie->nom, ENT_COMPAT, 'UTF-8').'</a>';
			}
		?>
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1&appId=141680632674080";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
			<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			<div class="boutonssociauxart">
			<ul id="like" >
			<li class="google_plus_un" >
			<g:plusone annotation="none" size="medium"></g:plusone>
			</li>
			<li class="twitter">
			<a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-lang="fr">Tweeter</a>
			</li>
			<li class="facebook">
			<div class="fb-like" data-send="false" data-layout="button_count" data-width="500" data-show-faces="false"></div>
			</li>
			</div>
		<?php
			echo '<p class="ariane">Vous êtes ici : &gt; <a href="/">Accueil</a>'.$fils.' &gt; '.htmlentities($article->titre, ENT_NOQUOTES, 'UTF-8').'</p>';
			echo '<h1>'.Formatage::FormatHtml($article->titre, ENT_COMPAT, false, false, false, false).'</h1>';
			$illustration = $article->GetIllustration();
			afficheIllustration($illustration);
			$tableau = null;
			$tableaux = Tableau::GetListe($article->id, false);
			if (($tableaux !== null) && ($tableaux !== false) && (count($tableaux) > 0))
				$tableau = $tableaux[0];
			if ($tableau !== null)
				echo $tableau->Format();
			if (strlen($article->resume) > 0)
				echo '<h3>'.Formatage::FormatHtml($article->resume, ENT_COMPAT, true, true, false, true).'</h3>';
			echo '<div class="sectionBottom"></div>';
			$paragraphes = $article->GetParagraphes();
			if ($paragraphes !== null)
			{
				foreach ($paragraphes as $paragraphe)
				{
					?>
						<section class="paragraphe">
							<?php
								if ($paragraphe->titre !== null)
									echo '<h4>'.Formatage::FormatHtml($paragraphe->titre, ENT_COMPAT, false, true, false, true).'</h4>';
								$illustration = $paragraphe->GetIllustration();
								afficheIllustration($illustration);
								$tableau = null;
								$tableaux = Tableau::GetListe($article->id, $paragraphe->id);
								if (($tableaux !== null) && ($tableaux !== false) && (count($tableaux) > 0))
									$tableau = $tableaux[0];
								if ($tableau !== null)
									echo $tableau->Format();
								echo Formatage::FormatHtml($paragraphe->texte, ENT_COMPAT, true, true, true, true);
							?>
							<div class="sectionBottom"></div>
						</section>
					<?php
				}
			}
			?>
				<div id="zoomGalerie" onclick="return article_fermerZoom();"></div>
			<?php
		}
		$rslt = ob_get_contents();
		ob_end_clean();
		return $rslt;
	}
	
	function afficheIllustration($illustration)
	{
		if (is_a($illustration, 'Illustration'))
		{
			echo '<div';
			switch(intval($illustration->position))
			{
			case 0:
				echo ' class="floatLeft"';
				break;
			case 1:
				echo ' class="floatRight"';
				break;
			case 2:
				echo ' class="floatCenter"';
				break;
			}
			echo '>';
			if ($illustration->id_image !== null)
				afficheIllustrationImage($illustration);
			if ($illustration->id_galerie !== null)
				afficheIllustrationGalerie($illustration);
			if ($illustration->legende !== null)
			{
				echo '<div';
				if ($illustration->largeur !== null)
					echo ' style="width:'.intval($illustration->largeur).'px; max-width:100%"';
				echo ' class="legende">'.htmlentities($illustration->legende, ENT_COMPAT, 'UTF-8').'</div>';
			}
			echo '</div>';
		}
	}

	function afficheIllustrationImage($illustration)
	{
		if (is_a($illustration, 'Illustration') && ($illustration->id_image !== null))
		{
			echo '<img src="'.Configuration::$Static['url'].'/Images/I'.intval($illustration->id_image);
			if (($illustration->hauteur !== null) || ($illustration->largeur !== null))
			{
				echo '_';
				if ($illustration->largeur !== null)
					echo intval($illustration->largeur);
				if ($illustration->hauteur !== null)
					echo '_'.intval($illustration->hauteur);
			}
			echo '"';
			if ($illustration->legende !== null)
			{
				echo ' alt="'.htmlentities($illustration->legende, ENT_COMPAT, 'UTF-8').'"';
				echo ' title="'.htmlentities($illustration->legende, ENT_COMPAT, 'UTF-8').'"';
			}
			else
			{
				echo ' alt="Image"';
				echo ' title="Image"';
			}
			if (($illustration->hauteur !== null) || ($illustration->largeur !== null))
			{
				if ($illustration->hauteur !== null)
					echo ' height="'.intval($illustration->hauteur).'"';
				if ($illustration->largeur !== null)
					echo ' width="'.intval($illustration->largeur).'"';
			}
			echo '/>';
		}
	}
	
	function afficheIllustrationGalerie($illustration)
	{
		require_once 'include/camsii/Galerie.php';
		
		if (is_a($illustration, 'Illustration') && ($illustration->id_galerie !== null))
		{
			$galerie = Galerie::Get($illustration->id_galerie);
			if ($galerie !== null)
			{
				$images = $galerie->GetImages();
				if (($images !== null) && (count($images) > 0))
				{
					foreach ($images as $image)
					{
						$width = 200;
						$filepath = $image->GetFilePath();
						if (is_file($filepath))
						{
							list($w, $h) = getimagesize($filepath);
							$height = intval(1. * $h * $width / $w);
							if ($height > 200)
							{
								$height = 200;
								$width = intval(1.* $w * $height / $h);
							}
						}
						$legende = '';
						if ($image->legende !== null)
							$legende = htmlentities($image->legende, ENT_NOQUOTES, 'UTF-8');
						echo '<div class="imageGalerie">';
						echo '<img id="image_'.intval($image->id).'" src="'.Configuration::$Static['url'].'/Images/I'.intval($image->id).'_'.$width.'_'.$height.'" alt="'.$legende.'" title="'.$legende.'" onclick="return article_selectPhoto('.intval($image->id).')"/>';
						echo '</div>';
					}
				}
			}
		}
	}
	
	function afficheListeArticles($theCategorie)
	{
		$notfound = true;
		if ((isset($theCategorie)) && ($theCategorie !== null))
		{
			if ($theCategorie->id_article === null)
			{
				$articles = Article::GetListe($theCategorie->id);		
				if (($articles !== null) && (count($articles) > 0))
				{
					$notfound = false;
					if (count($articles) > 1)
					{
						echo '<div class="listeArticles">';
						echo '<h1><a href="/categories/'.Formatage::Lien($theCategorie->nom).'">'.$theCategorie->nom.'</a></h1>';
						foreach($articles as $article)
							echo afficheResumeArticle($article);
						echo '</div>';
					}
					else
					{
						echo '<article id="contenu" class="article">';				
						echo afficheArticle($articles[0]);
						echo '</article>';
					}
				}
			}
			else
			{
				$notfound = false;
				echo '<article id="contenu" class="article">';
				$article = Article::Get($theCategorie->id_article);
				echo afficheArticle($article);
				echo '</article>';
			}
		}
		if ($notfound)
		{
			echo '<article id="contenu" class="article">';
			if ((isset($theCategorie)) && ($theCategorie !== null))
				echo '<h1><a href="/categories/'.Formatage::Lien($theCategorie->nom).'">'.$theCategorie->nom.'</a></h1>';
			echo 'Aucun article';
			echo '</article>';
		}
	}
	
	function afficheResumeArticle($article)
	{
		ob_start();
		if (is_a($article, 'Article'))
		{
			echo '<a class="article" href="/articles/'.$article->GetLien().'">';
			echo '<h2>'.Formatage::FormatHtml($article->titre, ENT_COMPAT, false, false, false, true).'</h2>';
			$illustration = $article->GetIllustration();
			afficheIllustrationResume($illustration);
			if (strlen($article->resume) > 0)
				echo '<h3>'.Formatage::FormatHtml($article->resume, ENT_COMPAT, true, false, true, true).'</h3>';
			echo 'Lire la suite...';
			echo '<div class="sectionBottom"></div>';
			echo '</a>';
		}
		$rslt = ob_get_contents();
		ob_end_clean();
		return $rslt;
	}
	
	function afficheIllustrationResume($illustration)
	{
		if (is_a($illustration, 'Illustration'))
		{
			echo '<div';
			switch(intval($illustration->position))
			{
			case 0:
				echo ' class="floatLeft"';
				break;
			case 1:
				echo ' class="floatRight"';
				break;
			case 2:
				echo ' class="floatCenter"';
				break;
			}
			echo '>';
			if ($illustration->id_image !== null)
				afficheIllustrationResumeImage($illustration);
			if ($illustration->legende !== null)
				echo '<div class="legende">'.htmlentities($illustration->legende, ENT_COMPAT, 'UTF-8').'</div>';
			echo '</div>';
		}
	}
	
	function afficheIllustrationResumeImage($illustration)
	{
		if (is_a($illustration, 'Illustration') && ($illustration->id_image !== null))
		{
			echo '<img src="'.Configuration::$Static['url'].'/Images/I'.intval($illustration->id_image);
			if (($illustration->hauteur !== null) || ($illustration->largeur !== null))
			{
				echo '_';
				if ($illustration->largeur !== null)
					echo intval($illustration->largeur);
				if ($illustration->hauteur !== null)
					echo '_'.intval($illustration->hauteur);
			}
			echo '"';
			if ($illustration->legende !== null)
			{
				echo ' alt="'.htmlentities($illustration->legende, ENT_COMPAT, 'UTF-8').'"';
				echo ' title="'.htmlentities($illustration->legende, ENT_COMPAT, 'UTF-8').'"';
			}
			else
			{
				echo ' alt="Image"';
				echo ' title="Image"';
			}
			if (($illustration->hauteur !== null) || ($illustration->largeur !== null))
			{
				if ($illustration->hauteur !== null)
					echo ' height="'.intval($illustration->hauteur).'"';
				if ($illustration->largeur !== null)
					echo ' width="'.intval($illustration->largeur).'"';
			}
			echo '/>';
		}
	}
	
	function afficheArticlesAdmin($articles = null, $idCategories = null, $brouillons = false, $archives = false, $filtre = null)
	{
		if ($articles === null)
			$articles = Article::Search($idCategories, $brouillons, $archives, $filtre);
		if ($articles !== null)
		{
			echo '<div id="liste" class="liste">';
			if (count($articles) > 0)
			{	
				$i=0;
				foreach ($articles as $article)
				{
					if (is_a($article, 'Article'))
					{
						echo '<div class="listeArticles">';
						$commandes = null;
						if ($article->IsBrouillon())
							$commandes[] = '<a onclick="document.getElementById(\'calendrierpublication_'.$i.'\').style.display=\'inline\'">Publication</a>';
						if ($article->IsPublished())
							$commandes[] = '<a href="'.Configuration::$Static['url'].'/administration/adminArticle.php?article='.intval($article->id).'&amp;action=depublier" onclick="return adminArticles_action(this)">Dépublier</a>';
						if ($article->IsArchive())
							$commandes[] = '<a href="'.Configuration::$Static['url'].'/administration/adminArticle.php?article='.intval($article->id).'&amp;action=desarchiver" onclick="return adminArticles_action(this)">Désarchiver</a>';
						else
							$commandes[] = '<a href="'.Configuration::$Static['url'].'/administration/adminArticle.php?article='.intval($article->id).'&amp;action=archiver" onclick="return adminArticles_action(this)">Archiver</a>';
						if ($article->IsPlanned())
						{
							$commandes[] = '<span> Publié le '.$article->publication.'</span><a   onclick="document.getElementById(\'calendrierpublication_'.$i.'\').style.display=\'inline\'">Modifier la publication</a>';
							$commandes[] = '<a href="'.Configuration::$Static['url'].'/administration/adminArticle.php?article='.intval($article->id).'&amp;action=depublier" onclick="return adminArticles_action(this)">Annuler la publication</a>';
						}
						
						echo '<h2><a href="'.Configuration::$Static['url'].'/administration/article?article='.intval($article->id).'">'.Formatage::FormatHtml($article->titre, ENT_COMPAT, false, false, false, false).'</a></h2>';
						$illustration = $article->GetIllustration();
						afficheIllustration($illustration);
						echo '<p><a href="'.Configuration::$Static['url'].'/administration/article?article='.intval($article->id).'">'.Formatage::FormatHtml($article->resume.PHP_EOL.'Lire la suite...', ENT_COMPAT, true, false, true, true).'</a></p>';
						echo '<div id="commandesPublication">'.implode(' ', $commandes).'</div>';
						echo '<br>';
						echo '<div class="publication" id="calendrierpublication_'.$i.'" style="display:none;">';
						echo '<div class="commandes" id="commandes_'.$i.'">';
						echo '<input type="button" value="Publier maintenant" onclick="adminArticles_action(\''.Configuration::$Static['url'].'/administration/adminArticle.php?article='.intval($article->id).'&amp;action=publier\','.$i.');">';
						echo '<input type="button" value="Choisir une date" onclick="document.getElementById(\'choixdate_'.$i.'\').style.display=\'inline\'; document.getElementById(\'commandes_'.$i.'\').style.display=\'none\'" />';
						echo '<input type="button" value="Annuler" onclick="document.getElementById(\'calendrierpublication_'.$i.'\').style.display=\'none\'"/>';
						echo '</div>';
						echo '<div id="choixdate_'.$i.'" style="display:none;">';
						echo '<span>Choisir la date de publication de l\'article '.$article->titre.' :  </span>';
						echo '<form method="post" action="'.Configuration::$Static['url'].'/administration/adminArticle.php" onsubmit = "adminArticles_action(\''.Configuration::$Static['url'].'/administration/adminArticle.php?article='.intval($article->id).'&amp;action=publier\')">';
						echo '<input type="datetime-local" id="plannif_'.$i.'" name="publication" />';
						echo '<input type="hidden" name="action" value="publier">';
						echo '<input type="hidden" name="article" value="'.intval($article->id).'">';
						echo '<br>';
						echo '<input type="button" value="Accepter la publication" onclick="return adminArticles_action(\''.Configuration::$Static['url'].'/administration/adminArticle.php?article='.intval($article->id).'&amp;action=publier\','.$i.');">';
						echo '<input type="button" value="Annuler" onclick="document.getElementById(\'calendrierpublication_'.$i.'\').style.display=\'none\'; document.getElementById(\'choixdate_'.$i.'\').style.display=\'none\'; document.getElementById(\'commandes_'.$i.'\').style.display=\'inline\';" />';
						//onclick="return adminArticles_action(\''.Configuration::$Static['url'].'/administration/adminArticle.php?article='.intval($article->id).'&amp;action=publier\');"
						echo '</form>';
						echo '</div>';
						echo '</div>';
						echo '<div class="sectionBottom"></div></div>';
					}
				$i++;
				}
			}
			else
			{
				if ($idCategories === null)
					echo '<span class="message">Aucun article trouvé. Sélectionnez une catégorie.</span>';
				else
					echo '<span class="message">Aucun article trouvé</span>';
			}
			echo '</div>';
		}
		return $articles;
	}
	
	function afficheIllustrationAdmin($illustration)
	{
		if (is_a($illustration, 'Illustration'))
		{
			echo '<img src="'.Configuration::$Static['url'].'/Images/I'.intval($illustration->id_image).'__60"';
			if ($illustration->legende !== null)
			{
				echo ' alt="'.htmlentities($illustration->legende, ENT_COMPAT, 'UTF-8').'"';
				echo ' title="'.htmlentities($illustration->legende, ENT_COMPAT, 'UTF-8').'"';
			}
			else
			{
				echo ' alt="Image"';
				echo ' title="Image"';
			}
			echo ' style="height:60px;" class="floatLeft"/>';
		}
	}
	
	
	function afficheIllustrationAdminArticle($illustration, $index)
	{
		if (is_a($illustration, 'Illustration'))
		{
			$class = ' illustration';
			if ($illustration->id_image !== null)
				$class .= ' modeImage';
			if ($illustration->id_galerie !== null)
				$class .= ' modeGalerie';
			echo '<div';
			switch(intval($illustration->position))
			{
			case 0:
				echo ' class="floatLeft'.$class.'"';
				break;
			case 1:
				echo ' class="floatRight'.$class.'"';
				break;
			case 2:
				echo ' class="floatCenter'.$class.'"';
				break;
			}
			echo '>';
			echo '<div class="commandes">';
			$l = 642;
			if ($illustration->largeur !== null)
				$l = intval($illustration->largeur);
			echo '<input type="range" class="imageSize" min="50" max="642" step="1" value="'.$l.'" onchange="return adminArticle_resizeImage(this.parentNode.parentNode, this.value);"/>';
			echo '<span class="icone galerie" title="Changer de galerie" onclick="return adminArticle_selectGalerie(this.parentNode.parentNode);"></span>';
			echo '<span class="icone photo" title="Changer de photo/image" onclick="return adminArticle_selectImage(this.parentNode.parentNode);"></span>';
			echo '<span class="icone gaucheToute" title="Aligner à gauche" onclick="return adminArticle_alignerIllustration(this.parentNode.parentNode, 0);"></span>';
			echo '<span class="icone centre" title="Center" onclick="return adminArticle_alignerIllustration(this.parentNode.parentNode, 2);"></span>';
			echo '<span class="icone droiteToute" title="Aligner à droite" onclick="return adminArticle_alignerIllustration(this.parentNode.parentNode, 1);"></span>';
			echo '<span class="icone delete" title="Supprimer l\'illustration" onclick="return adminArticle_supprimerIllustration(this.parentNode.parentNode);"></span>';
			echo '</div>';
			if ($illustration->id_image !== null)
				afficheIllustrationImageAdminArticle($illustration, $index);
			if ($illustration->id_galerie !== null)
				afficheIllustrationGalerie($illustration, $index);
			$texte = '';
			if ($illustration->legende !== null)
				$texte = $illustration->legende;
			echo '<textarea';
			if ($index !== null)
				echo ' id="paragraphe_legende_'.$index.'"';
			else
				echo ' id="article_legende"';
			echo ' title="Légende" placeholder="Légende" class="legende" onkeyup="adminArticle_editTexteMultiChanged(this);" onpaste="adminArticle_editTexteMultiChanged(this)" onchange="adminArticle_editTexteMultiChanged(this)">'.htmlentities($texte, ENT_COMPAT, 'UTF-8').'</textarea>';
			echo '</div>';
		}
	}

	function afficheIllustrationImageAdminArticle($illustration, $index)
	{
		if (is_a($illustration, 'Illustration') && ($illustration->id_image !== null))
		{
			if ($index !== null)
				$id = 'paragraphe_image_'.intval($index);
			else
				$id = 'article_image';
			echo '<img class="illustrationImage" id="'.$id.'" onclick="adminArticle_editPhoto(this)" src="/Images/I'.intval($illustration->id_image).'"';
			if ($illustration->legende !== null)
			{
				echo ' alt="'.htmlentities($illustration->legende, ENT_COMPAT, 'UTF-8').'"';
				echo ' title="'.htmlentities($illustration->legende, ENT_COMPAT, 'UTF-8').'"';
			}
			else
			{
				echo ' alt="Image"';
				echo ' title="Image"';
			}
			if (($illustration->hauteur !== null) || ($illustration->largeur !== null))
			{
				if ($illustration->hauteur !== null)
					echo ' height="'.intval($illustration->hauteur).'"';
				if ($illustration->largeur !== null)
					echo ' width="'.intval($illustration->largeur).'"';
			}
			echo '/>';
		}
	}
	
	function afficheIllustrationGalerieAdminArticle($illustration, $index)
	{
		require_once 'include/camsii/Galerie.php';
		
		if (is_a($illustration, 'Illustration') && ($illustration->id_galerie !== null))
		{
			$galerie = Galerie::Get($illustration->id_galerie);
			if ($galerie !== null)
			{
				echo '<input type="hidden" class="galerieId" value="'.intval($illustration->id_galerie).'"/>';
				$images = $galerie->GetImages();
				if (($images !== null) && (count($images) > 0))
				{
					foreach ($images as $image)
					{
						$width = 200;
						$filepath = $image->GetFilePath();
						if (is_file($filepath))
						{
							list($w, $h) = getimagesize($filepath);
							$height = intval(1. * $h * $width / $w);
							if ($height > 200)
							{
								$height = 200;
								$width = intval(1.* $w * $height / $h);
							}
						}
						$legende = '';
						if ($image->legende !== null)
							$legende = htmlentities($image->legende, ENT_NOQUOTES, 'UTF-8');
						echo '<span class="imageGalerie">';
						echo '<img id="image_'.intval($image->id).'" src="'.Configuration::$Static['url'].'/Images/I'.intval($image->id).'_'.$width.'_'.$height.'" alt="'.$legende.'" title="'.$legende.'"/>';
						echo '</span>';
					}
				}
			}
		}
	}
	
	function afficheTableauArticle($article, $paragraphe = null, $index = null)
	{
		if ($article->id === null)
			return;
		if ($paragraphe !== null)
			$tableaux = Tableau::GetListe($article->id, $paragraphe->id);
		else
			$tableaux = Tableau::GetListe($article->id, false);
		if (($tableaux !== null) && ($tableaux !== false) && (count($tableaux) > 0))
		{
			$tableau = $tableaux[0];
			afficheTableau($tableau, $index);
		}
	}

	function afficheTableau($tableau, $index = null)
	{
		if ($tableau !== null)
		{
			if ($index === null)
				$prefix = 'cellule';
			else
				$prefix = 'paragraphe_cellule_'.intval($index);
			?>
				<div class="tableau">
					
					<div class="commandes">
						<span class="icone addLigne" title="Ajouter une ligne" onclick="return adminArticle_ajouterLigneTableau(this.parentNode.parentNode, '<?php echo $prefix; ?>');"></span>
						<span class="icone addColonne" title="Ajouter une colonne" onclick="return adminArticle_ajouterColonneTableau(this.parentNode.parentNode, '<?php echo $prefix; ?>');"></span>
						<span class="icone delete" title="Supprimer le tableau" onclick="return adminArticle_supprimerTableau(this.parentNode.parentNode);"></span>
					</div>
					<?php
						if ($index === null)
							echo $tableau->Format('tableau_article', null, $prefix);
						else
							echo $tableau->Format('paragraphe_tableau_'.$index, null, $prefix);
					?>
				</div>
			<?php
		}
	}
	
	function afficheListeActusAccueil()
	{
		
		$notfound = true;
		$cat = array(1,2);
		$tevenements = array(2);
		$tactualites = array(1);
		$articles = Article::GetListe($cat);	
		$evenements = Article::GetListe($tevenements);
		$actualites = Article::GetListe($tactualites);
		if (($articles !== null) && (count($articles) > 0))
		{
			$notfound = false;
			if (count($articles) > 1)
			{
				echo '<ul id="liste_article">';
				foreach($evenements as $evenement)
				{
					$illustration = $evenement->GetIllustration();
					if($illustration !== null)
						echo '<li class="unarticle" onclick="accueil_goTo(\'/articles/'.$evenement->GetLien().'\')"';
					else
						echo '<li class="unarticlesansimages" onclick="accueil_goTo(\'/articles/'.$evenement->GetLien().'\')"';
					if($illustration !== null)
					{
						echo 'style="background-image:url(\''.Configuration::$Static['url'].'/Images/I'.intval($illustration->id_image);
						if (($illustration->hauteur !== null) || ($illustration->largeur !== null))
						{
							echo '_';
							if ($illustration->largeur !== null)
								echo intval($illustration->largeur);
							if ($illustration->hauteur !== null)
								echo '_'.intval($illustration->hauteur);
						}
						echo '\');background-repeat: no-repeat;">';
					}
					else
					{
						echo '>';
					}
					echo '<a class="article" href="/articles/'.$evenement->GetLien().'">';
					echo '<h2>'.Formatage::FormatHtml($evenement->titre, ENT_COMPAT, false, false, false, true).'</h2>';
					if (strlen($evenement->resume) > 0)
						echo '<h3>'.Formatage::FormatHtml($evenement->resume, ENT_COMPAT, true, false, true, true).'</h3>';
					echo '</a>';
					echo '</li>';
				}
				
				foreach($actualites as $actualite)
				{
					$illustration = $actualite->GetIllustration();
					if($illustration !== null)
						echo '<li class="unarticle" onclick="accueil_goTo(\'/articles/'.$actualite->GetLien().'\')"';
					else
						echo '<li class="unarticlesansimages" onclick="accueil_goTo(\'/articles/'.$actualite->GetLien().'\')"';
					if($illustration !== null)
					{
						echo 'style="background-image:url(\''.Configuration::$Static['url'].'/Images/I'.intval($illustration->id_image);
						if (($illustration->hauteur !== null) || ($illustration->largeur !== null))
						{
							echo '_';
							if ($illustration->largeur !== null)
								echo intval($illustration->largeur);
							if ($illustration->hauteur !== null)
								echo '_'.intval($illustration->hauteur);
						}
						echo '\');background-repeat: no-repeat;">';
					}
					else
					{
						echo '>';
					}
					echo '<a class="article" href="/articles/'.$actualite->GetLien().'">';
					echo '<h2>'.Formatage::FormatHtml($actualite->titre, ENT_COMPAT, false, false, false, true).'</h2>';
					if (strlen($actualite->resume) > 0)
						echo '<h3>'.Formatage::FormatHtml($actualite->resume, ENT_COMPAT, true, false, true, true).'</h3>';
					echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
			}
			else
			{
				echo '<article id="contenu" class="article">';				
				echo afficheArticle($articles[0]);
				echo '</article>';
			}
		}
		if ($notfound)
		{
			echo '<article id="contenu" class="article" style="text-align:center; color: #A12C2E;margin-top: 100px;">';
			echo 'Aucun(e)s êvenements / actualités';
			echo '</article>';
		}
	}
	
	function afficheListeProduits()
	{
		
		$notfound = true;
		$catproduit = array(5);
		$produits = Article::GetListe($catproduit);
		if (($produits !== null) && (count($produits) > 0))
		{
			$notfound = false;
			if (count($produits) > 1)
			{
				echo '<ul id="liste_article">';
				foreach($produits as $produit)
				{
					$illustration = $produit->GetIllustration();
					if($illustration !== null)
						echo '<li class="unarticle" onclick="accueil_goTo(\'/articles/'.$produit->GetLien().'\')"';
					else
						echo '<li class="unarticlesansimages" onclick="accueil_goTo(\'/articles/'.$produit->GetLien().'\')"';
					if($illustration !== null)
					{
						echo 'style="background-image:url(\''.Configuration::$Static['url'].'/Images/I'.intval($illustration->id_image);
						if (($illustration->hauteur !== null) || ($illustration->largeur !== null))
						{
							echo '_';
							if ($illustration->largeur !== null)
								echo intval($illustration->largeur);
							if ($illustration->hauteur !== null)
								echo '_'.intval($illustration->hauteur);
						}
						echo '\');background-repeat: no-repeat;">';
					}
					else
					{
						echo '>';
					}
					echo '<a class="article" href="/articles/'.$produit->GetLien().'">';
					echo '<h2>'.Formatage::FormatHtml($produit->titre, ENT_COMPAT, false, false, false, true).'</h2>';
					if (strlen($produit->resume) > 0)
						echo '<h3>'.Formatage::FormatHtml($produit->resume, ENT_COMPAT, true, false, true, true).'</h3>';
					echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
			}
			else
			{
				echo '<article id="contenu" class="article">';				
				echo afficheArticle($produits[0]);
				echo '</article>';
			}
		}
		if ($notfound)
		{
			echo '<article id="contenu" class="article" style="text-align:center; color: #A12C2E;margin-top: 100px;">';
			echo 'Aucuns produits référencés';
			echo '</article>';
		}
	}
	
	
	
	
	
	function affichePresentationAccueil()
	{
		
		$notfound = true;
		$article = Article::Get(1);		
		if ($article !== null)
		{
			$notfound = false;	
			$illustration = $article->GetIllustration();
			echo '<div ';
			if($illustration !== null)
			{
				echo 'style="background-image:url(\''.Configuration::$Static['url'].'/Images/I'.intval($illustration->id_image);
				if (($illustration->hauteur !== null) || ($illustration->largeur !== null))
				{
					echo '_';
					if ($illustration->largeur !== null)
						echo intval($illustration->largeur);
					if ($illustration->hauteur !== null)
						echo '_'.intval($illustration->hauteur);
				}
				echo '\');background-repeat: no-repeat;">';
			}
			else
			{
				echo '>';
			}
			if (strlen($article->resume) > 0)
				echo '<h3>'.Formatage::FormatHtml($article->resume, ENT_COMPAT, true, false, true, true).'</h3>';
			echo '</a>';
			echo '</div>';
		}
		if ($notfound)
		{
			echo '<article id="contenu" class="article">';
			echo 'Aucune présentation';
			echo '</article>';
		}
	}
	
	function formulaireContact()
	{
		echo '
		<form method="post" action="/contact.php">
		<fieldset><legend>Vos coordonnées</legend>
			<div class="champsContact">
				<span><label for="nom">Nom Prénom * :</label></span>
				<br>
				<span><input type="text" id="nomprenom" name="nomprenom" required="required" autocomplete="billing name" tabindex="1" /><span>
			</div>
			<div class="champsContact">
				<span><label for="email">Email :</label></span>
				<br>
				<span><input type="mail" id="email" name="email" autocomplete="billing email" tabindex="2" /></span>
			</div>
			<div class="champsContact">
				<span><label for="email">Téléphone :</label></span>
				<br>
				<span><input type="tel" id="telephone" name="telephone" placeholder="Exemple : 0601020304" autocomplete="billing mobile tel" tabindex="3" /></span>
			</div>
		</fieldset>
	 
		<fieldset><legend>Votre message :</legend>
			<div class="champsContact">
				<span><label for="objet">Objet * :</label></span>
				<br>
				<span><input type="text" id="objet" name="objet" required="required" tabindex="4" /></span>
			</div>
			<br>
			<div class="champsContact">
				<span><label for="message">Message :</label></span>
				<br>
				<span><textarea id="message" name="message" tabindex="5" cols="30" rows="8"></textarea></span>
			</div>
		<p>* Obligatoire</p>
		</fieldset>
		<div style="text-align:center;"><input type="submit" name="envoi" value="Envoyer le formulaire" /></div>
		</form>';
	}

?>