<?php
	require_once 'include/camsii/Galerie.php';
	require_once 'include/camsii/Categorie.php';

	
?>
<script type="text/javascript" src="/Scripts/galerie.js"></script>
<article id="contenu" class="galerie">
	<div id="breadcrumb">
		<a href="/">Accueil</a> &gt; <a href="/Galeries">Galeries</a> &gt;
		<?php
			if (isset($theGalerie))
				echo htmlentities($theGalerie->nom, ENT_COMPAT, 'UTF-8');
		?>
	</div>
	<?php
		include("template/template_galerie.php");
		if (isset($theGalerie))
			afficheGalerie($theGalerie);
	?>
</article>
<div id="zoomGalerie" onclick="return galerie_fermerZoom();">
</div>
