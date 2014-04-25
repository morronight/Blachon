<article id="contenu" class="utilisateurs">
	<div id="breadcrumb">
		<a href="/administration"><span class="icone adminHome" title="Accueil administration"></span> Administration</a> &gt; Liste des utilisateurs
	</div>
	<?php
		include 'template/template_utilisateur.php';
		if (!isset($theUtilisateurs))
			$theUtilisateurs = afficheUtilisateurs();
		else
			afficheUtilisateurs($theUtilisateurs);
	?>
</article>