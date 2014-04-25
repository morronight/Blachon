<?php
	require_once 'include/camsii/Utilisateur.php';
	if (!isset($theUtilisateur) && isset($request) && isset($request['utilisateur']))
		$theUtilisateur = Utilisateur::Get(intval($request['utilisateur']));
	if (isset($theUtilisateur) && (($theUtilisateur === null) || ($theUtilisateur === false)))
		$theUtilisateur = new Utilisateur();
?>
<script type="text/javascript" src="/Scripts/adminUtilisateur.js"></script>
<article id="contenu" class="article">
	<?php
		include 'adminUtilisateurContenu.php';
	?>
</article>
