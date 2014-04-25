<?php
	if (!class_exists('Video'))
	{
		require_once 'TableWithPublication.php';
		require_once 'Formatage.php';
		
		class	VideoCategorisation
		{
		}
		
		class	Video	extends TableWithPublication
		{
			protected	$_categorisations;
			
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Video';
				$this->_categorisations = null;
			}

			public function __destruct()
			{
				parent::__destruct();
			}

			public function Insert($url, $description = null, $visible = 1, $autocommit = true)
			{
				$this->url = substr($url, 0, 255);
				if ($description === null)
					$this->description = null;
				else
					$this->description = $description;
				$this->visible = intval($visible);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (url, description, visible) VALUES (\''.$this->getBase()->escapeString($this->url).'\'';
				if ($this->description === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->description).'\'';
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
				$class = 'Video';
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

			public function Update($url, $description, $visible = 1, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if (($url != $this->url) || ($this->description != $description) || (intval($visible) != intval($this->visible)))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET url = \''.$this->getBase()->escapeString($url).'\'';
					if ($description !== null)
						$sql .= ', description = \''.$this->getBase()->escapeString($description).'\'';
					else
						$sql .= ', description = NULL';
					$sql .= ', visible = '.intval($visible);
					$sql .= ' WHERE id = '.intval($id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->description = $description;
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
					$class = 'VideoCategorisation';
					$sql = 'SELECT * FROM video_categorisation '.strtolower($class).' WHERE id_video = '.intval($this->id);
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
					$sql = 'INSERT INTO video_categorisation (id_video, id_categorie) VALUES ('.intval($this->id).', '.intval($idCategorie).')';
					$rslt = $this->getBase()->insertOne($sql, $autocommit);
					if ($rslt !== false)
						return $idCategorie;
					return false;
				}
				return null;
			}
			
			public function RemoveCategorisations($idCategorie = null, $autocommit = true)
			{
				if (isset($this->id) && (intval($this->id) > 0))
				{
					if ($idCategorie === null)
						$sql = 'DELETE FROM video_categorisation WHERE id_video = '.intval($this->id);
					else
						$sql = 'DELETE FROM video_categorisation WHERE id_video = '.intval($this->id).' AND id_categorie = '.intval($idCategorie);
					$rslt = $this->getBase()->deleteOne($sql, $autocommit);
					$this->_categorisations = null;
					if ($rslt !== false)
						return true;
					return false;
				}
				return null;
			}
			
			public static function	Search($idCategories = null, $brouillons = false, $archives = false, $filtre = null, $visible = null, $limit = null, $sortedByCategorie = false)
			{
				$class = 'Video';
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
							$sql .= ' (i.description LIKE \'%'.$base->escapeString($f).'%\')';
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
					$sql .= ' ORDER BY cat.nom ASC, i.publication DESC, i.expiration DESC';
				else
					$sql .= ' ORDER BY i.publication DESC, i.expiration DESC';
				if ($limit !== null)
					$sql .= ' LIMIT '.intval($limit);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public function Format()
			{
				// http://www.dailymotion.com/video/xpwkw9_effroyables-jardins-bande-annonce-vf_shortfilms
				if (preg_match('/^http:\/\/www\.dailymotion.com\/video\/([a-zA-Z0-9]+)_[a-zA-Z0-9_]+/', $this->url, $regs) > 0)
					return '<iframe frameborder="0" width="480" height="360" src="http://www.dailymotion.com/embed/video/'.$regs[1].'?foreground=%23F78949&highlight=%23FFFFFF&background=%23612467&logo=0"></iframe>';
				if (preg_match('/^http:\/\/www\.youtube.com\/watch\?v=([a-zA-Z0-9]+)/', $this->url, $regs) > 0)
					return '<iframe frameborder="0" width="480" height="360" src="http://www.youtube.com/embed/'.$regs[1].'?foreground=%23F78949&highlight=%23FFFFFF&background=%23612467&logo=0" frameborder="0"/></iframe>';
				return '';
			}

			public static function	Get($id)
			{
				$video = new Video();
				return $video->Charge($id);
			}
		}
	}
?>