<?php
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
					echo '<article id="contenu" class="article">';
					echo '<section id="articlesCategories">';
					echo '<p class="ariane">Vous êtes ici : &gt; <a href="/">Accueil</a>'.$fils.' &gt; '.htmlentities($theCategorie->nom, ENT_NOQUOTES, 'UTF-8').'</p>';
					echo '<h1><a href="/categories/'.Formatage::Lien($theCategorie->nom).'">'.$theCategorie->nom.'</a></h1>';
					require_once 'template/template_general.php';
					echo '<ul id="liste_article">';
					foreach($articles as $article)
					{
						$illustration = $article->GetIllustration();
						if($illustration !== null)
							echo '<li class="unarticle" onclick="accueil_goTo(\'/articles/'.$article->GetLien().'\')"';
						else
							echo '<li class="unarticlesansimages" onclick="accueil_goTo(\'/articles/'.$article->GetLien().'\')"';
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
						echo '<a class="article" href="/articles/'.$article->GetLien().'">';
						echo '<h2>'.Formatage::FormatHtml($article->titre, ENT_COMPAT, false, false, false, true).'</h2>';
						if (strlen($article->resume) > 0)
							echo '<h3>'.Formatage::FormatHtml($article->resume, ENT_COMPAT, true, false, true, true).'</h3>';
						echo '</a>';
						echo '</li>';
					}
					echo '</article>';
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
				echo '<article id="contenu" class="article" style="text-align:center; color: #A12C2E;">';
				echo '<p class="ariane" style="text-align:left;color:black;">Vous êtes ici : &gt; <a href="/">Accueil</a>'.$fils.' &gt; '.htmlentities($theCategorie->nom, ENT_NOQUOTES, 'UTF-8').'</p>';
				echo '<h1 style="text-align:left;"><a href="/categories/'.Formatage::Lien($theCategorie->nom).'">'.$theCategorie->nom.'</a></h1>';
			
				echo '<h2 id="pasArticle">Aucun article dans cette categorie</h2>';
				echo '</article>';
			}
		}
	}
	
?>