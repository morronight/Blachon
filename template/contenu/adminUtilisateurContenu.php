<div class="commandes">
	<span class="icone valider" title="Enregistrer" onclick="return adminUtilisateur_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration/utilisateurs';"></span>
</div>
<div id="breadcrumb">
	<a href="/administration"><span class="icone adminHome" title="Accueil administration"></span> Administration</a> &gt; <a href="/administration/utilisateurs">Les utilisateurs</a> &gt;
	<?php
		if (isset($theUtilisateur))
		{
			echo 'Modifier un utilisateur';
			echo '<input type="hidden" id="utilisateur_id" value="'.(($theUtilisateur->id !== null) ? intval($theUtilisateur->id) : '').'"/>';
		}
		else
			echo 'Créer un utilisateur';
	?>
</div>
<form id="utilisateurForm" action="" method="post" enctype="multipart/form-data">
	<?php
		if (isset($theUtilisateur))
		{
			$mail = $theUtilisateur->mail;
			$pseudo = $theUtilisateur->pseudo;
			if ($pseudo === null)
				$pseudo = '';
		}
		else
		{
			$mail = '';
			$pseudo = '';
		}
		echo '<section id="Utilisateur" class="utilisateur">';
		echo '<span>Mail de l\'utilisateur</span>';
		if (isset($theUtilisateur))
			echo '<input type="text" id="utilisateur_mail" placeholder="Mail de l\'utilisateur" value="'.htmlentities($mail, ENT_COMPAT, 'UTF-8').'" maxlenght="256" readonly="readonly">';
		else
			echo '<input type="text" id="utilisateur_mail" placeholder="Mail de l\'utilisateur" value="'.htmlentities($mail, ENT_COMPAT, 'UTF-8').'" maxlenght="256">';
		echo '<span>Nom de l\'utilisateur</span>';
		echo '<input type="text" id="utilisateur_pseudo" placeholder="Nom de l\'utilisateur (facultatif)" value="'.htmlentities($pseudo, ENT_COMPAT, 'UTF-8').'" maxlenght="30">';
		echo '</section>';
	?>
</form>
<div class="commandes">
	<span class="icone valider" title="Enregistrer" onclick="return adminUtilisateur_valider();"></span>
	<span class="icone cancel" title="Annuler" onclick="window.location='/administration/utilisateurs';"></span>
</div>
<div class="sectionBottom"></div>