<form id="utilisateurForm" action="administration/adminUtilisateur.php" method="post" onsubmit="return changeMotDePasse();">
	<section class="message">
	<?php
		if (isset($erreur) && ($erreur !== null))
			echo htmlentities($erreur, ENT_COMPAT, 'utf-8');
	?>
	</section>
	<?php
		if (isset($query_string))
			parse_str($query_string, $request);
		else
			$request = $_REQUEST;
		echo '<input type="hidden" name="utilisateur" id="utilisateur" value="'.(isset($request['id']) ? intval($request['id']) : '').'"/>';
		echo '<input type="hidden" name="key" id="key" value="'.(isset($request['key']) ? $request['key'] : '').'"/>';
		if (isset($request['id']) && isset($request['key']))
		{
			require_once 'include/camsii/Utilisateur.php';
			$u = Utilisateur::Get(intval($request['id']));
			if (($u !== null) && ($u !== false) && ($u->id !== null))
			{
				$genkey = hash("sha256", strval($u->id).$u->mail.$u->motdepasse);
				if ($request['key'] == $genkey)
					echo '<input type="hidden" name="mail" id="mail" value="'.htmlentities($u->mail, ENT_COMPAT, 'UTF-8').'"/>';
			}
		}
	?>
	<input type="hidden" name="action" id="action" value="ChangeMotDePasse"/>
	<span>Nouveau mot de passe</span>
	<input type="password" name="motdepasse" id="motdepasse" placeholder="Nouveau mot de passe" value="">
	<span>Confirmer votre nouveau mot de passe</span>
	<input type="password" name="motdepasse2" id="motdepasse2" placeholder="Confirmation du nouveau mot de passe" value="">
	<input type="submit" value="Envoyer">
</form>