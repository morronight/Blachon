<?php
	require_once 'include/camsii/Article.php';

	$articles = Article::GetListe(null, true, true);
	foreach ($articles as $article)
	{
		$done = null;
		//if (intval($article->id) == 64)
		$done = migrationArticle($article, true);
		echo 'Article '.intval($article->id).' : '.(($done === true) ? 'Ok' : 'Ko').'<br>'.PHP_EOL;
	}
	//$mode = 'article';
	//include 'index.php';
	
	function MigrationTexteGras($matches)
	{
		if (count($matches) < 2)
			return $matches[0];
		return '(b)"'.$matches[1].'"';
	}

	function MigrationLienHtml($matches)
	{
		$str = $matches[1].'://'.$matches[2].$matches[3].$matches[4].$matches[5];
		if (($matches[6] != '') && ($matches[6] != $matches[2].$matches[3]) && ($matches[6] != rtrim($matches[3], '/')))
			$str .= '"'.$matches[6].'"';
		return $str;
	}

	function MigrationMailHtml($matches)
	{
		$str = $matches[1].'@'.$matches[2];
		if ($matches[3] != '')
			$str .= '"'.$matches[3].'"';
		return $str;
	}

	function migrationArticle($article, $commit = true)
	{
		$erreur = false;
		$reGras = '/<b>(.+)<\/b>/i';
		$reLien = '/<a(?:\s+target="_blank")?\s+href="(http|https|ftp):\/\/([a-z0-9_\-.]+[a-z0-9])(?:(\/[a-z0-9_\-.\/]*[a-z0-9\/])?(\?.*(?=#|"))?(#.*(?="))?)?"(?:\s+title="[^"]+")?(?:\s+onclick="[^"]+")?>(.*(?=<\/a>))<\/a>/i';
		$reMail = '/<a\s+href="mailto:([a-z0-9_\-.]+[a-z0-9])@([a-z0-9_\-.]+[a-z0-9])">(.*(?=<\/a>))<\/a>/i';
		$resume = $article->resume;
		if ($resume !== null)
		{
			$resume = preg_replace_callback($reGras, 'MigrationTexteGras', $resume);
			$resume = preg_replace_callback($reLien, 'MigrationLienHtml', $resume);
			$resume = preg_replace_callback($reMail, 'MigrationMailHtml', $resume);
			$resume = preg_replace('/<br\s*\/?>/', PHP_EOL, $resume);
		}
		$changement = $article->Update($article->titre, $resume, $article->auteur, $article->id_charte, false);
		if ($changement === false)
			$erreur = true;
		if ($erreur === false)
		{
			$paragraphes =& $article->GetParagraphes();
			if ($paragraphes !== null)
			{
				foreach ($paragraphes as $paragraphe)
				{
					$titre = $paragraphe->titre;
					if ($titre !== null)
					{
						$titre = preg_replace_callback($reGras, 'MigrationTexteGras', $titre);
						$titre = preg_replace_callback($reLien, 'MigrationLienHtml', $titre);
						$titre = preg_replace_callback($reMail, 'MigrationMailHtml', $titre);
					}
					$texte = $paragraphe->texte;
					$texte = preg_replace_callback($reGras, 'MigrationTexteGras', $texte);
					$texte = preg_replace_callback($reLien, 'MigrationLienHtml', $texte);
					$texte = preg_replace_callback($reMail, 'MigrationMailHtml', $texte);
					$texte = preg_replace('/<br\s*\/?>/', PHP_EOL, $texte);
					$res = $paragraphe->Update($titre, $texte, false);
					if ($res === true)
						$changement = true;
					if ($res === false)
					{
						$erreur = true;
						break;
					}
				}
			}
		}
		if (($commit === true) && ($erreur === false))
		{
			if ($changement === true)
				$article->commit();
			else
				$article->rollback();
		}
		else
			$article->rollback();
		return !$erreur;
	}
?>