<?php
	require_once('include/Configuration.php');
	require_once('include/camsii/Article.php');
	require_once('include/camsii/Page.php');
	
	$pages = Page::GetListe();
	$articles = Article::GetListe();
	header('content-type: text/xml; charset=utf-8');
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php
		if (count($pages) > 0)
		{
			foreach($pages as $page)
			{
				if (intval($page->is_admin) == 0)
				{
					echo '<url>';
					echo '<loc>'.Configuration::$Url.'/'.$page->short_path.'</loc>';
					echo '</url>';
				}
			}
		}
		if (count($articles) > 0)
		{
			foreach($articles as $article)
			{
				if (!$article->IsBrouillon() && !$article->IsArchive())
				{
					echo '<url>';
					echo '<loc>'.Configuration::$Url.'/articles/'.$article->GetLien().'</loc>';
					echo '</url>';
				}
			}
		}
	?>
</urlset>