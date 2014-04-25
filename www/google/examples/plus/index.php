<?php
require_once '../../src/Google_Client.php';
require_once '../../src/contrib/Google_PlusService.php';
require_once 'include/camsii/Utilisateur.php';

session_start();

$client = new Google_Client();
$client->setApplicationName("Google+ PHP Starter Application");
// Visit https://code.google.com/apis/console to generate your
// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
 $client->setClientId('395237950753-312tjni6o9sto0t80ph4t8s9ie03l8lr.apps.googleusercontent.com');
 $client->setClientSecret('X-olE7pEMFkKbztnG-0kr182');
 $client->setRedirectUri('http://pole.cansii.com/google/examples/plus/index.php');
 $client->setDeveloperKey('AIzaSyDVpfibafRmQB5l3bxV2xx8Q5Jc8A4mliQ');
$plus = new Google_PlusService($client);

if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
}

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['access_token'])) {
  $client->setAccessToken($_SESSION['access_token']);
}

if ($client->getAccessToken()) {
echo "ok";
  $me = $plus->people->get('me');

  // These fields are currently filtered through the PHP sanitize filters.
  // See http://www.php.net/manual/en/filter.filters.sanitize.php
  $url = filter_var($me['url'], FILTER_VALIDATE_URL);
  $img = filter_var($me['image']['url'], FILTER_VALIDATE_URL);
  $name = filter_var($me['displayName'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
  $personMarkup = "<a rel='me' href='$url'>$name</a><div><img src='$img'></div>";

  $optParams = array('maxResults' => 100);
  $activities = $plus->activities->listActivities('me', 'public', $optParams);
  $activityMarkup = '';
  foreach($activities['items'] as $activity) {
    // These fields are currently filtered through the PHP sanitize filters.
    // See http://www.php.net/manual/en/filter.filters.sanitize.php
    $url = filter_var($activity['url'], FILTER_VALIDATE_URL);
    $title = filter_var($activity['title'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $content = filter_var($activity['object']['content'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $activityMarkup .= "<div class='activity'><a href='$url'>$title</a><div>$content</div></div>";
  }

  // The access token may have been updated lazily.
  $_SESSION['access_token'] = $client->getAccessToken();
    if(!isset($_SESSION['utilisateur']))
	{	
		$utilisateur = Utilisateur::GetAccesGoogle($me['id']);
		if($utilisateur === false)
		{
		//	unset($_SESSION['access_token']);
			echo '<h1>Ce compte google+ n\'est associé à aucun compte du site - Redirection en cours...<h1>';
			header('Refresh: 5; URL= /administration');
			exit();
		}
		else
		{
			$_SESSION['utilisateur'] = $utilisateur->id;
			$_SESSION['habilitation'] = $utilisateur->droits;
			echo '<h1>Vous êtes bien connecté ! - Redirection en cours...<h1>';
			header('Refresh: 5; URL= /administration');
			exit();
		}
	}
	else
	{
		$utilisateur = Utilisateur::Get($_SESSION['utilisateur']);
		if($utilisateur->google_id == $me['id'])
		{
			echo '<h1>Votre compte est déjà associé à un compte google+ - Redirection en cours...<h1>';
			header('Refresh: 5; URL= /administration');
			exit();
		}
		else
			if($utilisateur->UpdateGoogleId($me['id']) === true)
			{
				echo '</h1>Compte désormais associé à votre compte google+ - Redirection en cours...<h1>';
				header('Refresh: 5; URL= /administration');
				exit();
			}
			else
			{
				echo '<h1>Erreur lors de l\'association à votre compte google+ - Redirection en cours...<h1>';
				header('Refresh: 5; URL= /administration');
				exit();
			}
	}
} else {
  $authUrl = $client->createAuthUrl();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <link rel='stylesheet' href='style.css' />
</head>
<body>
<header><h1>Google+ Sample App</h1></header>
<div class="box">

<?php if(isset($personMarkup)): ?>
<div class="me"><?php print $personMarkup ?></div>
<?php endif ?>

<?php if(isset($activityMarkup)): ?>
<div class="activities">Your Activities: <?php print $activityMarkup ?></div>
<?php endif ?>

<?php
  if(isset($authUrl)) {
    print "<a class='login' href='$authUrl'>Connect Me!</a>";
  } else {
   print "<a class='logout' href='?logout'>Logout</a>";
  }
?>
</div>
</body>
</html>