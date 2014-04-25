<?php

require '../src/facebook.php';
require_once 'include/camsii/Utilisateur.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => '141680632674080',
  'secret' => '3f4828a2cf21d6498b823267555f6018',
));

// Get User ID
$user = $facebook->getUser();

// We may or may not have this data based on whether the user is logged in.
//
// If we have a $user id here, it means we know the user is logged into
// Facebook, but we don't know if the access token is valid. An access
// token is invalid if the user logged out of Facebook.
if ($user && !isset($user_profile)) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}


// This call will always work since we are fetching public data.
$naitik = $facebook->api('/naitik');

  if(isset($user_profile))
	{
	 if(!isset($_SESSION['utilisateur']))
		{	
			$utilisateur = Utilisateur::GetAccesFacebook($user_profile['id']);
			if($utilisateur === false)
			{
			//	unset($_SESSION['access_token']);
				echo '<h1>Ce compte google+ n\'est associé à aucun compte du site - Redirection en cours...<h1>';
				header('Location: /administration');
				exit();
			}
			else
			{
				$_SESSION['utilisateur'] = $utilisateur->id;
				$_SESSION['habilitation'] = $utilisateur->droits;
				echo '<h1>Vous êtes bien connecté ! - Redirection en cours...<h1>';
				header('Location: /administration');
				exit();
			}
		}
		else
		{
			$utilisateur = Utilisateur::Get($_SESSION['utilisateur']);
			if($utilisateur->google_id == $me['id'])
			{
				echo '<h1>Votre compte est déjà associé à un compte google+ - Redirection en cours...<h1>';
				header('Location: /administration');
				exit();
			}
			else
				if($utilisateur->UpdateGoogleId($user_profile['id']) === true)
				{
					echo '</h1>Compte désormais associé à votre compte google+ - Redirection en cours...<h1>';
					header('Location: /administration');
					exit();
				}
				else
				{
					echo '<h1>Erreur lors de l\'association à votre compte google+ - Redirection en cours...<h1>';
					header('Location: /administration');
					exit();
				}
		}
	}

?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>php-sdk</title>
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
    </style>
  </head>
  <body>
    <h1>php-sdk</h1>

    <?php if ($user): ?>
      <a href="<?php echo $logoutUrl; ?>">Logout</a>
    <?php else: ?>
      <div>
        Login using OAuth 2.0 handled by the PHP SDK:
        <a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
      </div>
    <?php endif ?>

    <h3>PHP Session</h3>
    <pre><?php print_r($_SESSION); ?></pre>

    <?php if ($user): ?>
      <h3>You</h3>
      <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

      <h3>Your User Object (/me)</h3>
      <pre><?php print_r($user_profile); ?></pre>
    <?php else: ?>
      <strong><em>You are not Connected.</em></strong>
    <?php endif ?>

    <h3>Public profile of Naitik</h3>
    <img src="https://graph.facebook.com/naitik/picture">
    <?php echo $naitik['name']; ?>
  </body>
</html>
