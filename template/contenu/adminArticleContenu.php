<?php
	require_once 'include/camsii/Image.php';
	require_once 'include/camsii/Categorie.php';
	require_once 'include/camsii/Categorisation.php';	
	require_once 'include/camsii/Utilisateur.php';
	require_once 'include/camsii/Tableau.php';
	require_once 'include/camsii/Galerie.php';
	require_once 'include/camsii/Page.php';
	require_once 'template/template_general.php';
	require_once 'template/template_image.php';
	require_once 'template/template_galerie.php';

	function afficheFiltre($idCategories = null, $brouillons = false, $archives = false, $filtre = null)
	{
		$categories = Categorie::GetListe();
		echo '<div class="filtres">';
		echo '<div id="categories_photo" class="categories">';
		foreach ($categories as $categorie)
		{
			if (($idCategories !== null) && (in_array($categorie->id, $idCategories)))
				echo '<span id="categorie_'.intval($categorie->id).'" class="selected" onclick="adminPhotos_update(this)">'.$categorie->nom.'</span>';
			else
				echo '<span id="categorie_'.intval($categorie->id).'" onclick="adminPhotos_update(this)">'.$categorie->nom.'</span>';
		}
		echo '<input type="button" value="Toutes" onclick="adminPhotos_selectCategories(1)"/>';
		echo '<input type="button" value="Aucune" onclick="adminPhotos_selectCategories(0)"/>';
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
		echo '<div>Recherche <input type="text" id="seach" name="seach" value="'.$searchClause.'" onchange="adminPhotos_update()" onkeyup="adminPhotos_update()"/>';
		echo '<input type="checkbox" id="brouillons_photo" name="brouillons"'.$brouillonsClause.' onchange="adminPhotos_update()"/>Brouillons';
		echo '<input type="checkbox" id="archives_photo" name="archives"'.$archivesClause.' onchange="adminPhotos_update()"/>Archives';
		echo '</div></div>';
	}
	$commandes = array();
	if (intval($article->id) > 0)
	{
		if ($article->IsBrouillon())
			$commandes[] = '<a onclick="document.getElementById(\'calendrierpublication\').style.display=\'inline\'">Publication</a>';
		if ($article->IsPublished())
			$commandes[] = '<a href="'.Configuration::$Static['url'].'/administration/adminArticle.php?article='.intval($article->id).'&amp;action=depublier">Dépublier</a>';
		if ($article->IsArchive())
			$commandes[] = '<a href="'.Configuration::$Static['url'].'/administration/adminArticle.php?article='.intval($article->id).'&amp;action=desarchiver">Désarchiver</a>';
		else
			$commandes[] = '<a href="/administration/adminArticle.php?article='.intval($article->id).'&amp;action=archiver">Archiver</a>';
		if ($article->IsPlanned())
		{
			$commandes[] = '<a   onclick="document.getElementById(\'calendrierpublication\').style.display=\'inline\'">Modifier la publication</a>';
			$commandes[] = '<a href="'.Configuration::$Static['url'].'/administration/adminArticle.php?article='.intval($article->id).'&amp;action=depublier" onclick="return adminArticles_action(this)">Annuler la publication</a><span> Publié le '.$article->publication.'</span>';
		}
	}
?>
<div id="fb-root"></div>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
</script>
<script type="text/javascript" src="https://apis.google.com/js/plusone.js" ></script>
<script type="text/javascript">
  window.___gcfg = {lang: 'fr'};

  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

<div id="breadcrumb">
	<?php
		$cats = '';
		if ($article->GetCategoriesIds() != '')
			$cats = '?categories='.$article->GetCategoriesIds();
		else
		{
			if (($article->id === null) && isset($request) && isset($request['categories']))
				$cats = '?categories='.$request['categories'];
		}
	?>
	<a href="/administration"><span class="icone adminHome" title="Accueil administration"></span> Administration</a> >
	<a href="/administration/articles<?php echo $cats; ?>">Liste des articles</a> >
	<?php
		if ($article->id !== null)
			$texte = 'Modifier un article';
		else
			$texte = 'Rédiger un article';
		echo $texte;
		echo '<input type="hidden" id="article_id" value="'.(($article->id !== null) ? intval($article->id) : '').'"/>';
	?>
