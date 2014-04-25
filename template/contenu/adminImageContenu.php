<div class="commandes">
	<?php
		$commandes = null;
		if (isset($theImage))
		{
			if ($theImage->IsBrouillon())
				$commandes[] = '<a href="/administration/adminImage.php?image='.intval($theImage->id).'&amp;action=publier" onclick="return adminImage_action(this)">Publier</a>';
			else
				$commandes[] = '<a href="/administration/adminImage.php?image='.intval($theImage->id).'&amp;action=depublier" onclick="return adminImage_action(this)">Dépublier</a>';
			if ($theImage->IsArchive())
				$commandes[] = '<a href="/administration/adminImage.php?image='.intval($theImage->id).'&amp;action=desarchiver" onclick="return adminImage_action(this)">Désarchiver</a>';
			else
				$commandes[] = '<a href="/administration/adminImage.php?image='.intval($theImage->id).'&amp;action=archiver" onclick="return adminImage_action(this)">Archiver</a>';
		}
		if ($commandes !== null)
			echo '<div id="commandesPublication">'.implode(' ', $commandes).'</div>';
	?>
	<span class="icone valider" title="Enregistrer" onclick="return adminImage_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration/images';"></span>
	<?php
		if (isset($theImage) && ($theImage->IsArchive() || ($theImage->IsBrouillon())))
			echo '<span class="icone delete" title="Supprimer" onclick="return adminImage_supprimer();"></span>';
	?>
</div>
<div id="breadcrumb">
	<?php
		$cats = '';
		if (isset($theImage))
			$cats .= '?categories='.$theImage->GetCategoriesIds();
	?>
	<a href="/administration"><span class="icone adminHome" title="Accueil administration"></span> Administration</a> &gt; <a href="/administration/images<?php echo $cats; ?>">Les images</a> &gt;
	<?php
		if (isset($theImage))
		{
			echo 'Modifier un image';
			echo '<input type="hidden" id="image_id" value="'.(($theImage->id !== null) ? intval($theImage->id) : '').'"/>';
		}
		else
			echo 'Envoyer un image';
	?>
</div>
<form id="imageForm" action="" method="post" enctype="multipart/form-data">
	<?php
		if (isset($theImage))
		{
			$legende = $theImage->legende;
			if ($legende === null)
				$legende = '';
			$texte = 'Changer le fichier';
			$visible = (intval($theImage->visible) > 0);
		}
		else
		{
			$legende = '';
			$texte = 'Choisir le fichier';
			$visible = true;
		}
		echo '<section id="Image" class="image">';
		if (isset($theImage))
			echo '<img id="previewImage" src="/Images/I'.intval($theImage->id).'"/>';
		else
			echo '<img id="previewImage" src="/Images/noImg.png"/>';
		echo '<span>Légende de l\'image/photo</span>';
		echo '<textarea id="image_legende" placeholder="Légende de l\'image/photo (facultatif)">'.htmlentities($legende, ENT_COMPAT, 'UTF-8').'</textarea>';
		echo '</section>';
		$checked = '';
		if ($visible !== true)
			$checked = ' checked="checked"';
		echo '<input type="checkbox" id="image_masque" value="1"'.$checked.'> Image masquée<br>'.PHP_EOL;
		echo $texte;
		if (isset($theImage))
			echo '<input type="file" id="ajoutImageBrowse" onchange="adminImage_selectFile()" accept="image/png, image/jpeg, image/gif"/>';
		else
			echo '<input type="file" id="ajoutImageBrowse" multiple="multiple" onchange="adminImage_selectFile()" accept="image/png, image/jpeg, image/gif"/>';
	?>
	<progress id="imageProgress" value="0"></progress>
	<div id="message" class="message">
		<?php
			if (isset($message))
				echo nl2br(htmlentities($message, ENT_COMPAT, 'UTF-8'));
		?>
	</div>
</form>
<div class="commandes">
	<?php
		if ($commandes !== null)
			echo '<div id="commandesPublication">'.implode(' ', $commandes).'</div>';
	?>
	<span class="icone valider" title="Enregistrer" onclick="return adminImage_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration/images';"></span>
	<?php
		if (isset($theImage) && ($theImage->IsArchive() || ($theImage->IsBrouillon())))
			echo '<span class="icone delete" title="Supprimer" onclick="return adminImage_supprimer();"></span>';
	?>
</div>
<div class="sectionBottom"></div>