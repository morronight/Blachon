<?php
	require_once 'include/camsii/Document.php';
	require_once 'include/camsii/Article.php';
	
	$bandeau = Bandeau::Get();
	$documents = Document::GetListe();
	$articles = Article::GetListe(null,false,false,20);
	
	$documentId = null;
	$articleId = null;
	$texte = '';
	$actif = true;
	if (($bandeau !== null) && ($bandeau !== false))
	{
		$actif = (intval($bandeau->actif) == 1);
		$texte = $bandeau->titre;
		if (preg_match('/^(?:http|https|ftp):\/\/[a-z0-9_\-.]+[a-z0-9]\/documents\/([a-z0-9_\-]+\.pdf)(:?"([^"]+)")?$/i', $texte, $regs) == 1)
		{
			if (isset($regs[1]))
			{ 
				$document = Document::GetByPath($regs[1]);
				if (($document !== false) && ($document !== null))
				{
					$documentId = intval($document->id);
					$texte = '';
				}
			}
		}
		else
		{
			$titre = strstr($texte, '"');
			$titre1 = substr($titre,1,-1); 
			$testArt = Article::testTitreExist($titre1);
			if (($testArt !== false) && ($testArt !== null))
			{ 
				$article = Article::getIdByTitre($titre1);
				$articleId = $article->id;
				$texte = '';
			}
		}
	}
?>
<div class="commandes">
	<?php
		$commandes = null;
		if ($commandes !== null)
			echo '<div id="commandesPublication">'.implode(' ', $commandes).'</div>';
	?>
	<span class="icone valider" title="Enregistrer" onclick="return adminBandeau_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration';"></span>
</div>
<div id="breadcrumb">
	<a href="/administration"><span class="icone adminHome" title="Accueil administration"></span> Administration</a> &gt; Modifier le bandeau défilant
</div>
<form id="bandeauForm" action="" method="post">
	<section id="Bandeau" class="bandeau">
		<span>Texte libre</span>
		<input type="text" id="bandeau_texte" value="<?php echo htmlentities($texte, ENT_COMPAT, 'UTF-8'); ?>" placeholder="Texte du bandeau" onchange="adminBandeau_onTexteChange()" onpaste="adminBandeau_onTexteChange()" onkeyup="adminBandeau_onTexteChange()"/>
		<span>Document</span>
		<select id="bandeau_document" onchange="adminBandeau_onDocumentChange()">
			<option value=""></option>
			<?php
				foreach($documents as $document)
				{
					if (($documentId === null) || ($documentId != intval($document->id)))
						echo '<option value="'.intval($document->id).'">'.htmlentities($document->nom, ENT_COMPAT, 'UTF-8').'</option>';
					else
						echo '<option selected="selected" value="'.intval($document->id).'">'.htmlentities($document->nom, ENT_COMPAT, 'UTF-8').'</option>';
				}
			?>
		</select>
		<span>Article</span>
		<select id="bandeau_article" onchange="adminBandeau_onArticleChange()">
			<option value=""></option>
			<?php
				foreach($articles as $article)
				{
						if (($articleId === null) || ($articleId != intval($article->id)))
							echo '<option value="'.intval($article->id).'">'.htmlentities($article->titre, ENT_COMPAT, 'UTF-8').'</option>';
						else
							echo '<option selected="selected" value="'.intval($article->id).'">'.htmlentities($article->titre, ENT_COMPAT, 'UTF-8').'</option>';
				}
			?>
		</select>
		<?php
			$checked = '';
			if ($actif === true)
				$checked = ' checked="checked"';
			echo '<input type="checkbox" id="bandeau_actif" value="1"'.$checked.'> Bandeau activé<br>'.PHP_EOL;
		?>
	</section>
</form>
<div class="commandes">
	<?php
		if ($commandes !== null)
			echo '<div id="commandesPublication">'.implode(' ', $commandes).'</div>';
	?>
	<span class="icone valider" title="Enregistrer" onclick="return adminBandeau_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration';"></span>
</div>
<div class="sectionBottom"></div>