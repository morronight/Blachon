<div class="commandes">
	<?php
		$commandes = null;
		if (isset($theGalerie))
		{
			if ($theGalerie->IsBrouillon())
				$commandes[] = '<a href="/administration/adminGalerie.php?galerie='.intval($theGalerie->id).'&amp;action=publier" onclick="return adminGalerie_action(this)">Publier</a>';
			else
				$commandes[] = '<a href="/administration/adminGalerie.php?galerie='.intval($theGalerie->id).'&amp;action=depublier" onclick="return adminGalerie_action(this)">Dépublier</a>';
			if ($theGalerie->IsArchive())
				$commandes[] = '<a href="/administration/adminGalerie.php?galerie='.intval($theGalerie->id).'&amp;action=desarchiver" onclick="return adminGalerie_action(this)">Désarchiver</a>';
			else
				$commandes[] = '<a href="/administration/adminGalerie.php?galerie='.intval($theGalerie->id).'&amp;action=archiver" onclick="return adminGalerie_action(this)">Archiver</a>';
		}
		if ($commandes !== null)
			echo '<div id="commandesPublication">'.implode(' ', $commandes).'</div>';
	?>
	<span class="icone valider" title="Enregistrer" onclick="return adminGalerie_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration/galeries';"></span>
	<?php
		if (isset($theGalerie) && ($theGalerie->IsArchive() || ($theGalerie->IsBrouillon())))
			echo '<span class="icone delete" title="Supprimer" onclick="return adminGalerie_supprimer();"></span>';
	?>
</div>
<div id="breadcrumb">
	<?php
		$cats = '';
		if (isset($theGalerie))
			$cats .= '?categories='.$theGalerie->GetCategoriesIds();
	?>
	<a href="/administration"><span class="icone adminHome" title="Accueil administration"></span> Administration</a> &gt; <a href="/administration/galeries<?php echo $cats; ?>">Les galeries</a> &gt;
	<?php
		if (isset($theGalerie))
		{
			echo 'Modifier une galerie';
			echo '<input type="hidden" id="galerie_id" value="'.(($theGalerie->id !== null) ? intval($theGalerie->id) : '').'"/>';
		}
		else
			echo 'Créer une galerie';
	?>
</div>
<form id="galerieForm" action="" method="post" enctype="multipart/form-data">
	<?php
		if (isset($theGalerie))
		{
			$nom = $theGalerie->nom;
			$description = $theGalerie->description;
			if ($description === null)
				$description = '';
			$visible = (intval($theGalerie->visible) > 0);
		}
		else
		{
			$nom = '';
			$description = '';
			$visible = true;
		}
		echo '<section id="Galerie" class="galerie">';
		echo '<span>Nom de la galerie</span>';
		echo '<input type="text" id="galerie_nom" placeholder="Nom de la galerie" value="'.htmlentities($nom, ENT_COMPAT, 'UTF-8').'" maxlenght="64">';
		echo '<span>Description de la galerie</span>';
		echo '<textarea id="galerie_description" placeholder="Description de la galerie (facultatif)">'.htmlentities($description, ENT_COMPAT, 'UTF-8').'</textarea>';
		$checked = '';
		if ($visible !== true)
			$checked = ' checked="checked"';
		echo '<input type="checkbox" id="galerie_masque" value="1"'.$checked.'> Galerie masquée<br>'.PHP_EOL;
		if ($nom != '')
			echo '<a id="galeriePreview" target="_blank" href="'.Configuration::$Url.'/galeries/'.Formatage::Lien($nom).'">'.Configuration::$Url.'/galeries/'.Formatage::Lien($nom).'</a><br>';
		echo '<span>Images/Photos de la galerie</span>';
		echo '<div class="galerie" id="imagesGalerie">';
		if (isset($theGalerie))
		{
			$images = $theGalerie->GetImages();
			if (($images !== null) && (count($images) > 0))
			{
				foreach($images as $image)
				{
					$width = 200;
					$filepath = $image->GetFilePath();
					if (is_file($filepath))
					{
						echo '<div class="imageGalerie">';
						list($w, $h) = getimagesize($filepath);
						$height = intval(1. * $h * $width / $w);
						if ($height > 200)
						{
							$height = 200;
							$width = intval(1.* $w * $height / $h);
						}
						$titre = '';
						if ($image->legende !== null)
							$titre = htmlentities($image->legende, ENT_NOQUOTES, 'UTF-8');
						echo '<img id="image_'.intval($image->id).'" src="/Images/I'.intval($image->id).'_'.$width.'_'.$height.'" alt="'.$titre.'" title="'.$titre.'"/>';
						echo '<span class="icone delete" title="Supprimer de la galerie" onclick="return adminGalerie_supprimeImageGalerie('.intval($image->id).');"></span>';
						echo '</div>';
					}
				}
			}
		}
		echo '</div>';
		echo '<input type="button" value="Sélectionner des images/photos" onclick="adminGalerie_selectImages();"><br>'.PHP_EOL;
		echo '</section>';
	?>
</form>
<div class="commandes">
	<?php
		if ($commandes !== null)
			echo '<div id="commandesPublication">'.implode(' ', $commandes).'</div>';
	?>
	<span class="icone valider" title="Enregistrer" onclick="return adminGalerie_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration/galeries';"></span>
	<?php
		if (isset($theGalerie) && ($theGalerie->IsArchive() || ($theGalerie->IsBrouillon())))
			echo '<span class="icone delete" title="Supprimer" onclick="return adminGalerie_supprimer();"></span>';
	?>
</div>
<div class="sectionBottom"></div>
<div id="photoSelector"></div>