<?php
	require_once 'include/Configuration.php';
	require_once 'include/camsii/Article.php';
	
	$articles = Article::GetListe(array(1));
	header('Content-Type: application/rss+xml');
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<atom:link href="<?php echo Configuration::$Url; ?>/rss.php" rel="self" type="application/rss+xml"/>
		<title>Actualités de la Commune de Larnage</title>
		<description>Les actualités de la commune de Larnage</description>
		<link><?php echo Configuration::$Url; ?></link>
		<image>
			<url><?php echo Configuration::$Url; ?>/Images/Larnage.png</url>
			<link><?php echo Configuration::$Url; ?></link>
			<title>Actualités de la commune de Larnage</title>
		</image>
		<language>fr-fr</language>
		<?php
			$lastTimestamp = 0;
			ob_start();
			if (count($articles) > 0)
			{
				foreach($articles as $article)
				{
					if (!$article->IsBrouillon() && !$article->IsArchive())
					{
						echo '<item>'.PHP_EOL;
						echo '<title>'.htmlspecialchars($article->titre, ENT_COMPAT, 'UTF-8').'</title>'.PHP_EOL;
						if (strlen($article->resume) > 0)
							echo '<description>'.Formatage::FormatTexte(htmlspecialchars($article->resume, ENT_COMPAT, 'UTF-8')).'</description>'.PHP_EOL;
						echo '<link>'.Configuration::$Url.'/articles/'.$article->GetLien().'</link>'.PHP_EOL;
						echo '<guid>'.Configuration::$Url.'/articles/'.$article->GetLien().'</guid>'.PHP_EOL;
						$publication = $article->GetDatePublication();
						$timestamp = mktime(substr($article->publication, 11, 2), substr($article->publication, 14, 2), substr($article->publication, 17, 2), substr($article->publication, 5, 2), substr($article->publication, 8, 2), substr($article->publication, 0, 4));
						$lastTimestamp = max($lastTimestamp, $timestamp);
						$pubDate = date(DATE_RSS, $timestamp);
						echo '<pubDate>'.$pubDate.'</pubDate>'.PHP_EOL;
/*						$illustration = $article->GetIllustration();
						if ($illustration !== null)
						{
							echo '<image>'.PHP_EOL;
							echo '<url>'.Configuration::$Static['url'].'/Images/I'.intval($illustration->id).strtolower(substr($illustration->path, -4)).'</url>'.PHP_EOL;
							if ($illustration->legende !== null)
								echo '<title>'.$illustration->legende.'</title>'.PHP_EOL;
							else
								echo '<title>'.$illustration->titre.'</title>'.PHP_EOL;
							echo '<link>'.Configuration::$Url.'/articles/'.$article->GetLien().'</link>'.PHP_EOL;
							echo '</image>'.PHP_EOL;
						}
*/						echo '</item>'.PHP_EOL;
					}
				}
			}
			$contents = ob_get_contents();
			ob_end_clean();
			if ($lastTimestamp > 0)
				echo '<pubDate>'.date(DATE_RSS, $lastTimestamp).'</pubDate>'.PHP_EOL;
			echo $contents;
		?>
	</channel>
</rss>