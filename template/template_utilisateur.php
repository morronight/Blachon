<?php	
	require_once 'include/camsii/Utilisateur.php';
	
	function afficheUtilisateurs($utilisateurs = null)
	{
		if ($utilisateurs === null)
			$utilisateurs = Utilisateur::GetListe();
		if ($utilisateurs !== null)
		{
			echo '<div id="liste" class="liste">';
				echo '<div class="utilisateur">';
				echo '<span>Mail</span>';
				echo '<span>Nom</span>';
				echo '<span class="commandes">';
				echo '<span class="icone addUser" title="Ajouter un utilisateur" onclick="return adminUtilisateurs_ajouterUtilisateur();"></span>';
				echo '</span>';
				echo '</div>';
				if (count($utilisateurs) > 0)
				{
					foreach ($utilisateurs as $utilisateur)
					{
						echo '<div class="utilisateur" id="utilisateur_'.intval($utilisateur->id).'">';
						echo '<span><a href="mailto:'.htmlentities($utilisateur->mail, ENT_COMPAT, 'UTF-8').'">'.htmlentities($utilisateur->mail, ENT_NOQUOTES, 'UTF-8').'</a></span>';
						echo '<span>'.htmlentities($utilisateur->pseudo, ENT_NOQUOTES, 'UTF-8').'</span>';
						echo '<span class="commandes">';
						//echo '<span class="icone motdepasse" title="Changement d\'identifiants" onclick="return adminUtilisateurs_modifierIdentifiants();"></span>';
						echo '<span class="icone edit" title="Modifier" onclick="return adminUtilisateurs_modifierUtilisateur('.intval($utilisateur->id).');"></span>';
						echo '<span class="icone resetPwd" title="Réinitialiser le mot de passe" onclick="return adminUtilisateurs_resetMotDePasse('.intval($utilisateur->id).');"></span>';
						echo '<span class="icone delete" title="Supprimer" onclick="return adminUtilisateurs_supprimerUtilisateur('.intval($utilisateur->id).');"></span>';
						echo '</span>';
						echo '</div>';
					}
				}
				else
					echo 'Aucun utilisateur trouvé';
				echo '<br/>';
				echo '<div id="outilssociaux">';
					if(!isset($_SESSION['google_access_token'])){
						echo '<a href="http://caveblachon.fr/administration/identificationGoogle.php">Associer ce compte à Google</a><br/>';
					} 
					if(!isset($_SESSION['fb_141680632674080_access_token'])){
						echo '<a href="http://caveblachon.fr/administration/identificationFacebook.php">Associer ce compte à Facebook</a><br/>';
					}
					if(!isset($_SESSION['twitter_access_token'])){
						echo '<a href="http://caveblachon.fr/administration/identificationTwitter.php?authenticate=1">Associer ce compte à Twitter</a><br/>';
					}
				echo '</div>';
			echo '</div>';
		}
	}
	
	function afficheOutilsSociaux()
	{
		echo '<div id="outilssociaux">';
		if(!isset($_SESSION['google_access_token'])){
			echo '<a href="https://accounts.google.com/ServiceLogin?service=lso&passive=1209600&continue=https://accounts.google.com/o/oauth2/auth?from_login%3D1%26response_type%3Dcode%26scope%3Dhttps://www.googleapis.com/auth/plus.login%26redirect_uri%3Dhttp://caveblachon.fr/administration/identificationGoogle.php%26access_type%3Doffline%26approval_prompt%3Dforce%26as%3Da5663d4d36a3ae3%26client_id%3D395237950753-312tjni6o9sto0t80ph4t8s9ie03l8lr.apps.googleusercontent.com%26hl%3Dfr-FR&ltmpl=popup&shdf=CmgLEhF0aGlyZFBhcnR5TG9nb1VybBoADAsSFXRoaXJkUGFydHlEaXNwbGF5TmFtZRoEVGVzdAwLEgZkb21haW4aBFRlc3QMCxIVdGhpcmRQYXJ0eURpc3BsYXlUeXBlGgdERUZBVUxUDBIDbHNvIhQsO-2cUqRk0JW6ytSfw8hFr83nESgBMhRGyUyB2Mj-MJHKh21SfWMf-PxnrA&hl=fr-FR&sarp=1&scc=1">Associer ce compte à Google</a><br/>';
		} 
		if(!isset($_SESSION['fb_141680632674080_access_token'])){
			echo '<a href="https://accounts.google.com/ServiceLogin?service=lso&passive=1209600&continue=https://accounts.google.com/o/oauth2/auth?from_login%3D1%26response_type%3Dcode%26scope%3Dhttps://www.googleapis.com/auth/plus.login%26redirect_uri%3Dhttp://caveblachon.fr/administration/identificationGoogle.php%26access_type%3Doffline%26approval_prompt%3Dforce%26as%3Da5663d4d36a3ae3%26client_id%3D395237950753-312tjni6o9sto0t80ph4t8s9ie03l8lr.apps.googleusercontent.com%26hl%3Dfr-FR&ltmpl=popup&shdf=CmgLEhF0aGlyZFBhcnR5TG9nb1VybBoADAsSFXRoaXJkUGFydHlEaXNwbGF5TmFtZRoEVGVzdAwLEgZkb21haW4aBFRlc3QMCxIVdGhpcmRQYXJ0eURpc3BsYXlUeXBlGgdERUZBVUxUDBIDbHNvIhQsO-2cUqRk0JW6ytSfw8hFr83nESgBMhRGyUyB2Mj-MJHKh21SfWMf-PxnrA&hl=fr-FR&sarp=1&scc=1">Associer ce compte à Facebook</a><br/>';
		}
		if(!isset($_SESSION['twitter_access_token'])){
			echo '<a href="https://accounts.google.com/ServiceLogin?service=lso&passive=1209600&continue=https://accounts.google.com/o/oauth2/auth?from_login%3D1%26response_type%3Dcode%26scope%3Dhttps://www.googleapis.com/auth/plus.login%26redirect_uri%3Dhttp://caveblachon.fr/administration/identificationGoogle.php%26access_type%3Doffline%26approval_prompt%3Dforce%26as%3Da5663d4d36a3ae3%26client_id%3D395237950753-312tjni6o9sto0t80ph4t8s9ie03l8lr.apps.googleusercontent.com%26hl%3Dfr-FR&ltmpl=popup&shdf=CmgLEhF0aGlyZFBhcnR5TG9nb1VybBoADAsSFXRoaXJkUGFydHlEaXNwbGF5TmFtZRoEVGVzdAwLEgZkb21haW4aBFRlc3QMCxIVdGhpcmRQYXJ0eURpc3BsYXlUeXBlGgdERUZBVUxUDBIDbHNvIhQsO-2cUqRk0JW6ytSfw8hFr83nESgBMhRGyUyB2Mj-MJHKh21SfWMf-PxnrA&hl=fr-FR&sarp=1&scc=1">Associer ce compte à Twitter</a><br/>';
		}
		echo '</div>';
	}
	
	
?>