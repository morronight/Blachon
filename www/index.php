<?php
	ini_set('include_path', '.:'.realpath($_SERVER['DOCUMENT_ROOT'].'/..'));

	require_once 'include/camsii/Charte.php';
	require_once 'include/Bandeau.php';

	$thisPageIsAdminPage = false;
	$isAdmin = false;
	if (isset($_SESSION['utilisateur']))
		$isAdmin = true;
	
	if (isset($query_string))
		parse_str($query_string, $request);
	else
		$request = $_REQUEST;
	if (isset($request) && isset($request['mode']))
		$mode = $request['mode'];
	if (!isset($mode))
		$mode = null;
	switch($mode)
	{
		default:
			require_once 'include/camsii/Page.php';
			$mode = 'page';
			$thePage = Page::Get(1);
			if (isset($thePage->query_string) && ($thePage->query_string !== null))
				$query_string = urldecode(basename($thePage->query_string));
			if (isset($query_string))
				parse_str($query_string, $request);
		case 'page':
			if ($thePage)
			{
				if (intval($thePage->is_admin) > 0)
					$thisPageIsAdminPage = true;
				if (!$thisPageIsAdminPage || ($thisPageIsAdminPage && isset($_SESSION['habilitation']) && (intval($_SESSION['habilitation']) >= 1)))
				{
					$realDocRoot = realpath($_SERVER['DOCUMENT_ROOT']).'/';
					if (isset($request) && isset($request['page']) && is_file($realDocRoot.'../template/contenu/'.$request['page'].'.php'))
						$fichier = $request['page'];
					if (!isset($titre) || ($titre === null) || ($titre == ''))
						$titre = $thePage->description;
					$theCharte = $thePage->GetCharte();
				}
				else
				{
					$realDocRoot = realpath($_SERVER['DOCUMENT_ROOT']).'/';
					$thePage = Page::Get(4);
					parse_str($thePage->query_string, $request);
					$fichier = $request['page'];
					$titre = $thePage->description;
					$theCharte = $thePage->GetCharte();
				}
			}
			break;
		case 'article':
			require_once 'include/camsii/Article.php';
			$mode = 'article';
			if (!isset($article) && isset($request) && isset($request['article']))
				$article = Article::Get(intval($request['article']));
			if (!isset($article))
			{
				$article = Article::Get(1);
				if ($article === false)
					$article = null;
			}
			if ($article !== null)
			{
				$titre = $article->titre;
				$theCharte = $article->GetCharte();
			}
			break;
	}
	include 'template/template_accueil.php';
?>