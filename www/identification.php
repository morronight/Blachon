<?php
	$thisPageIsAdminPage = true;
	require_once 'include/camsii/Utilisateur.php';
	require_once 'include/camsii/Page.php';

	if (isset($_SESSION['key']) && isset($_REQUEST['identifiant']) && isset($_REQUEST['cipher']))
	{
		$key = $_SESSION['key'];
		//$identifiant = str_replace(array('#3d', '#26', '#23'), array('=', '&', '#'), $_GET['identifiant']);
		$identifiant = $_REQUEST['identifiant'];
		$cipher = $_REQUEST['cipher'];
		$utilisateur = Utilisateur::Identify($identifiant, $key, $cipher);
		if ($utilisateur !== null)
		{
			$_SESSION['utilisateur'] = $utilisateur->id;
			$_SESSION['habilitation'] = $utilisateur->droits;
			header("HTTP/1.0 200");
			$mode = 'page';
			$thePage = Page::Get(2);
			if (isset($_REQUEST['methode']) && ($_REQUEST['methode'] == 'ajax'))
			{
				echo 'Identification réussie';
				exit();
			}
			if (isset($thePage->query_string) && ($thePage->query_string !== null))
				$query_string = urldecode(basename($thePage->query_string));
			$path = realpath(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].$thePage->real_path));
			include($path);
			exit();
		}
	}
	unset($_SESSION['utilisateur']);
	unset($_SESSION['habilitation']);
	sleep(2);
	header("HTTP/1.0 403");
	$erreur = 'Identifiant ou mot de passe incorrect.';
	if (isset($_REQUEST['methode']) && ($_REQUEST['methode'] == 'ajax'))
	{
		echo $erreur;
		exit();
	}
	$mode = 'page';
	$thePage = Page::Get(4);
	if (isset($thePage->query_string) && ($thePage->query_string !== null))
		$query_string = urldecode(basename($thePage->query_string));
	$path = realpath(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].$thePage->real_path));
	include($path);	
?>