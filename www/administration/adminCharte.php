<?php
	$thisPageIsAdminPage = true;
	$isAdmin = false;
	$erreur = null;
	if (isset($_SESSION['utilisateur']) && ($_SESSION['habilitation'] == 2))
	{
		$isAdmin = true;
		require_once 'include/camsii/Charte.php';
		require_once 'include/camsii/CSS.php';
		$error = null;
		$changement = false;
		$charte = null;
		if (isset($_REQUEST['charte']))
			$charte = intval($_REQUEST['charte']);
		$action = null;
		if (isset($_REQUEST['action']))
			$action = strtolower($_REQUEST['action']);

		if (($action !== null) && ($charte !== false))
		{
			switch($action)
			{
			case 'ajoutercss':
				$css = null;
				if (isset($_REQUEST['css']))
					$css = $_REQUEST['css'];
				$ordre = null;
				if (isset($_REQUEST['ordre']))
					$ordre = $_REQUEST['ordre'];
				$user_agent = null;
				if (isset($_REQUEST['user_agent']))
					$user_agent = $_REQUEST['user_agent'];
				
				$cssA = new CSS();
				if ($cssA->Insert($charte, $css, $ordre+1, $user_agent) === false)
				{
					$erreur = 'Erreur lors l\ajout du css.';
					$error = 1;
					
				}
				else
				{
					$erreur = 'ajout réussie.';
					exit();
				}
				break;
			case 'changerordrecss':
				$css = null;
				if (isset($_REQUEST['css']))
					$css = $_REQUEST['css'];
				$ordre = null;
				if (isset($_REQUEST['ordre']))
					$ordre = $_REQUEST['ordre'];
				$cssA = new CSS();
				if ($cssA->Update($charte, $css, $ordre) === null)
				{
					$erreur = 'Erreur lors du changement d\'ordre du css.';
					$error = 1;
					
				}
				else
				{
					$erreur = 'ajout réussie.';
					exit();
				}
				break;
			case 'supprimercss':
				if ($charte !== null)
				{
					$css = null;
					if (isset($_REQUEST['css']))
						$css = $_REQUEST['css'];
					$cssD = new CSS;
					$changement = $cssD->Delete($charte, $css);
					if ($changement === false)
						$erreur = 'Erreur lors de la suppression du CSS.';
					else
					{
						$erreur = 'Suppression réussie.';
						exit();
					}
				}
				else
					$erreur = 'CSS non trouvé.';
				break;
			case 'dupliquercharte':
				$nomcharte = null;
				if (isset($_REQUEST['nomcharte']))
					$nomcharte = $_REQUEST['nomcharte'];
				$getCharte = Charte::Get($charte);
				$getCharte->DupliquerCharte($nomcharte, false);
				if($getCharte === false)
					$erreur = 'Erreur lors de la duplication de la charte.';
				else
				{
					$erreur = 'Charte dupliquée.';
					exit();
				}
				break;
			default:
			case 'supprimercharte':
				if ($charte !== null)
				{
					$getCharte = Charte::Get($charte);
					$changement = $getCharte->Delete($charte);
					if ($changement === false)
						$erreur = 'Erreur lors de la suppression du CSS.';
					else
					{
						$erreur = 'Suppression réussie.';
						exit();
					}
				}
				else
					$erreur = 'CSS non trouvé.';
				break;
				$erreur = 'Action non reconnue.';
				break;
			}
		}
		else
		{
			if ($utilisateur === false)
				$utilisateur = null;
		}
	}
	header("HTTP/1.0 500");
	echo $erreur;
?>