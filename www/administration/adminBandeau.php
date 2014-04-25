<?php
	$thisPageIsAdminPage = true;
	$isAdmin = false;
	if (isset($_SESSION['habilitation']) && (1 <= intval($_SESSION['habilitation'])))
	{
		$isAdmin = true;
		require_once 'include/Bandeau.php';
		require_once 'include/camsii/Document.php';
		require_once 'include/camsii/Article.php';

		$error = null;
		$erreur = null;
		$changement = false;
		$action = null;
		if (isset($_REQUEST['action']))
			$action = strtolower($_REQUEST['action']);
		$bandeau = Bandeau::Get();
		if (($action !== null) && ($bandeau !== false))
		{
			switch($action)
			{
			case 'modifier':
				$texte = '';
				if (isset($_REQUEST['texte']))
					$texte = Formatage::RemoveScript($_REQUEST['texte']);
				$documentId = null;
				if (isset($_REQUEST['document']) && (intval($_REQUEST['document']) > 0))
					$documentId = intval($_REQUEST['document']);
				if ($documentId !== null)
				{
					$document = Document::Get($documentId);
					if (($document !== false) && ($document !== null))
						$texte = Configuration::$Url.'/documents/'.$document->GetLien().'"'.$document->nom.'"';
				}
				if (isset($_REQUEST['article']) && (intval($_REQUEST['article']) > 0))
					$articleId = intval($_REQUEST['article']);
				if ($articleId !== null)
				{
					$article = Article::Get($articleId);
					if (($article !== false) && ($article !== null))
						$texte = Configuration::$Url.'/articles/'.$article->GetLien().'"'.$article->titre.'"';
				}
				$actif = 0;
				if (isset($_REQUEST['actif']))
					$actif = intval($_REQUEST['actif']);
				$changement = $bandeau->Update($texte, $actif, true);
				if ($changement !== false)
				{
					if ($changement === true)
						$erreur = 'Enregistrement réussi.';
					else
						$erreur = 'Bandeau non modifié, il n\'y a aucun changement.';
					include 'template/contenu/adminBandeauContenu.php';
					exit();
				}
				else
					$erreur = 'Erreur lors de la modification du bandeau.';
				break;
			default:
				$erreur = 'Action non reconnue.';
				break;
			}
		}
	}
	header("HTTP/1.0 500");
	echo $erreur;
?>