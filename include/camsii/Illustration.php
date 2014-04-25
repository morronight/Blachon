<?php
	if (!class_exists('Illustration'))
	{
		require_once('Table.php');

		class	Illustration	extends Table
		{
			public		$id_image;
			public		$id_galerie;
			public		$position;
			public		$legende;
			public		$largeur;
			public		$hauteur;
			protected	$location;
			
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Illustration';
				$this->location = Configuration::$Images['location'];
			}

			public function __destruct()
			{
				parent::__destruct();
			}

			public static function	Delete($id, $autocommit = true)
			{
				$class = 'Illustration';
				$base = BD::OuvrirBase();
				$sql = 'DELETE FROM '.strtolower($class).' WHERE id = '.intval($id);
				$rslt = $base->deleteOne($sql, $autocommit);
				$base->FermerBase();
				return $rslt;
			}
			
			public function Insert($idImage, $idGalerie, $position, $legende = null, $largeur = null, $hauteur = null, $autocommit = true)
			{
				$this->id_image = $idImage;
				$this->id_galerie = $idGalerie;
				$this->position = $position;
				if ($legende === null)
					$this->legende = null;
				else
					$this->legende = substr($legende, 0, 128);
				$this->largeur = $largeur;
				$this->hauteur = $hauteur;
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (id_image, id_galerie, position, legende, largeur, hauteur) VALUES (';
				if ($this->id_image === null)
					$sql .= 'NULL';
				else
					$sql .= intval($this->id_image);
				if ($this->id_galerie === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.intval($this->id_galerie);
				$sql .= ', '.intval($this->position);
				if ($this->legende === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->legende).'\'';
				if ($this->largeur === null)
					$sql .= ',NULL';
				else
					$sql .= ', '.intval($this->largeur);
				if ($this->hauteur === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.intval($this->hauteur);
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}
			
			public function Update($idImage, $idGalerie, $position, $legende = null, $largeur = null, $hauteur = null, $autocommit = true)
			{
				if ($this->id === null)
					return false;
				$rslt = null;
				if (($this->id_image != $idImage) || ($this->id_galerie != $idGalerie) || ($this->position != $position) || (($this->legende === null) && ($legende !== null)) || (($this->legende !== null) && ($legende === null)) || (($this->legende !== null) && ($legende !== null) && ($this->legende != substr($legende, 0, 128))) || ($this->largeur != $largeur) || ($this->hauteur != $hauteur))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET position = '.intval($position);
					if ($idImage === null)
						$sql .= ', id_image = NULL';
					else
						$sql .= ', id_image = '.intval($idImage);
					if ($idGalerie === null)
						$sql .= ', id_galerie = NULL';
					else
						$sql .= ', id_galerie = '.intval($idGalerie);
					if ($legende === null)
						$sql .= ', legende = NULL';
					else
						$sql .= ', legende = \''.$this->getBase()->escapeString($legende).'\'';
					if ($largeur === null)
						$sql .= ', largeur = NULL';
					else
						$sql .= ', largeur = '.intval($largeur);
					if ($hauteur === null)
						$sql .= ', hauteur = NULL';
					else
						$sql .= ', hauteur = '.intval($hauteur);
					$sql .= ' WHERE id = '.intval($this->id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->id_image = $idImage;
						$this->id_galerie = $idGalerie;
						$this->position = $position;
						if ($legende === null)
							$this->legende = null;
						else
							$this->legende = substr($legende, 0, 128);
						$this->largeur = $largeur;
						$this->hauteur = $hauteur;
						$rslt = true;
					}
				}
				return $rslt;
			}
			
			public static function	GetListe($array_id = null)
			{
				$class = 'Illustration';
				$base = BD::OuvrirBase();
				$sql = 'SELECT c.id, c.id_image, c.id_galerie, i.path, IFNULL(c.legende, i.legende) legende, c.position, c.largeur, c.hauteur FROM '.strtolower($class).' c LEFT JOIN image i ON c.id_image = i.id';
				if (($array_id !== null) && is_array($array_id))
				{
					$sql .= ' WHERE c.id IN (';
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
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function getNombreUtilisations($idImage, $idGalerie)
			{
				$class = 'Illustration';
				$base = BD::OuvrirBase();
				if ($idImage !== null)
					$sql = 'SELECT * FROM '.strtolower($this->m_class).' WHERE id_image = '.intval($idImage);
				else
					$sql = 'SELECT * FROM '.strtolower($this->m_class).' WHERE id_galerie = '.intval($idGalerie);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				if ($liste !== null)
					return count($liste);
				return 0;
			}
			
			protected function	prepare_Charge($id)
			{
				$sql = null;
				if (($this->getBase() !== false) && !is_null($this->m_pkey))
					$sql = 'SELECT c.id, c.id_image, c.id_galerie, i.path, IFNULL(c.legende, i.legende) legende, c.position, c.largeur, c.hauteur FROM '.strtolower($this->m_class).' c LEFT JOIN image i ON c.id_image = i.id WHERE c.'.$this->m_pkey.' = '.$this->getBase()->escapeString($id);
				return $sql;
			}
			
			public static function	Get($id)
			{
				$illustration = new Illustration();
				return $illustration->Charge($id);
			}

			public function GetFilePath()
			{
				$path = null;
				if (isset($this->path))
				{
					$realLocation = realpath($this->location).'/';
					$filepath = realpath(str_replace('//', '/', $this->location.$this->path));
					if (($realLocation == substr($filepath, 0, strlen($realLocation))) && is_file($filepath))
					{
						if ((strtolower(substr($filepath, -4)) == '.png') || (strtolower(substr($filepath, -4)) == '.jpg') || (strtolower(substr($filepath, -4)) == '.gif'))
							$path = $filepath;
					}
				}
				return $path;				
			}
	
			public function GetSize($width = null, $height = null)
			{
				$path = null;
				if (isset($this->path))
				{
					$realLocation = realpath($this->location).DIR_SEPARATOR;
					$srcPath = realpath(str_replace(DIR_SEPARATOR.DIR_SEPARATOR, DIR_SEPARATOR, $this->location.$this->path));
					if ((preg_match('/image\/svg\+xml|image\/\*/', $_SERVER['HTTP_ACCEPT']) == 0) && (strtolower(substr($srcPath, -4)) == '.svg'))
						$srcPath = substr($srcPath, 0, -4).'.gif';
					if (($realLocation == substr($srcPath, 0, strlen($realLocation))) && is_file($srcPath))
					{
						if ((strtolower(substr($srcPath, -4)) == '.png') || (strtolower(substr($srcPath, -4)) == '.jpg') || (strtolower(substr($srcPath, -4)) == '.gif'))
						{
							list($w, $h) = getimagesize($srcPath);
							if (($width !== null) || ($height !== null))
							{
								if ($width === null)
									$width = intval(1.* $w * $height / $h);
								if ($height === null)
									$height = intval(1. * $h * $width / $w);
							}
							else
							{
								$width = $w;
								$height = $h;
							}
						}
					}
				}
				$rslt = array($width, $height);
				return $rslt;
			}
			
			public function GetMimeType()
			{
				$mime = null;
				if (isset($this->path))
				{
					$path = substr($this->path, -4);
					if ((preg_match('/Firefox\/3|MSIE 6|MSIE 7|MSIE 8/', $_SERVER['HTTP_USER_AGENT']) > 0) && (strtolower($path) == '.svg'))
						$path = '.png';
					if ((preg_match('/image\/svg\+xml|image\/\*/', $_SERVER['HTTP_ACCEPT']) == 0) && (strtolower($path) == '.svg'))
						$path = '.gif';
					switch (strtolower($path))
					{
					case '.png':
						$mime = 'image/png';
						break;
					case '.jpg':
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
		}
	}
?>