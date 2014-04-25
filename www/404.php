<?php
	require_once 'include/camsii/Page.php';
	$pages = Page::GetListe();
	$realDocRoot = realpath($_SERVER['DOCUMENT_ROOT']).'/';
	$thePage = null;

	if (!function_exists('is_mobile'))
	{
		function is_mobile()
		{
			if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']))
				return true;
			if (isset ($_SERVER['HTTP_ACCEPT']))
			{
				$accept = strtolower($_SERVER['HTTP_ACCEPT']);
				if (strpos($accept, 'wap') !== false)
					return true;
			}
			if (isset ($_SERVER['HTTP_USER_AGENT']))
			{
				if (strpos ($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false)
					return true;
				if (strpos ($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false)
					return true;
			}
			return false;
		}
	}

	$_SESSION['mobile'] = false;
	if (preg_match('/^\/Css\/([a-zA-Z0-9-]+_[0-9ab-]+\.css)$/', $_SERVER['REDIRECT_URL'], $regs) == 1)
	{
		$path = realpath(str_replace('//', '/', Configuration::$Css['cache'].$regs[1]));
		if ((Configuration::$Css['cache'] == substr($path, 0, strlen(Configuration::$Css['cache']))) && (is_file($path)))
		{
			header("HTTP/1.0 200");
			header("Status: 200 OK");
			header('Content-type: text/css');
			include($path);
		}
		echo '';
		exit();
	}
	foreach($pages as $page)
	{
		if ((strtolower('/'.$page->short_path) == (strtolower($_SERVER['REDIRECT_URL']))))
		{
			if ($page->cache_path !== null)
			{
				$path = realpath(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].$page->cache_path));
				if (($realDocRoot == substr($path, 0, strlen($realDocRoot))) && (is_file($path)))
				{
					$thePage = $page;
					header("HTTP/1.0 200");
					header("Status: 200 OK");
					include($path);
					exit();
				}
			}
			if (preg_match('/^http[s]?:\/\//', $page->real_path) == 0)
			{
				$path = realpath(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].$page->real_path));
				if (($realDocRoot == substr($path, 0, strlen($realDocRoot))) && (is_file($path)))
				{
					$mode = 'page';
					$thePage = $page;
					header("HTTP/1.0 200");
					header("Status: 200 OK");
					$query_string = '';
					if (isset($_SERVER['REDIRECT_QUERY_STRING']) && ($_SERVER['REDIRECT_QUERY_STRING'] !== ''))
						$query_string .= urldecode($_SERVER['REDIRECT_QUERY_STRING']);
					if (isset($page->query_string) && ($page->query_string !== null))
					{
						$query_string .= '&'.urldecode(basename($page->query_string));
						parse_str(urldecode(basename($page->query_string)), $req);
						if (!isset($req['page']) && isset($req['article']))
							$mode = 'article';
					}
					include($path);
					exit();
				}
			}
			else
			{
				header('location:'.$page->real_path);
				exit();
			}
			break;
		}
	}
	
	if (strtolower(dirname($_SERVER['REDIRECT_URL'])) == '/articles')
	{
		$mode = 'article';
		header("HTTP/1.0 200");
		header("Status: 200 OK");
		$query_string = urldecode(basename($_SERVER['REDIRECT_URL']));
		require_once 'include/camsii/Article.php';
		$articles = Article::GetListe();
		$theArticle = null;
		foreach($articles as $article)
		{
			if (strtolower($article->GetLien()) == strtolower($query_string))
			{
				$theArticle = $article;
				break;
			}
		}
		if ($theArticle !== null)
		{
			$article = $theArticle;
			include 'www/index.php';
			exit();
		}
	}

	if (strtolower(dirname($_SERVER['REDIRECT_URL'])) == '/documents')
	{
		if (preg_match('/^\/documents\/([^\/.]+)(?:\.pdf)?$/', $_SERVER['REDIRECT_URL'], $regs) == 1)
		{
			require_once('include/camsii/Article.php');
			require_once('include/camsii/Document.php');
			
			$name = null;
			if (isset($regs[1]))
				$name = $regs[1];
			$erreur = 'Document introuvable.';
			if ($name !== null)
			{
				$document = Document::GetByPath($name.'.pdf');
				if ($document !== false)
				{
					$filepath = $document->GetFilePath();
					if ($filepath !== null)
					{
						$requestHeaders = apache_request_headers();				
						if (isset($requestHeaders['If-Modified-Since']))
						{
							if (strpos($requestHeaders['If-Modified-Since'], ';') === false)
								$ifModified = new DateTime($requestHeaders['If-Modified-Since']);
							else
								$ifModified = new DateTime(strstr($requestHeaders['If-Modified-Since'], ';', true));
							$fileModifed = filemtime($filepath);
							if ($ifModified->format('U') >= $fileModifed)
							{
								header("HTTP/1.0 304");
								header("Status: 304 Not Modified");
								header('Content-Type: application/pdf');
								header('Cache-Control: public, max-age='.(3600 * 24 * 30));
								header('Expires: '.date('r', mktime() + 3600 * 24 * 30));
								exit();
							}
						}
						header("HTTP/1.0 200");
						header("Status: 200 OK");
						header('Content-Type: application/pdf');
						header('Cache-Control: public, max-age='.(3600 * 24 * 30));
						header('Pragma: Cache');
						header('Expires: '.date('r', mktime() + 3600 * 24 * 30));
						header('Last-Modified: '.date('r', filemtime($filepath)));
						if (array_search('mod_xsendfile', apache_get_modules()) !== false)
							header('X-Sendfile: '.$filepath);
						else
							readfile($filepath);
						exit();
					}
					else
						$erreur = 'Fichier "'.$name.'.pdf" introuvable.';
				}
				else
					$erreur = 'Document "'.$name.'" introuvable.';
			}
			if ($erreur !== null)
				echo $erreur;
			exit();
		}
	}
	
	if (strtolower(dirname($_SERVER['REDIRECT_URL'])) == '/images')
	{
		if (preg_match('/^\/Images\/I([0-9]+)(?:_([0-9]*)(?:_([0-9]+))?)?(?:\.(jpe?g|gif|png))?$/', $_SERVER['REDIRECT_URL'], $regs) == 1)
		{
			require_once('include/camsii/Image.php');

			$id = null;
			if (isset($regs[1]))
				$id = intval($regs[1]);
			$width = null;
			if (isset($regs[2]) && !empty($regs[2]))
				$width = intval($regs[2]);
			$height = null;	
			if (isset($regs[3]) && !empty($regs[3]))
				$height = intval($regs[3]);
			$erreur = 'Image introuvable.';
			if ($id !== null)
			{
				$image = Image::Get($id);
				if ($image !== false)
				{
					$filepath = null;
					if (($width !== null) || ($height !== null))
						$filepath = $image->GetCachedFilePath($width, $height);
					if ($filepath === null)
						$filepath = $image->GetFilePath();
					if ($filepath !== null)
					{
						$requestHeaders = apache_request_headers();				
						if (isset($requestHeaders['If-Modified-Since']))
						{
							if (strpos($requestHeaders['If-Modified-Since'], ';') === false)
								$ifModified = new DateTime($requestHeaders['If-Modified-Since']);
							else
								$ifModified = new DateTime(strstr($requestHeaders['If-Modified-Since'], ';', true));
							$fileModifed = filemtime($filepath);
							if ($ifModified->format('U') >= $fileModifed)
							{
								header("HTTP/1.0 304");
								header("Status: 304 Not Modified");
								header('Content-Type: '.$image->GetMimeType());
								header('Cache-Control: public, max-age='.(3600 * 24 * 30));
								header('Expires: '.date('r', mktime() + 3600 * 24 * 30));
								exit();
							}
						}
						header("HTTP/1.0 200");
						header("Status: 200 OK");
						header('Content-Type: '.$image->GetMimeType());
						header('Cache-Control: public, max-age='.(3600 * 24 * 30));
						header('Pragma: Cache');
						header('Expires: '.date('r', mktime() + 3600 * 24 * 30));
						header('Last-Modified: '.date('r', filemtime($filepath)));
						if (array_search('mod_xsendfile', apache_get_modules()) !== false)
							header('X-Sendfile: '.$filepath);
						else
							readfile($filepath);
						exit();
					}
					else
					{
						$erreur = 'L\'image "'.$image->path.'" est introuvable.';
					}
				}
				else
					$erreur = 'L\'image '.intval($id).' est introuvable.';
			}
			if ($erreur !== null)
				echo $erreur;
			exit();
		}
	}

	if (strtolower(dirname($_SERVER['REDIRECT_URL'])) == '/categories')
	{
		$query_string = urldecode(basename($page->query_string));
		require_once 'include/camsii/Categorie.php';
		require_once 'include/camsii/Article.php';
		$categories = Categorie::GetListe();
		$theCategorie = null;
		foreach($categories as $categorie)
		{
			if (strtolower(Formatage::Lien($categorie->nom)) == strtolower(basename($_SERVER['REDIRECT_URL'])))
			{
				$theCategorie = $categorie;
				break;
			}
		}
		if ($theCategorie !== null)
		{
			header("HTTP/1.0 200");
			header("Status: 200 OK");
			if ($theCategorie->id_article === null)
			{
				$mode = 'page';
				$thePage = Page::Get(6);
				$theArticles = Article::GetListe($theCategorie->id);
				if (isset($thePage->query_string) && ($thePage->query_string !== null))
					$query_string = urldecode(basename($thePage->query_string));
				$path = realpath(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].$thePage->real_path));
				$titre = $theCategorie->nom;
				include($path);
				exit();
			}
			else
			{
				$mode = 'article';
				$theArticle = Article::Get($theCategorie->id_article);
				if (isset($_SERVER['REDIRECT_QUERY_STRING']))
					$query_string = urldecode($_SERVER['REDIRECT_QUERY_STRING']);
				$article = $theArticle;
				include 'www/index.php';
				exit();
			}
		}
	}
	
	if (strtolower(dirname($_SERVER['REDIRECT_URL'])) == '/galeries')
	{
		$query_string = urldecode(basename($page->query_string));
		require_once 'include/camsii/Galerie.php';
		require_once 'include/camsii/Article.php';
		$galeries = Galerie::GetListe();
		$theGalerie = null;
		foreach($galeries as $galerie)
		{
			if (strtolower(Formatage::Lien($galerie->nom)) == strtolower(basename($_SERVER['REDIRECT_URL'])))
			{
				$theGalerie = $galerie;
				break;
			}
		}
		if ($theGalerie !== null)
		{
			header("HTTP/1.0 200");
			header("Status: 200 OK");
			$mode = 'page';
			$thePage = Page::Get(21);
			if (isset($thePage->query_string) && ($thePage->query_string !== null))
				$query_string = urldecode(basename($thePage->query_string));
			$path = realpath(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].$thePage->real_path));
			$titre = $theGalerie->nom;
			include($path);
			exit();
		}
	}
	
	$page = Page::Get(3);
	if ($page !== null)
	{
		$path = realpath(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].$page->real_path));
		if (($realDocRoot == substr($path, 0, strlen($realDocRoot))) && (is_file($path)))
		{
			$mode = 'page';
			$thePage = $page;
			if (isset($page->query_string) && ($page->query_string !== null))
				$query_string = urldecode(basename($page->query_string));
			include($path);
			exit();
		}
	}
?>