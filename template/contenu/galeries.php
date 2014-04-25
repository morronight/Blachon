<article id="contenu" class="galeries">
	<div id="breadcrumb">
		<a href="/">Accueil</a> &gt; Liste des galeries
	</div>
	<?php
		include("template/template_galerie.php");
		if (!isset($theGaleries))
			$theGaleries = afficheGaleries();
		else
			afficheGaleries($theGaleries);
	?>
</article>