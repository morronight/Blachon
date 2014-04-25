<?php
	require_once 'include/Configuration.php';
	require_once 'include/camsii/Galerie.php';

	$isAdmin = false;
	if (isset($_SESSION['habilitation']) && (1 <= intval($_SESSION['habilitation'])))
		$isAdmin = true;

	if ($isAdmin)
	{
		ob_start();
		require 'template/contenu/adminGaleries.php';
		ob_end_clean();

		$archive = null;
		if (isset($_REQUEST['archives']))
			$archive = (intval($_REQUEST['archives']) > 0) ? true : false;
		$brouillon = null;
		if (isset($_REQUEST['brouillons']))
			$brouillon = (intval($_REQUEST['brouillons']) > 0) ? true : false;
		$categorieIds = null;
		if (isset($_REQUEST['categories']))
			$categorieIds = explode(',', $_REQUEST['categories']);
		$filtre = null;
		if (isset($_REQUEST['filtre']))
			$filtre = $_REQUEST['filtre'];
		$masque = true;
		if (isset($_REQUEST['masques']))
			$masque = (intval($_REQUEST['masques']) > 0) ? null : true;

		afficheGaleries(null, $categorieIds, $brouillon, $archive, $filtre, $masque);
	}
?>