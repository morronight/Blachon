<div class="commandes">
	<?php
		$commandes = null;
		if (isset($theVideo))
		{
			if ($theVideo->IsBrouillon())
				$commandes[] = '<a href="/administration/adminVideo.php?video='.intval($theVideo->id).'&amp;action=publier" onclick="return adminVideo_action(this)">Publier</a>';
			else
				$commandes[] = '<a href="/administration/adminVideo.php?video='.intval($theVideo->id).'&amp;action=depublier" onclick="return adminVideo_action(this)">Dépublier</a>';
			if ($theVideo->IsArchive())
				$commandes[] = '<a href="/administration/adminVideo.php?video='.intval($theVideo->id).'&amp;action=desarchiver" onclick="return adminVideo_action(this)">Désarchiver</a>';
			else
				$commandes[] = '<a href="/administration/adminVideo.php?video='.intval($theVideo->id).'&amp;action=archiver" onclick="return adminVideo_action(this)">Archiver</a>';
		}
		if ($commandes !== null)
			echo '<div id="commandesPublication">'.implode(' ', $commandes).'</div>';
	?>
	<span class="icone valider" title="Enregistrer" onclick="return adminVideo_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration/videos';"></span>
	<?php
		if (isset($theVideo) && ($theVideo->IsArchive() || ($theVideo->IsBrouillon())))
			echo '<span class="icone delete" title="Supprimer" onclick="return adminVideo_supprimer();"></span>';
	?>
</div>
<div id="breadcrumb">
	<?php
		$cats = '';
		if (isset($theVideo))
			$cats .= '?categories='.$theVideo->GetCategoriesIds();
	?>
	<a href="/administration"><span class="icone adminHome" title="Accueil administration"></span> Administration</a> &gt; <a href="/administration/videos<?php echo $cats; ?>">Les vidéos</a> &gt;
	<?php
		if (isset($theVideo))
		{
			echo 'Modifier une vidéo';
			echo '<input type="hidden" id="video_id" value="'.(($theVideo->id !== null) ? intval($theVideo->id) : '').'"/>';
		}
		else
			echo 'Ajouter une vidéo';
	?>
</div>
<form id="videoForm" action="" method="post" enctype="multipart/form-data">
	<?php
		if (isset($theVideo))
		{
			$url = $theVideo->url;
			$description = $theVideo->description;
			if ($description === null)
				$description = '';
			$visible = (intval($theVideo->visible) > 0);
			echo $theVideo->Format();
		}
		else
		{
			$url = '';
			$description = '';
			$visible = true;
		}
		echo '<section id="Video" class="video">';		
		echo '<span>Adresse de la vidéo (Dailymotion ou Youtube)</span>';
		echo '<input type="text" id="video_url" placeholder="Adresse de la vidéo" value="'.htmlentities($url, ENT_COMPAT, 'UTF-8').'" maxlenght="255">';
		echo '<span>Description de la vidéo</span>';
		echo '<textarea id="video_description" placeholder="Description de la vidéo (facultatif)">'.htmlentities($description, ENT_COMPAT, 'UTF-8').'</textarea>';
		$checked = '';
		if ($visible !== true)
			$checked = ' checked="checked"';
		echo '<input type="checkbox" id="video_masque" value="1"'.$checked.'> Vidéo masquée<br>'.PHP_EOL;
		//if ($url != '')
			//echo '<a id="videoPreview" target="_blank" href="'.Configuration::$Url.'/videos/'.Formatage::Lien($url).'">'.Configuration::$Url.'/videos/'.Formatage::Lien($url).'</a><br>';
		echo '</section>';
	?>
</form>
<div class="commandes">
	<?php
		if ($commandes !== null)
			echo '<div id="commandesPublication">'.implode(' ', $commandes).'</div>';
	?>
	<span class="icone valider" title="Enregistrer" onclick="return adminVideo_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration/videos';"></span>
	<?php
		if (isset($theVideo) && ($theVideo->IsArchive() || ($theVideo->IsBrouillon())))
			echo '<span class="icone delete" title="Supprimer" onclick="return adminVideo_supprimer();"></span>';
	?>
</div>
<div class="sectionBottom"></div>