</div>
<?php
			if (intval($article->id) > 0) {?>
	<div class="socialadmin">
	<ul id="share">
	<li class="google_share">
	<div class="g-plus" data-action="share" data-annotation="none" href="<?php echo Configuration::$Url.'/articles/'.$article->GetLien(); ?>" ></div>
	</li>
	<li class="twitter_share">
	<a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-url="<?php echo Configuration::$Url.'/articles/'.$article->GetLien(); ?>" data-lang="fr">Tweeter</a>
	</li>
	<li class="fb_share">
	<img name="fb_share" style="cursor:pointer" src="/Images/facebook_bouton_partager.png" class="fb-like" onclick="window.open('http://www.facebook.com/sharer/sharer.php?u=<?php echo Configuration::$Url.'/articles/'.$article->GetLien(); ?>');" />
	<?php // &t=<?php echo $article->resume; ', 'facebook_share', 'height=320, width=640, toolbar=no, menubar=no, scrollbars=no, resizable=no, location=no, directories=no, status=no' ?>
	</li>
	</ul>
	</div>
	<?php }  ?>
<div class="commandes">

	<?php 	echo '<div id="commandesPublication">'.implode(' ', $commandes).'</div>'; 
	echo '<br>';
						echo '<div class="publication" id="calendrierpublication" style="display:none;">';
						echo '<div class="commandes" id="commandes">';
						echo '<input type="button" value="Publier maintenant" onclick="window.location.href=\''.Configuration::$Static['url'].'/administration/adminArticle.php?article='.intval($article->id).'&amp;action=publier\'">';
						echo '<input type="button" value="Choisir une date" onclick="document.getElementById(\'choixdate\').style.display=\'inline\'; document.getElementById(\'commandes\').style.display=\'none\'" />';
						echo '<input type="button" value="Annuler" onclick="document.getElementById(\'calendrierpublication\').style.display=\'none\'"/>';
						echo '</div>';
						echo '<div id="choixdate" style="display:none;">';
						echo '<span>Choisir la date de publication de l\'article '.$article->titre.' :  </span>';
						echo '<form method="post" action="'.Configuration::$Static['url'].'/administration/adminArticle.php">';
						echo '<input type="datetime-local" id="plannif" name="publication" />';
						echo '<input type="hidden" name="action" value="publier">';
						echo '<input type="hidden" name="article" value="'.intval($article->id).'">';
						echo '<br>';
						echo '<input type="submit" value="Accepter la publication">';
						echo '<input type="button" value="Annuler" onclick="document.getElementById(\'calendrierpublication\').style.display=\'none\'; document.getElementById(\'choixdate\').style.display=\'none\'; document.getElementById(\'commandes\').style.display=\'inline\';" />';
						//onclick="return adminArticles_action(\''.Configuration::$Static['url'].'/administration/adminArticle.php?article='.intval($article->id).'&amp;action=publier\');"
						echo '</form>';
						echo '</div>';
						echo '</div>'; 
	?>
	<span class="icone valider" title="Enregistrer" onclick="return adminArticle_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration';"></span>
	<span class="icone aide" title="Aide mémoire" onclick="adminArticle_montrerAideMemoire();"></span>
</div>

