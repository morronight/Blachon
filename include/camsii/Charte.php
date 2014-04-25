<?php
	if (!class_exists('Charte'))
	{
		require_once 'Table.php';
		require_once 'CSS.php';
		require_once 'Credit.php';
		require_once 'Zone.php';

		class	Charte	extends Table
		{
			/*public $Id;
			public $Lang;
			public $Nom;
			public $Modification;*/
			
			protected	$m_zones;
		
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Charte';
				$this->m_zones = null;
			}

			public function __destruct()
			{
				parent::__destruct();
			}
				
			public function DupliquerCharte($nom, $autocommit = true)
			{
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (nom, droits) VALUES (';
				$sql .= '\''.$nom.'\'';
				$sql .= ', '.$this->droits;
				$sql .= ');';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					return false;
				else
				{
					if($this->dupliquerCss($id, $autocommit) !== false)
					{
						
						$this->commit();
					}
					else
					{
						$this->rollback();
					}
				}
			}
				
			public function GetCss()
			{
				$str = '';
				$css = CSS::GetListe($this->id);
				if (is_array($css) && (count($css) > 0))
				{
					if (Configuration::$Css['compact'] !== true)
					{
						foreach($css as $file)
							$str .= $file->Format();
					}
					else
					{
						$fichier = preg_replace(array(utf8_decode('/[àâä]/i'), utf8_decode('/[êëéè]/i'), utf8_decode('/[ïî]/i'), utf8_decode('/[ôö]/i'), utf8_decode('/[ûüù]/i'), utf8_decode('/ç/i')), array('a', 'e', 'i', 'o', 'u', 'c'), utf8_decode($this->nom));
						$fichier = trim(preg_replace('/[^a-z0-9]+/i', '-', $fichier), '-');
						$fichier .= '_'.Configuration::$Version.'.css';
						$strComplementaire = '';
						if ((Configuration::$Css['force'] === true) || !is_file(Configuration::$Css['cache'].$fichier))
						{
							$realCssRoot = Configuration::$Css['location'];
							$contenuFichier = '';
							foreach($css as $file)
							{
								if ($file->user_agent === null)
								{
									if (strtolower(substr($file->fichier, -4)) == '.php')
									{
										ob_start();
										include $realCssRoot.$file->fichier;
										$contenu = ob_get_contents();
										ob_end_clean();
									}
									else
										$contenu = file_get_contents($realCssRoot.$file->fichier);
									if ($contenu !== false)
										$contenuFichier .= $contenu;
								}
								else
								{
									if (isset($_SERVER['HTTP_USER_AGENT']) && (preg_match($file->user_agent, $_SERVER['HTTP_USER_AGENT']) > 0))
										$strComplementaire .= $file->Format();
								}
							}
							if (($contenuFichier != '') && (is_dir(Configuration::$Css['cache'])))
							{
								$contenuFichier = preg_replace('/\s+/m', ' ', $contenuFichier);
								file_put_contents(Configuration::$Css['cache'].$fichier, $contenuFichier);
							}
						}
						else
						{
							foreach($css as $file)
							{
								if (($file->user_agent !== null) && isset($_SERVER['HTTP_USER_AGENT']) && (preg_match($file->user_agent, $_SERVER['HTTP_USER_AGENT']) > 0))
									$strComplementaire .= $file->Format();
							}
						}
						$str = '<link href="'.Configuration::$Static['url'].'/Css/'.$fichier.'" rel="stylesheet" type="text/css"/>';
						$str .= PHP_EOL.$strComplementaire;
					}
				}
				return $str;
			}
			
			public function GetCredits($separateur = ' - ')
			{
				$str = '';
				$credits = Credit::GetListe($this->id);
				$c = array();
				foreach($credits as $credit)
					array_push($c, $credit->Format());
				$str .= implode($separateur, $c);
				return $str;
			}
			
			public function HasZone($nom)
			{
				$find = false;
				if ($this->m_zones === null)
					$this->m_zones = Zone::GetListe($this->id);
				if (is_array($this->m_zones))
				{
					foreach($this->m_zones as $zone)
					{
						if (strtoupper($zone->nom) == strtoupper($nom))
							$find = true;
					}
				}
				return $find;
			}
			
			public static function	GetListe($droits = 1)
			{
				$class = 'Charte';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class);
				$sql .= ' WHERE droits >= '.intval($droits);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function	Get($id)
			{
				$charte = new Charte();
				return $charte->Charge($id);
			}
			
			private function dupliquerCss($idNewCharte, $autocommit = true)
			{
				$sql = ' INSERT INTO css (id_charte, ordre, fichier, user_agent)';
				$sql .= 'SELECT '.$idNewCharte.', ordre, fichier, user_agent FROM css WHERE id_charte='.$this->id;
				$sql .= ';';
				if($this->getBase()->insertOne($sql, $autocommit) === false)
					return false;
			}
			
			public function	Delete($idCharte = null, $autocommit = true)
			{
				$class = 'Charte';
				if ($idCharte === null)
					return false;
				$sql = 'DELETE FROM '.strtolower($class).' WHERE id = '.intval($idCharte);
				$rslt = $this->getBase()->deleteOne($sql, $autocommit);
				return $rslt;
			}	
		}
	}
?>