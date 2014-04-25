<?php
	if (!class_exists('Formatage'))
	{
		require_once('include/Configuration.php');
		require_once('Table.php');

		class Formatage
		{
			public static function Lien($str)
			{
				$ascii = str_replace(
					array(utf8_decode('à'), utf8_decode('â'), utf8_decode('ä')
						, utf8_decode('ê'), utf8_decode('ë'), utf8_decode('é'), utf8_decode('è')
						, utf8_decode('ï'), utf8_decode('î')
						, utf8_decode('ô'), utf8_decode('ö')
						, utf8_decode('û'), utf8_decode('ü'), utf8_decode('ù')
						, utf8_decode('ç')
					)
					, array('a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c')
					, strtolower(utf8_decode($str))
				);
				$lien = preg_replace('/[^a-z0-9\.]+/i', '-', $ascii);
				return trim($lien, '-');
			}
			
			public static function RemoveScript($texte)
			{
				$texte = preg_replace('/<script\b(?:.|\v)*<\/script>/imu', '', $texte);
				$texte = preg_replace('/javascript:/im', '', $texte);
				$texte = preg_replace('/\bon(?:abort|activate|afterprint|afterupdate|beforeactivate|beforecopy|beforecut|beforedeactivate|beforeeditfocus|beforepaste|beforeprint|beforeunload|beforeupdate|blur|bounce|cellchange|change|click|contextmenu|controlselect|copy|cut|dataavaible|datasetchanged|datasetcomplete|dblclick|deactivate|drag|dragdrop|dragend|dragenter|dragleave|dragover|dragstart|drop|error|errorupdate|filterupdate|finish|focus|focusin|focusout|help|keydown|keypress|keyup|layoutcomplete|load|losecapture|mousedown|mouseenter|mouseleave|mousemove|moveout|mouseover|mouseup|mousewheel|move|moveend|movestart|paste|propertychange|readystatechange|reset|resize|resizeend|resizestart|rowexit|rowsdelete|rowsinserted|scroll|select|selectionchange|selectstart|start|stop|submit|unload)="(?:.|\v)*"/imu', '', $texte);
				return $texte;
			}
			
			public static function FormatHtml($str, $mode = ENT_COMPAT, $eols = true, $liens = true, $listes = true, $gras = true)
			{
				$str = htmlentities($str, $mode, 'UTF-8');
				$str = Formatage::Gras($str, $gras);
				$str = Formatage::Listes($str, $listes);
				$str = Formatage::Liens($str, $liens);
				if ($eols === true)
					$str = nl2br($str);
				return $str;
			}

			public static function FormatTexte($str, $liens = true, $listes = true, $gras = true)
			{
				if ($listes === true)
					$str = Formatage::Listes($str, false);
				if ($liens === true)
					$str = Formatage::Liens($str, false);
				if ($gras === true)
					$str = Formatage::Gras($str, false);
				return $str;
			}

			public static function	Liens($str, $toHtml = true)
			{
				if (!function_exists('FormatLienUrlHtml'))
				{
					function FormatLienUrlHtml($matches)
					{
						$url_elems = parse_url(Configuration::$Url);
						$host = $url_elems['host'];
						$onclick = null;
						$title = null;
						$description = '';
/*
						ob_start();
						var_dump($matches);
						error_log(ob_get_contents());
						ob_end_clean();
*/
						if (($matches[count($matches) - 2] == '"') || ($matches[count($matches) - 2] == '&quot;'))
						{
							// Une description est utilisée
							$count = count($matches) - 2;
							$description = $matches[count($matches) - 1];
						}
						else
							$count = count($matches);
						if ($count < 3)
							return $matches[0];
						if ($count > 3)
						{
							if (($matches[2] != $_SERVER['SERVER_NAME']) && ($matches[2] != $host))
							{
								// Url externe => utilisation de target
								if ($count == count($matches))
									$description = $matches[2].'/'.$matches[3];
								if ($count > 4)
								{
									if ($count > 5)
									{
										if ($count > 6)
											return '<a target="_blank" href="'.$matches[1].'://'.$matches[2].'/'.$matches[3].$matches[4].$matches[5].rtrim($matches[6], '.').'"'.(($onclick === null) ? '' : ' onclick="'.$onclick.'"').'>'.$description.'</a>';
										else
											return '<a target="_blank" href="'.$matches[1].'://'.$matches[2].'/'.$matches[3].$matches[4].rtrim($matches[5], '.').'"'.(($onclick === null) ? '' : ' onclick="'.$onclick.'"').'>'.$description.'</a>';
									}
									else
										return '<a target="_blank" href="'.$matches[1].'://'.$matches[2].'/'.$matches[3].rtrim($matches[4], '.').'"'.(($onclick === null) ? '' : ' onclick="'.$onclick.'"').'>'.$description.'</a>';
								}
								else
									return '<a target="_blank" href="'.$matches[1].'://'.$matches[2].'/'.$matches[3].'"'.(($onclick === null) ? '' : ' onclick="'.$onclick.'"').'>'.$description.'</a>';
							}
							else
							{
								// Url interne au site
								$desc = rtrim($matches[3], '/');
								$raccourci = false;
								if (preg_match('/^articles\/([a-z0-9\-]+)$/i', $desc, $regs))
								{
									require_once 'include/camsii/Article.php';
									$articles = Article::GetListe();
									foreach($articles as $article)
									{
										if (strtolower($article->GetLien()) == strtolower($regs[1]))
										{
											$raccourci = true;
											$desc = htmlentities($article->titre, ENT_NOQUOTES, 'UTF-8');
											break;
										}
									}
								}
								if (preg_match('/^documents\/([a-z0-9\-_\.]+)$/i', $desc, $regs))
								{
									require_once 'include/camsii/Document.php';
									$documents = Document::GetListe();
									foreach($documents as $document)
									{
										if ((strtolower($document->path) == strtolower($regs[1])) || (strtolower(Formatage::Lien($document->nom).'.pdf') == strtolower(urldecode($regs[1]))))
										{
											$raccourci = true;
											$desc = htmlentities($document->nom, ENT_NOQUOTES, 'UTF-8');
											$title = 'Fichier PDF '.$document->GetReadeableSize();
											$onclick = 'if (_gaq) _gaq.push([\'_trackEvent\', \'Téléchargement\', \''.str_replace(array('"', '\''), array('\\"', '\\\''), $document->nom).'\', \'/documents/'.$document->GetLien().'\'])';
											break;
										}
									}
								}
								if ($raccourci == false)
								{
									$base = BD::OuvrirBase();
									$sql = 'SELECT * FROM page WHERE short_path = \''.$base->escapeString($desc).'\' OR real_path = \'/'.$base->escapeString($desc).'\'';
									$liste = $base->selectMany($sql, 'Page');
									$base->FermerBase();
									if ($liste && (count($liste) > 0))
										$desc = $liste[0]->description;
								}
								if ($count == count($matches))
									$description = $desc;
								if ($count > 4)
								{
									if ($count > 5)
									{
										if ($count > 6)
										{
											return '<a href="'.$matches[1].'://'.$matches[2].'/'.$matches[3].$matches[4].$matches[5].rtrim($matches[6], '.').'"'.(($title === null) ? '' : ' title="'.$title.'"').(($onclick === null) ? '' : ' onclick="'.$onclick.'"').'>'.$description.'</a>';
										}
										else
											return '<a href="'.$matches[1].'://'.$matches[2].'/'.$matches[3].$matches[4].rtrim($matches[5], '.').'"'.(($title === null) ? '' : ' title="'.$title.'"').(($onclick === null) ? '' : ' onclick="'.$onclick.'"').'>'.$description.'</a>';
									}
									else
										return '<a href="'.$matches[1].'://'.$matches[2].'/'.$matches[3].rtrim($matches[4], '.').'"'.(($title === null) ? '' : ' title="'.$title.'"').(($onclick === null) ? '' : ' onclick="'.$onclick.'"').'>'.$description.'</a>';
								}
								else
									return '<a href="'.$matches[1].'://'.$matches[2].'/'.$matches[3].'"'.(($title === null) ? '' : ' title="'.$title.'"').(($onclick === null) ? '' : ' onclick="'.$onclick.'"').'>'.$description.'</a>';
							}
						}
						else
						{
							// Url minimaliste http://www.cansii.com
							$description = $matches[2];
							if (($matches[2] != $_SERVER['SERVER_NAME']) && ($matches[2] != $host))
								return '<a target="_blank" href="'.$matches[1].'://'.$matches[2].'"'.(($onclick === null) ? '' : ' onclick="'.$onclick.'"').'>'.$description.'</a>';
							else
								return '<a href="'.$matches[1].'://'.$matches[2].'"'.(($onclick === null) ? '' : ' onclick="'.$onclick.'"').'>'.$description.'</a>';
						}
					}
				}
				if (!function_exists('FormatLienUrlTexte'))
				{
					function FormatLienUrlTexte($matches)
					{
						$url_elems = parse_url(Configuration::$Url);
						$host = $url_elems['host'];
						$onclick = null;
						$title = null;
						$description = '';
						if ((substr($matches[count($matches) - 2], 0, 1) == '"') || (substr($matches[count($matches) - 2], 0, 6) == '&quot;'))
						{
							// Une description est utilisée
							$count = count($matches) - 2;
							$description = $matches[count($matches) - 1];
						}
						else
							$count = count($matches);
						if ($count < 3)
							return $matches[0];
						if ($count > 3)
						{
							if (($matches[2] != $_SERVER['SERVER_NAME']) && ($matches[2] != $host))
							{
								// Url externe => utilisation de target
								if ($count == count($matches))
									$description = $matches[2].'/'.$matches[3];
								return $description;
							}
							else
							{
								// Url interne au site
								$desc = rtrim($matches[3], '/');
								$raccourci = false;
								if (preg_match('/^articles\/([a-z0-9\-]+)$/i', $desc, $regs))
								{
									require_once 'include/camsii/Article.php';
									$articles = Article::GetListe();
									foreach($articles as $article)
									{
										if (strtolower($article->GetLien()) == strtolower($regs[1]))
										{
											$raccourci = true;
											$desc = htmlentities($article->titre, ENT_NOQUOTES, 'UTF-8');
											break;
										}
									}
								}
								if (preg_match('/^documents\/([a-z0-9\-_\.]+)$/i', $desc, $regs))
								{
									require_once 'include/camsii/Document.php';
									$documents = Document::GetListe();
									foreach($documents as $document)
									{
										if ((strtolower($document->path) == strtolower($regs[1])) || (strtolower(Formatage::Lien($document->nom)) == strtolower(urldecode($regs[1]))))
										{
											$raccourci = true;
											$desc = htmlentities($document->nom, ENT_NOQUOTES, 'UTF-8');
											$title = 'Fichier PDF '.$document->GetReadeableSize();
											$onclick = 'general_trackDocument(this.href, this.innerHTML);';
											break;
										}
									}
								}
								if ($raccourci == false)
								{
									$base = BD::OuvrirBase();
									$sql = 'SELECT * FROM page WHERE short_path = \''.$base->escapeString($desc).'\' OR real_path = \'/'.$base->escapeString($desc).'\'';
									$liste = $base->selectMany($sql, 'Page');
									$base->FermerBase();
									if ($liste && (count($liste) > 0))
										$desc = $liste[0]->description;
								}
								if ($count == count($matches))
									$description = $desc;
								return $description;
							}
						}
						else
						{
							// Url minimaliste http://www.cansii.com
							$description = $matches[2];
							return $description;
						}
					}
				}
				if (!function_exists('FormatLienMailToHtml'))
				{
					function FormatLienMailToHtml($matches)
					{
						if (count($matches) > 4)
							return '<a href="mailto:'.$matches[1].'@'.$matches[2].'">'.$matches[4].'</a>';
						else
							return '<a href="mailto:'.$matches[1].'@'.$matches[2].'">'.$matches[1].'@'.$matches[2].'</a>';
					}
				}
				if (!function_exists('FormatLienMailToTexte'))
				{
					function FormatLienMailToTexte($matches)
					{
						if (count($matches) > 4)
							return $matches[4];
						else
							return $matches[1].'@'.$matches[2];
					}
				}
				$re = '/(?<!\shref="|\shref=&quot;|\ssrc="|\ssrc=&quot;)(http|https|ftp):\/\/([a-z0-9_\-.]+[a-z0-9])(?:\/([a-z0-9_\-.\/]*[a-z0-9\/])?(\?.*(?=#| |\t|\n|<|"|&quot;|\z))?(#.*(?= |\t|\n|<|"|&quot;|\z))?)?(?:("|&quot;)(.+)\g{-2})?/i';
				if ($toHtml === true)
					$str = preg_replace_callback($re, 'FormatLienUrlHtml', $str);
				else
					$str = preg_replace_callback($re, 'FormatLienUrlTexte', $str);
				$re = '/(?<!href="mailto:|href=&quot;mailto:)\b([a-z0-9_\-.]+)@([a-z0-9_\-.]+\.[a-z0-9]+)(?:("|&quot;)([^"]+)\3)?/i';
				if ($toHtml === true)
					$str = preg_replace_callback($re, 'FormatLienMailToHtml', $str);
				else
					$str = preg_replace_callback($re, 'FormatLienMailToTexte', $str);
				return $str;
			}
			
			public static function	Listes($str, $toHtml = true)
			{
				if (!function_exists('FormatListesSimple'))
				{
					function FormatListesSimple($matches)
					{
						if (count($matches) < 2)
							return $matches[0];
						return '<ul><li>'.$matches[1].'</li></ul>';
					}
				}
				if (!function_exists('FormatListesOrdonneeHtml'))
				{
					function FormatListesOrdonneeHtml($matches)
					{
						if (count($matches) < 3)
							return $matches[0];
						return '<ol><li>'.$matches[2].'</li></ol>';
					}
				}
				if (!function_exists('FormatListesOrdonneeTexte'))
				{
					function FormatListesOrdonneeTexte($matches)
					{
						if (count($matches) < 3)
							return $matches[0];
						return $matches[1].' '.$matches[2].PHP_EOL;
					}
				}
				if ($toHtml === true)
				{
					/* $str = preg_replace('/<br\s*\/?>/i', "\n", $str); */
					$re = '/^(?:\([\*\+\-]\)|[\*\+\-]) (\V+)/im';
					$str = preg_replace_callback($re, 'FormatListesSimple', $str);
					$str = preg_replace('/<\/ul>(?:\v*)<ul>/im', '', $str);
					$re = '/^\(([0-9a-z]+)\) (\V+)/im';
					$str = preg_replace_callback($re, 'FormatListesOrdonneeHtml', $str);
					$str = preg_replace('/<\/ol>(?:\v*)<ol>/im', '', $str);
					//$str = preg_replace('/(?:\v*)/im', '', $str);
					//$str = nl2br($str);
				}
				else
				{
					$re = '/^\(([0-9a-z]+)\) (\V+)/im';
					$str = preg_replace_callback($re, 'FormatListesOrdonneeTexte', $str);
				}
				return $str;
			}
			
			public static function	Gras($str, $toHtml = true)
			{
				if (!function_exists('FormatTexteGrasHtml'))
				{
					function FormatTexteGrasHtml($matches)
					{
						if (count($matches) < 3)
							return $matches[0];
						return '<b>'.$matches[2].'</b>';
					}
				}
				if (!function_exists('FormatTexteGrasTexte'))
				{
					function FormatTexteGrasTexte($matches)
					{
						if (count($matches) < 3)
							return $matches[0];
						return $matches[2];
					}
				}
				$re = '/\(b\)(?:("|&quot;)(.+)\g{-2})?/im';
				if ($toHtml === true)
				{
					$str = preg_replace_callback($re, 'FormatTexteGrasHtml', $str);
					$str = preg_replace('/<b>(\s*)<\/b>/im', "$1", $str);
					$str = preg_replace('/<\/b>(\s*)<b>/im', "$1", $str);
				}
				else
					$str = preg_replace_callback($re, 'FormatTexteGrasTexte', $str);
				return $str;
			}
			
			public static function GetReadeableFileSize($taille)
			{
				$rslt = array('valeur' => intval($taille), 'unit?' => 'o');
				$unite = array('o', 'Ko', 'Mo', 'Go', 'To', 'Po');
				$p = floor(log(intval($taille), 1024));
				if (($p > 0) && isset($unite[$p]))
				{
					$rslt = array('valeur' => (intval($taille) / pow(1024, $p)), 'unit?' => $unite[$p]);
					return sprintf('%0.2f %s', $rslt['valeur'], $rslt['unit?']);
				}
				return sprintf('%d %s', $rslt['valeur'], $rslt['unit?']);
			}			
		}
	}
?>