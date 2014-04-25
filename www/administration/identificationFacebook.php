<?php
	require_once 'include/facebook/src/facebook.php';
	require_once 'include/camsii/Utilisateur.php';

	$action = null;
	if(isset($_REQUEST['action']))
		$action = $_REQUEST['action'];
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
	  'appId'  => Configuration::$Facebook['appId'],
	  'secret' => Configuration::$Facebook['secret'],
	));
	$params = array( 'next' => 'http://caveblachon.fr/administration/' ); 

	// Get User ID
	$user = $facebook->getUser();

	// We may or may not have this data based on whether the user is logged in.
	//
	// If we have a $user id here, it means we know the user is logged into
	// Facebook, but we don't know if the access token is valid. An access
	// token is invalid if the user logged out of Facebook.
	
	if ($action == 'deconnexion') {
		$logoutUrl = $facebook->getLogoutUrl($params);
		unset($_SESSION['utilisateur']);
		unset($_SESSION['access_token']);
		echo '<script language="Javascript">
			<!--
			document.location.replace("'.$logoutUrl.'");
			// -->
			</script>';
			exit();
	}


	if ($user) {
		$_SESSION['user'] = $user_id;
	  try {
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me');
		if(!isset($_SESSION['utilisateur']))
			{	
				$utilisateur = Utilisateur::GetAccesFacebook($user_profile['id']);
				if($utilisateur === false)
				{
					unset($_SESSION['access_token']);
					echo '<h1>Ce compte facebook n\'est associé aucun compte du site - Redirection en cours...<h1>';
					echo '<script language="Javascript">
					document.location.replace("/administration");
					</script>';
					exit();
				}
				else
				{
					$_SESSION['utilisateur'] = $utilisateur->id;
					$_SESSION['habilitation'] = $utilisateur->droits;
					echo '<h1>Vous êtes bien connectés - Redirection en cours...<h1>';
					echo '<script language="Javascript">
					document.location.replace("/administration");
					</script>';

					exit();
				}
			}
			else
			{
				$utilisateur = Utilisateur::Get($_SESSION['utilisateur']);
				if($utilisateur->facebook_id == $user_profile['id'])
				{
					echo '<h1>Votre compte est déjà associé un compte facebook - Redirection en cours...<h1>';
					echo '<script language="Javascript">
					document.location.replace("/administration");
					</script>';
					exit();
				}
				else
					if($utilisateur->UpdateFacebookId($user_profile['id']) === true)
					{
						echo '</h1>Compte dérmais associé votre compte facebook - Redirection en cours...<h1>';
						echo '<script language="Javascript">
					document.location.replace("/administration");
					</script>';
						exit();
					}
					else
					{
						unset($_SESSION['access_token']);
						echo '<h1>Erreur lors de l\'association ࡶotre compte facebook - Redirection en cours...<h1>';
						echo '<script language="Javascript">
					<!--
					document.location.replace("/administration");
					// -->
					</script>';
						exit();
					}
			}
	  } catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	  }
	}
	else
	{
    $loginUrl = $facebook->getLoginUrl(array(
        'scope' => 'publish_stream'));
     echo '<script language="Javascript">
			<!--
			document.location.replace("'.$loginUrl.'");
			// -->
			</script>';
	}


	// This call will always work since we are fetching public data.
	$naitik = $facebook->api('/naitik');
?>
