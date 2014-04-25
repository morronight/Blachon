<?php
	if (!class_exists('Image'))
	{
		require_once 'TableWithPublication.php';
		require_once 'include/Configuration.php';
		require_once 'Formatage.php';

		class	ImageCategorisation
		{
		}
		
		class	Image	extends TableWithPublication
		{
			protected	$location;
			protected	$cache;
			protected	$_categorisations;
			
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Image';
				$this->location = Configuration::$Images['location'];
				$this->cache = Configuration::$Images['cache'];
			}

			public function __destruct()
			{
				parent::__destruct();
			}

			public function Insert($path, $legende = null, $visible = 1, $autocommit = true)
			{
				$this->path = substr($path, 0, 255);
				if ($legende === null)
					$this->legende = null;
				else
					$this->legende = substr($legende, 0, 128);
				$this->visible = intval($visible);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (path, legende, visible) VALUES (\''.$this->getBase()->escapeString($this->path).'\'';
				if ($this->legende === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->legende).'\'';
				$sql .= ', '.intval($this->visible);
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}
			
			public static function	GetListe($array_id = null, $visible = null)
			{
				$class = 'Image';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE 1=1';
				if (($array_id !== null) && is_array($array_id))
				{
					$sql .= ' AND id IN (';
					$first = true;
					foreach($array_id as $id)
					{
						if ($first)
							$first = false;
						else
							$sql .= ', ';
						$sql .= intval($id);
					}
					$sql .= ')';
				}
				if ($visible !== null)
				{
					if ($visible === true)
						$sql .= ' AND visible = 1';
					else
						$sql .= ' AND visible = 0';
				}
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}

			public function Update($legende, $visible = 1, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if (($this->legende != (($legende !== null) ? substr($legende, 0, 128) : null)) || (intval($visible) != intval($this->visible)))
				{
					if ($legende !== null)
						$legende = substr($legende, 0, 128);
					else
						$legende = null;
					$sql = 'UPDATE '.strtolower($this->m_class).' SET ';
					if ($legende !== null)
						$sql .= ' legende = \''.$this->getBase()->escapeString($legende).'\'';
					else
						$sql .= ' legende = NULL';
					$sql .= ', visible = '.intval($visible);
					$sql .= ' WHERE id = '.intval($id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->legende = $legende;
						$this->visible = intval($visible);
						$rslt = true;
					}
				}
				return $rslt;
			}
			
			public function Remove($id = null, $autocommit = true)
			{
				if ($id === null)
					$id = $this->id;
				if ($id === null)
					return false;
				$sql = 'DELETE FROM '.strtolower($this->m_class).' WHERE id = '.intval($id);
				$rslt = $this->getBase()->deleteOne($sql, $autocommit);
				if ($rslt !== false)
					$this->id = null;
				return $rslt;
			}
			
			public function GetCategorisations()
			{
				if (($this->_categorisations === null) && isset($this->id) && (intval($this->id) > 0))
				{
					$class = 'ImageCategorisation';
					$sql = 'SELECT * FROM image_categorisation '.strtolower($class).' WHERE id_image = '.intval($this->id);
					$this->_categorisations = $this->getBase()->selectMany($sql, $class);
				}
				return $this->_categorisations;
			}
			
			public function hasCategorie($categorieId)
			{
				$categories = $this->GetCategorisations();
				if ($categories !== null)
				{
					foreach ($categories as $categorie)
					{
						if (intval($categorie->id_categorie) == $categorieId)
							return true;
					}
					return false;
				}
				return null;
			}
			
			public function GetCategoriesIds()
			{
				$categories = $this->GetCategorisations();
				if ($categories !== null)
				{
					$liste = '';
					foreach ($categories as $categorie)
					{
						if ($liste != '')
							$liste .= ',';
						$liste .= $categorie->id_categorie;
					}
					return $liste;
				}
				return '';
			}
			
			public function InsertCategorisation($idCategorie, $autocommit = true)
			{
				if (isset($this->id) && (intval($this->id) > 0) && (intval($idCategorie) > 0))
				{
					$sql = 'INSERT INTO image_categorisation (id_image, id_categorie) VALUES ('.intval($this->id).', '.intval($idCategorie).')';
					$rslt = $this->getBase()->insertOne($sql, $autocommit);
					if ($rslt !== false)
						return $idCategorie;
					return false;
				}
				return null;
			}
			
			public function RemoveCategorisations($autocommit = true)
			{
				if (isset($this->id) && (intval($this->id) > 0))
				{
					$sql = 'DELETE FROM image_categorisation WHERE id_image = '.intval($this->id);
					$rslt = $this->getBase()->deleteOne($sql, $autocommit);
					$this->_categorisations = null;
					if ($rslt !== false)
						return true;
					return false;
				}
				return null;
			}
			
			public function GetFilePath()
			{
				$path = null;
				if (isset($this->path))
				{
					$realLocation = realpath($this->location).DIR_SEPARATOR;
					$filepath = realpath(str_replace(DIR_SEPARATOR.DIR_SEPARATOR, DIR_SEPARATOR, $this->location.$this->path));
					if (isset($_SERVER['HTTP_USER_AGENT']) && (preg_match('/Firefox\/3|MSIE 6|MSIE 7|MSIE 8/', $_SERVER['HTTP_USER_AGENT']) > 0) && (strtolower(substr($filepath, -4)) == '.svg'))
						$filepath = substr($filepath, 0, -4).'.png';
					if (isset($_SERVER['HTTP_ACCEPT']) && (preg_match('/image\/svg\+xml|image\/\*/', $_SERVER['HTTP_ACCEPT']) == 0) && (strtolower(substr($filepath, -4)) == '.svg'))
						$filepath = substr($filepath, 0, -4).'.gif';
					if (($realLocation == substr($filepath, 0, strlen($realLocation))) && is_file($filepath))
					{
						if ((strtolower(substr($filepath, -4)) == '.png') || (strtolower(substr($filepath, -5)) == '.jpeg') || (strtolower(substr($filepath, -4)) == '.jpg') || (strtolower(substr($filepath, -4)) == '.gif') || (strtolower(substr($filepath, -4)) == '.svg'))
							$path = $filepath;
					}
				}
				return $path;				
			}
	
			public function GetCachedFilePath($width = null, $height = null)
			{
				$path = null;
				if (isset($this->path) && (($width !== null) || ($height !== null)))
				{
					$cacheLocation = realpath($this->cache).DIR_SEPARATOR;
					if (strtolower(substr($this->path, -5)) == '.jpeg')
						$cachePath = substr($this->path, 0, -5).'_'.(($width !== null) ? intval($width) : '').'_'.(($height !== null) ? intval($height) : '').'.jpg';
					else
						$cachePath = substr($this->path, 0, -4).'_'.(($width !== null) ? intval($width) : '').'_'.(($height !== null) ? intval($height) : '').substr($this->path, -4);
					$filepath = str_replace(DIR_SEPARATOR.DIR_SEPARATOR, DIR_SEPARATOR, $this->cache.$cachePath);
					if ($cacheLocation == substr($filepath, 0, strlen($cacheLocation)))
					{
						if ((strtolower(substr($filepath, -4)) == '.png') || (strtolower(substr($filepath, -5)) == '.jpeg') || (strtolower(substr($filepath, -4)) == '.jpg') || (strtolower(substr($filepath, -4)) == '.gif'))
						{
							if (!is_file($filepath))
							{
								$realLocation = realpath($this->location).DIR_SEPARATOR;
								$srcPath = realpath(str_replace(DIR_SEPARATOR.DIR_SEPARATOR, DIR_SEPARATOR, $this->location.$this->path));
								if (($realLocation == substr($srcPath, 0, strlen($realLocation))) && is_file($srcPath))
								{
									if ((strtolower(substr($srcPath, -4)) == '.png') || (strtolower(substr($srcPath, -5)) == '.jpeg') || (strtolower(substr($srcPath, -4)) == '.jpg') || (strtolower(substr($srcPath, -4)) == '.gif'))
									{
										list($w, $h) = getimagesize($srcPath);
										if ($width === null)
											$width = intval(1.* $w * $height / $h);
										if ($height === null)
											$height = intval(1. * $h * $width / $w);
										if (($width > 16) && ($height > 16))
										{
											$newImage = imagecreatetruecolor($width, $height);
											switch($this->GetMimeType())
											{
											case 'image/jpeg':
												$oldImage = imagecreatefromjpeg($srcPath);
												imagecopyresampled($newImage, $oldImage, 0, 0, 0, 0, $width, $height, $w, $h);
												imagejpeg($newImage, $filepath, 100);
												exec('jpegoptim --strip-all '.$filepath);
												break;
											case 'image/png':
												$oldImage = imagecreatefrompng($srcPath);
												imagecopyresampled($newImage, $oldImage, 0, 0, 0, 0, $width, $height, $w, $h);
												imagepng($newImage, $filepath, 0, PNG_ALL_FILTERS);
												exec('optipng -o7 '.$filepath);
												break;
											case 'image/gif':
												$oldImage = imagecreatefromgif($srcPath);
												imagecopyresampled($newImage, $oldImage, 0, 0, 0, 0, $width, $height, $w, $h);
												imagegif($newImage, $filepath);
												break;
											}
										}
									}
								}
							}
							if (is_file($filepath))
								$path = $filepath;
						}
						if (strtolower(substr($filepath, -4)) == '.svg')
						{
							if (is_file($filepath))
								$path = $filepath;
						}
					}
				}
				return $path;				
			}

			public function ClearCachedFile()
			{
				if (isset($this->path))
				{
					$cacheLocation = realpath($this->cache);
					if (strtolower(substr($this->path, -5)) == '.jpeg')
						$cachePath = substr($this->path, 0, -5).'_';
					else
						$cachePath = substr($this->path, 0, -4).'_';
					$files = scandir($cacheLocation);
					foreach($files as $file)
					{
						$filepath = $cacheLocation.DIR_SEPARATOR.$file;
						if (/*(strtolower(substr($file, -4)) == strtolower(substr($this->path, -4))) &&*/ (strtolower($cachePath) == strtolower(substr($file, 0, strlen($cachePath)))))
						{
							if (is_file($filepath))
								unlink($filepath);
						}
					}
				}
			}

			public function GetMimeType()
			{
				$mime = null;
				if (isset($this->path))
				{
					if (strtolower(substr($this->path, -5)) == '.jpeg')
						$path = substr($this->path, -5);
					else
						$path = substr($this->path, -4);
					if (isset($_SERVER['HTTP_USER_AGENT']) && (preg_match('/Firefox\/3|MSIE 6|MSIE 7|MSIE 8/', $_SERVER['HTTP_USER_AGENT']) > 0) && (strtolower($path) == '.svg'))
						$path = '.png';
					if (isset($_SERVER['HTTP_ACCEPT']) && (preg_match('/image\/svg\+xml|image\/\*/', $_SERVER['HTTP_ACCEPT']) == 0) && (strtolower($path) == '.svg'))
						$path = '.gif';
					switch (strtolower($path))
					{
					case '.png':
						$mime = 'image/png';
						break;
					case '.jpg':
					case '.jpeg':
						$mime = 'image/jpeg';
						break;
					case '.gif':
						$mime = 'image/gif';
						break;
					case '.svg':
						$mime = 'image/svg+xml';
						break;
					}
				}
				return $mime;
			}

			public static function	Search($idCategories = null, $brouillons = false, $archives = false, $filtre = null, $visible = null, $limit = null, $sortedByCategorie = false)
			{
				$class = 'Image';
				$base = BD::OuvrirBase();
				$brouillonsClause = '1 = 1';
				if ($brouillons == false)
					$brouillonsClause = '(NOT i.publication IS NULL AND i.publication <= NOW())';
				$archivesClause = '1 = 1';
				if ($archives == false)
					$archivesClause = '(i.expiration IS NULL OR i.expiration > NOW())';
				if ($idCategories === null)
					$idCategories = array();
				if ($sortedByCategorie === true)
					$joinCategorie = ' LEFT JOIN categorie cat ON c.id_categorie = cat.id';
				else
					$joinCategorie = '';
				if (is_array($idCategories))
				{
					if (count($idCategories) > 0)
						$sql = 'SELECT DISTINCT i.* FROM '.strtolower($class).' i LEFT JOIN '.strtolower($class).'_categorisation c ON c.id_'.strtolower($class).' = i.id'.$joinCategorie.' WHERE (c.id_categorie IS NULL OR c.id_categorie IN ('.implode(',', $idCategories).'))';
					else
						$sql = 'SELECT DISTINCT i.* FROM '.strtolower($class).' i LEFT JOIN '.strtolower($class).'_categorisation c ON c.id_'.strtolower($class).' = i.id'.$joinCategorie.' WHERE c.id_categorie IS NULL';
				}
				else
					$sql = 'SELECT DISTINCT i.* FROM '.strtolower($class).' i LEFT JOIN '.strtolower($class).'_categorisation c ON c.id_'.strtolower($class).' = i.id'.$joinCategorie.' WHERE (c.id_categorie IS NULL OR c.id_categorie = '.intval($idCategories).')';
				$sql .= ' AND '.$brouillonsClause.' AND '.$archivesClause;
				if (($filtre !== null) && ($filtre != ''))
				{
					$filtres = explode(' ', $filtre);
					if (count($filtres) > 0)
					{
						$sql .= ' AND (';
						foreach ($filtres as $k => $f)
						{
							if ($k > 0)
								$sql .= ' AND';
							$sql .= ' (i.legende LIKE \'%'.$base->escapeString($f).'%\')';
						}
						$sql .= ')';
					}
				}
				if ($visible !== null)
				{
					if ($visible !== false)
						$sql .= ' AND visible = 1';
					else
						$sql .= ' AND visible = 0';
				}
				if ($sortedByCategorie === true)
					$sql .= ' ORDER BY cat.nom ASC, i.publication DESC, i.expiration DESC, i.id DESC';
				else
					$sql .= ' ORDER BY i.publication DESC, i.expiration DESC, i.id DESC';
				if ($limit !== null)
					$sql .= ' LIMIT '.intval($limit);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}

			public static function	Get($id)
			{
				$image = new Image();
				return $image->Charge($id);
			}
		}
	}
?>