<article class="article">
<?php if ($article->id !== null)
			echo '<a class="articlePreview" target="_blank" href="/articles/'.$article->GetLien().'">'.Configuration::$Url.'/articles/'.$article->GetLien().'</a>';	?>
	<div class="articleHead">
		<div class="commandes">  
			<span class="icone importer tableau" title="Importer un tableau" onclick="return adminArticle_displayImport(this.parentNode.parentNode)"></span>
			<span class="icone tableau" title="Ajouter un tableau" onclick="return adminArticle_addTableau(this.parentNode.parentNode)"></span>
			<span class="icone photo" title="Ajouter une image/photo" onclick="return adminArticle_addIllustration(this.parentNode.parentNode)"></span>
			<span class="icone lien" title="Ajouter un lien" onclick="return adminArticle_displayLien()"></span>
		</div>
		<div id="fen_import_head" class="import" style="display:none">
			<form id="importForm" action="" method="post" enctype="multipart/form-data">
				<span>Veuillez séléctionner un fichier Excel valide (.XLSX) </span>
				<input type="file" name="file" id="FileXLSX" onchange="return adminArticle_validerImportHead();"/></span>
				<progress id="importProgress" value="0"></progress>
			</form>
			<div class="commandes">
				<span class="icone close" title="Fermer" onclick="return adminArticle_closeImport();"></span>
			</div>
		</div>

		<div id="ajoutLienHead" class="import" style="display:none">
		<div class="commandes">
				<span class="icone close" title="Fermer" onclick="return adminArticle_closeLien(this.parentNode.parentNode);"></span>
			</div>
			<span class="liens"><label style="font-weight:bold;">Inserer un lien :</label></span>
			<div class="liens" id="typeLienHead">
					<label>Creer un lien vers</label>
				<select class="selectType" selected="selected" name="selectTypeLien" onchange="adminArticle_selectType(this.value, this.parentNode.parentNode)"/>
					<option value=""></option>
					<option value="document">Document</option>
					<option value="article">Article</option>
					<option value="page">Page</option>
					<option value="autre">Autre</option>
				</select>
			</div>
			
		</div>
		<textarea id="article_titre" title="Titre de l'article" class="titre" placeholder="Titre de l'article" onkeyup="adminArticle_editTexteChanged(this);" onpaste="adminArticle_editTexteChanged(this)" onchange="adminArticle_editTexteChanged(this)"><?php echo htmlentities($article->titre, ENT_COMPAT, 'UTF-8'); ?></textarea>
		<?php
			$illustration = $article->GetIllustration();
			if ($illustration === null)
			{
				$illustration = new illustration();
				$illustration->position = 2;
			}
			afficheIllustrationAdminArticle($illustration, null);
			afficheTableauArticle($article);
		?>
		<textarea id="article_resume" title="Résumé de l'article" class="resume" placeholder="Résumé de l'article" onkeyup="adminArticle_editTexteMultiChanged(this);" onpaste="adminArticle_editTexteMultiChanged(this)" onchange="adminArticle_editTexteMultiChanged(this)"><?php echo htmlentities($article->resume, ENT_COMPAT, 'UTF-8'); ?></textarea>
	</div>
	<div class="sectionBottom"></div>
	<section id="paragraphes">
		<?php
			$index = 0;
			$paragraphes = $article->GetParagraphes();
			if ($paragraphes !== null)
			{
				foreach ($paragraphes as $paragraphe)
				{
					?>
						<section class="paragraphe" id="paragraphe_<?php echo $index;?>">
							<div class="commandes">
								<span class="icone importer tableau" title="Importer un tableau" onclick="return adminArticle_displayImport(this.parentNode.parentNode)"></span>
								<span class="icone tableau" title="Ajouter un tableau" onclick="return adminArticle_addTableauParagraphe(this.parentNode.parentNode, 3)"></span>
								<span class="icone galerie" title="Ajouter une galerie" onclick="return adminArticle_addIllustrationParagraphe(this.parentNode.parentNode, 2)"></span>
								<span class="icone photo" title="Ajouter une image/photo" onclick="return adminArticle_addIllustrationParagraphe(this.parentNode.parentNode, 1)"></span>
								<span class="icone lien" title="Ajouter un lien" onclick="return adminArticle_displayLien(this.parentNode.parentNode)"></span>
								<span class="icone up" title="Monter le paragraphe" onclick="return adminArticle_editTexteMoveUp(this.parentNode.parentNode)"></span>
								<span class="icone down" title="Descendre le paragraphe" onclick="return adminArticle_editTexteMoveDown(this.parentNode.parentNode);"></span>
								<span class="icone delete" title="Supprimer le paragraphe" onclick="return adminArticle_supprimerUnParagraphe(this.parentNode.parentNode);"></span>
							</div>
							<div id="fen_import_<?php echo $index;?>" class="import" style="display:none;">
								<form id="importForm" action="" method="post" enctype="multipart/form-data">
									<span>Veuillez séléctionner un fichier Excel valide (.XLSX) </span>
									<input type="file" name="file" id="FileXLSX_<?php echo $index;?>" class="fichier" onchange="return adminArticle_validerImportParagraphe(this.parentNode.parentNode);"/></span>
									
									
									<progress id="importProgress" value="0"></progress>
								</form>
								<div class="commandes">
									<span class="icone close" title="Fermer" onclick="return adminArticle_closeImport(this.parentNode.parentNode);"></span>
								</div>
							</div>
							
							
			<div id="ajoutLienParagraphe" class="import" style="display:none">
				<div class="commandes">
					<span class="icone close" title="Fermer" onclick="return adminArticle_closeLien(this.parentNode.parentNode);"></span>
				</div>
				<span class="liens"><label style="font-weight:bold;">Inserer un lien :</label></span>
				<div class ="liens" id="typeLienHead">
					<label>Creer un lien vers</label>
					<select class="selectType" selected="selected" name="selectTypeLien" onchange="adminArticle_selectType(this.value, this.parentNode.parentNode)"/>
						<option value=""></option>
						<option value="document">Document</option>
						<option value="article">Article</option>
						<option value="page">Page</option>
						<option value="autre">Autre</option>
					</select>
			</div>
		</div>
							
							<?php
								echo '<textarea id="paragraphe_titre_'.$index.'" title="Titre du paragraphe" class="section" placeholder="Titre du paragraphe" onkeyup="adminArticle_editTexteChanged(this);" onpaste="adminArticle_editTexteChanged(this)" onchange="adminArticle_editTexteChanged(this)">'.htmlentities($paragraphe->titre, ENT_COMPAT, 'UTF-8').'</textarea>';
								$illustration = $paragraphe->GetIllustration();
								if ($illustration === null)
								{
									$illustration = new illustration();
									$illustration->position = 2;
								}
								afficheIllustrationAdminArticle($illustration, $index);
								afficheTableauArticle($article, $paragraphe, $index);
								echo '<textarea id="paragraphe_texte_'.$index.'" title="Paragraphe" class="texte" placeholder="Paragraphe" onkeyup="adminArticle_editTexteMultiChanged(this);" onpaste="adminArticle_editTexteMultiChanged(this)" onchange="adminArticle_editTexteMultiChanged(this)">'.htmlentities($paragraphe->texte, ENT_COMPAT, 'UTF-8').'</textarea>';
							?>
							<div class="sectionBottom"></div>
						</section>
					<?php
					$index++;
				}
			}
		?>			
		<section id="paragraphe_<?php echo $index;?>" class="paragraphe">
			<div class="commandes">
				<span class="icone importer tableau" title="Importer un tableau" onclick="return adminArticle_displayImport(this.parentNode.parentNode)"></span>
				<span class="icone tableau" title="Ajouter un tableau" onclick="return adminArticle_addTableauParagraphe(this.parentNode.parentNode, 3)"></span>
				<span class="icone galerie" title="Ajouter une galerie" onclick="return adminArticle_addIllustrationParagraphe(this.parentNode.parentNode, 2)"></span>
				<span class="icone photo" title="Ajouter une image/photo" onclick="return adminArticle_addIllustrationParagraphe(this.parentNode.parentNode, 1)"></span>
				<span class="icone lien" title="Ajouter un lien" onclick="return adminArticle_displayLien(this.parentNode.parentNode)"></span>
				<span class="icone up" title="Monter le paragraphe" onclick="return adminArticle_editTexteMoveUp(this.parentNode.parentNode)"></span>
				<span class="icone down" title="Descendre le paragraphe" onclick="return adminArticle_editTexteMoveDown(this.parentNode.parentNode);"></span>
				<span class="icone delete" title="Supprimer le paragraphe" onclick="return adminArticle_supprimerUnParagraphe(this.parentNode.parentNode);"></span>
			</div>
			<div id="fen_import_<?php echo $index;?>" class="import" style="display:none;">
								<form id="importForm" action="" method="post" enctype="multipart/form-data">
									<span>Veuillez séléctionner un fichier Excel valide (.XLSX) </span>
									<input type="file" name="file" id="FileXLSX_<?php echo $index;?>" class="fichier" onchange="return adminArticle_validerImportParagraphe(this.parentNode.parentNode);"/></span>
									
									<progress id="importProgress" value="0"></progress>
								</form>
								<div class="commandes">
									<span class="icone close" title="Fermer" onclick="return adminArticle_closeImport(this.parentNode.parentNode);"></span>
								</div>
							</div>
			<div id="ajoutLienParagraphe" class="import" style="display:none">
			<div class="commandes">
				<span class="icone close" title="Fermer" onclick="return adminArticle_closeLien(this.parentNode.parentNode);"></span>
			</div>
				<span class="liens"><label>Inserer un lien :</label></span>
				<div id="typeLienParagraphe" class="liens">
					<label>Creer un lien vers</label>
					<select class="selectType" selected="selected" name="selectTypeLien" onchange="adminArticle_selectType(this.value,this.parentNode.parentNode)"/>
						<option value=""></option>
						<option value="document">Document</option>
						<option value="article">Article</option>
						<option value="page">Page</option>
						<option value="autre">Autre</option>
					</select>
				</div>
			</div>
			<textarea id="paragraphe_titre_<?php echo $index;?>" title="Titre du paragraphe" class="section" placeholder="Titre du paragraphe" onkeyup="adminArticle_editTexteChanged(this);" onpaste="adminArticle_editTexteChanged(this)" onchange="adminArticle_editTexteChanged(this)"></textarea>
			<textarea id="paragraphe_texte_<?php echo $index;?>" title="Paragraphe" class="texte" placeholder="Paragraphe" onkeyup="adminArticle_editTexteMultiChanged(this);" onpaste="adminArticle_editTexteMultiChanged(this)" onchange="adminArticle_editTexteMultiChanged(this)"></textarea>
			<div class="sectionBottom"></div>
		</section>
	</section>
</article>
<div class="commandes">
	<?php 	echo '<div id="commandesPublication">'.implode(' ', $commandes).'</div>'; ?>
	<span class="icone valider" title="Enregistrer" onclick="return adminArticle_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration';"></span>
	<span class="icone aide" title="Aide mémoire" onclick="adminArticle_montrerAideMemoire();"></span>
</div>
<div class="signature">
	<?php
		$pseudo = '';
		if ($article->auteur !== null)
			$pseudo = $article->auteur;
		else
		{
			$utilisateur = null;
			if (isset($_SESSION['utilisateur']))
				$utilisateur = Utilisateur::Get(intval($_SESSION['utilisateur']));
			if (($utilisateur !== null) && ($utilisateur->pseudo !== null))
				$pseudo = $utilisateur->pseudo;
		}
	?>
	<input type="text" id="article_signature" value="<?php echo $pseudo; ?>" placeholder="Auteur"/>
</div>