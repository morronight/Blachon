<div class="commandes">
	<?php
		$commandes = null;
		if (isset($theDocument))
		{
			if ($theDocument->IsBrouillon())
				$commandes[] = '<a href="/administration/adminDocument.php?document='.intval($theDocument->id).'&amp;action=publier" onclick="return adminDocument_action(this)">Publier</a>';
			else
				$commandes[] = '<a href="/administration/adminDocument.php?document='.intval($theDocument->id).'&amp;action=depublier" onclick="return adminDocument_action(this)">Dépublier</a>';
			if ($theDocument->IsArchive())
				$commandes[] = '<a href="/administration/adminDocument.php?document='.intval($theDocument->id).'&amp;action=desarchiver" onclick="return adminDocument_action(this)">Désarchiver</a>';
			else
				$commandes[] = '<a href="/administration/adminDocument.php?document='.intval($theDocument->id).'&amp;action=archiver" onclick="return adminDocument_action(this)">Archiver</a>';
		}
		if ($commandes !== null)
			echo '<div id="commandesPublication">'.implode(' ', $commandes).'</div>';
	?>
	<span class="icone valider" title="Enregistrer" onclick="return adminDocument_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration/documents';"></span>
	<?php
		if (isset($theDocument) && ($theDocument->IsArchive() || ($theDocument->IsBrouillon())))
			echo '<span class="icone delete" title="Supprimer" onclick="return adminDocument_supprimer();"></span>';
	?>
</div>
<div id="breadcrumb">
	<?php
		$cats = '';
		if (isset($theDocument))
			$cats .= '?categories='.$theDocument->GetCategoriesIds();
	?>
	<a href="/administration"><span class="icone adminHome" title="Accueil administration"></span> Administration</a> &gt; <a href="/administration/documents<?php echo $cats; ?>">Les documents</a> &gt;
	<?php
		if (isset($theDocument))
		{
			echo 'Modifier un document';
			echo '<input type="hidden" id="document_id" value="'.(($theDocument->id !== null) ? intval($theDocument->id) : '').'"/>';
		}
		else
			echo 'Envoyer un document';
	?>
</div>
<form id="documentForm" action="" method="post" enctype="multipart/form-data">
	<?php
		if (isset($theDocument))
		{
			$nom = $theDocument->nom;
			$description = $theDocument->GetDescription();
			$texte = 'Changer le fichier';
			$visible = (intval($theDocument->visible) > 0);
		}
		else
		{
			$nom = '';
			$description = '';
			$texte = 'Choisir le fichier';
			$visible = true;
		}
		echo '<section id="Document" class="document">';
		echo '<span>Nom du document</span>';
		echo '<input type="text" id="document_nom" value="'.htmlentities($nom, ENT_COMPAT, 'UTF-8').'" placeholder="Nom du document (obligatoire)">';
		echo '<span>Description du document</span>';
		echo '<textarea id="document_description" placeholder="Description du document (facultatif)">'.htmlentities($description, ENT_COMPAT, 'UTF-8').'</textarea>';
		if (isset($theDocument))
			echo '<a target="_blank" href="/documents/'.$theDocument->GetLien().'">'.Configuration::$Url.'/documents/'.$theDocument->GetLien().' ('.$theDocument->GetReadeableSize().')</a>';
		echo '</section>';
		$checked = '';
		if ($visible !== true)
			$checked = ' checked="checked"';
		echo '<input type="checkbox" id="document_masque" value="1"'.$checked.'> Document masqué<br>'.PHP_EOL;
		echo $texte;
	?>
	<input type="file" id="ajoutDocumentBrowse"/>
	<progress id="documentProgress" value="0"></progress>
</form>
<div class="commandes">
	<?php
		if ($commandes !== null)
			echo '<div id="commandesPublication">'.implode(' ', $commandes).'</div>';
	?>
	<span class="icone valider" title="Enregistrer" onclick="return adminDocument_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration/documents';"></span>
	<?php
		if (isset($theDocument) && ($theDocument->IsArchive() || ($theDocument->IsBrouillon())))
			echo '<span class="icone delete" title="Supprimer" onclick="return adminDocument_supprimer();"></span>';
	?>
</div>
<div class="sectionBottom"></div>