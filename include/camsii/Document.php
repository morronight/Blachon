<?php
	if (!class_exists('Document'))
	{
		require_once 'TableWithPublication.php';
		require_once 'Categorie.php';
		require_once 'include/Configuration.php';
		require_once 'Formatage.php';

		class	DocumentCategorisation
		{
		}

		class	Document	extends TableWithPublication
		{
			protected	$location = null;
			protected	$_categorisations;
			
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Document';
				$this->location = Configuration::$Documents['location'];
			}

			public function __destruct()
			{
				parent::__destruct();
			}
			
			public function Insert($nom, $path, $taille, $description = null, $visible = 1, $autocommit = true)
			{
				$this->nom = $nom;
				$this->path = $path;
				$this->taille = $taille;
				if ($description !== null)
					$this->description = substr($description, 0, 255);
				else
					$this->description = null;
				$this->visible = intval($visible);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (nom, path, taille, description, visible) VALUES (\''.$this->getBase()->escapeString($this->nom).'\', \''.$this->getBase()->escapeString($this->path).'\', '.intval($taille);
				if ($this->description === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->description).'\'';
				$sql .= ', visible = '.intval($this->visible);
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}

			public function Update($nom, $taille = null, $description = null, $visible = 1, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if (($this->nom != substr($nom, 0, 64)) || ($this->description != (($description !== null) ? substr($description, 0, 255) : null)) || ($taille !== null) || (intval($visible) != intval($this->visible))) {
					if ($description !== null)
						$description = substr($description, 0, 255);
					else
						$description = null;
					$sql = 'UPDATE '.strtolower($this->m_class).' SET nom = \''.$this->getBase()->escapeString(substr($nom, 0, 64)).'\'';
					if ($taille !== null)
						$sql .= ', taille = '.intval($taille);
					if ($description !== null)
						$sql .= ', description = \''.$this->getBase()->escapeString($description).'\'';
					else
						$sql .= ', description = NULL';
					$sql .= ', visible = '.intval($visible);
					$sql .= ' WHERE id = '.intval($id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->nom = $nom;
						if ($taille !== null)
							$this->taille = intval($taille);
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

			public static function	GetListe($visible = null)
			{
				$class = 'Document';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class);
				if ($visible !== null)
				{
					if ($visible === true)
						$sql .= ' WHERE visible = 1';
					else
						$sql .= ' WHERE visible = 0';
				}
				$sql .= ' ORDER BY publication DESC, id DESC';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function	GetByName($nom)
			{
				$class = 'Document';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE nom = \''.$base->escapeString($nom).'\'';
				$document = $base->selectOne($sql, $class);
				$base->FermerBase();
				return $document;
			}

			public static function	GetByPath($path)
			{
				$documents = Document::GetListe();
				$theDocument = null;
				foreach($documents as $document)
				{
					if (strtolower($document->GetLien()) == strtolower($path))
					{
						$theDocument = $document;
						break;
					}
				}
				return $theDocument;
			}

			public function GetCategorisations()
			{
				if (($this->_categorisations === null) && isset($this->id) && (intval($this->id) > 0))
				{
					$class = 'DocumentCategorisation';
					$sql = 'SELECT * FROM document_categorisation '.strtolower($class).' WHERE id_document = '.intval($this->id);
					$this->_categorisations = $this->getBase()->selectMany($sql, $class);
				}
				return $this->_categorisations;
			}
			
			public static function GetCategoriesNonVide()
			{
				$class = 'DocumentCategorisation';
				$base = BD::OuvrirBase();
				$sql = 'SELECT DISTINCT id_categorie FROM document_categorisation ORDER BY id_categorie';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
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
					$sql = 'INSERT INTO document_categorisation (id_document, id_categorie) VALUES ('.intval($this->id).', '.intval($idCategorie).')';
					$rslt = $this->getBase()->insertOne($sql, $autocommit);
					$this->_categorisations = null;
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
					$sql = 'DELETE FROM document_categorisation WHERE id_document = '.intval($this->id);
					if ($idCategorie !== null)
						$sql .= ' AND id_categorie = '.intval($idCategorie);
					$rslt = $this->getBase()->deleteOne($sql, $autocommit);
					$this->_categorisations = null;
					if ($rslt !== false)
						return true;
					return false;
				}
				return null;
			}

			public function GetDescription()
			{
				$rslt = '';
				if (isset($this->description) && ($this->description !== null))
					$rslt = $this->description;
				return $rslt;
			}
			
			public function GetReadeableSize()
			{
				return Formatage::GetReadeableFileSize($this->taille);
			}

			public function GetFilePath()
			{
				$path = null;
				if (isset($this->path))
				{
					$realLocation = realpath($this->location).DIR_SEPARATOR;
					$filepath = realpath(str_replace(DIR_SEPARATOR.DIR_SEPARATOR, DIR_SEPARATOR, $this->location.$this->path));
					if (($realLocation == substr($filepath, 0, strlen($realLocation))) && is_file($filepath) && (strtolower(substr($filepath, -4)) == '.pdf'))
						$path = $filepath;
				}
				return $path;				
			}
			
			public function GetLien()
			{
				return Formatage::Lien($this->nom).strtolower(substr($this->path, -4));
			}
			
			public static function	Search($idCategories = null, $brouillons = false, $archives = false, $filtre = null, $visible = null, $limit = null, $sortedByCategorie = false)
			{
				$class = 'Document';
				$base = BD::OuvrirBase();
				$brouillonsClause = '1 = 1';
				if ($brouillons == false)
					$brouillonsClause = '(NOT g.publication IS NULL AND g.publication <= NOW())';
				$archivesClause = '1 = 1';
				if ($archives == false)
					$archivesClause = '(g.expiration IS NULL OR g.expiration > NOW())';
				if ($idCategories === null)
					$idCategories = array();
				if ($sortedByCategorie === true)
					$joinCategorie = ' LEFT JOIN categorie cat ON c.id_categorie = cat.id';
				else
					$joinCategorie = '';
				if (is_array($idCategories))
				{
					if (count($idCategories) > 0)
						$sql = 'SELECT DISTINCT g.* FROM '.strtolower($class).' g LEFT JOIN '.strtolower($class).'_categorisation c ON c.id_'.strtolower($class).' = g.id'.$joinCategorie.' WHERE (c.id_categorie IS NULL OR c.id_categorie IN ('.implode(',', $idCategories).'))';
					else
						$sql = 'SELECT DISTINCT g.* FROM '.strtolower($class).' g LEFT JOIN '.strtolower($class).'_categorisation c ON c.id_'.strtolower($class).' = g.id'.$joinCategorie.' WHERE c.id_categorie IS NULL';
				}
				else
					$sql = 'SELECT DISTINCT g.* FROM '.strtolower($class).' g LEFT JOIN '.strtolower($class).'_categorisation c ON c.id_'.strtolower($class).' = g.id'.$joinCategorie.' WHERE (c.id_categorie IS NULL OR c.id_categorie = '.intval($idCategories).')';
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
							$sql .= ' (g.nom LIKE \'%'.$base->escapeString($f).'%\')';
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
					$sql .= ' ORDER BY cat.ordre ASC, cat.nom ASC, g.publication DESC, g.expiration DESC, g.id DESC';
				else
					$sql .= ' ORDER BY DATE(g.publication) DESC, g.nom ASC';
					//g.expiration DESC, g.id DESC';
				if ($limit !== null)
					$sql .= ' LIMIT '.intval($limit);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			
			public static function	testTitreExist($titre, $id = null)
			{
				$class = 'Document';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE LOWER(nom) = LOWER(\''.$base->escapeString($titre.'.pdf').'\')';
				if ($id !== null)
					$sql .= ' AND id != '.intval($id);
				$test = $base->selectOne($sql, $class);
				$base->FermerBase();
				if ($test !== false)
					return true;
				return false;
			}
			
			public static function	Get($id)
			{
				$document = new Document();
				return $document->Charge($id);
			}
		}
	}
?>