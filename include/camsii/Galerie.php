<?php
	if (!class_exists('Galerie'))
	{
		require_once 'TableWithPublication.php';
		require_once 'Image.php';
		require_once 'Formatage.php';

		class	GalerieCategorisation
		{
		}
		
		class	Galerie	extends TableWithPublication
		{
			protected	$_categorisations;
			protected	$_images;
			
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Galerie';
				$this->_categorisations = null;
				$this->_images = null;
			}

			public function __destruct()
			{
				parent::__destruct();
			}

			public function Insert($nom, $description = null, $visible = 1, $autocommit = true)
			{
				$this->nom = substr($nom, 0, 64);
				if ($description === null)
					$this->description = null;
				else
					$this->description = $description;
				$this->visible = intval($visible);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (nom, description, visible) VALUES (\''.$this->getBase()->escapeString($this->nom).'\'';
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
				$class = 'Galerie';
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

			public function Update($nom, $description, $visible = 1, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if (($nom != $this->nom) || ($this->description != $description) || (intval($visible) != intval($this->visible)))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET nom = \''.$this->getBase()->escapeString($nom).'\'';
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
					$class = 'GalerieCategorisation';
					$sql = 'SELECT * FROM galerie_categorisation '.strtolower($class).' WHERE id_galerie = '.intval($this->id);
					$this->_categorisations = $this->getBase()->selectMany($sql, $class);
				}
				return $this->_categorisations;
			}
			
			public function GetImages()
			{
				if (($this->_images === null) && isset($this->id) && (intval($this->id) > 0))
				{
					$class = 'Image';
					$sql = 'SELECT image.* FROM galerie_image g JOIN image ON image.id = g.id_image WHERE g.id_galerie = '.intval($this->id).' ORDER BY image.publication';
					$this->_images = $this->getBase()->selectMany($sql, $class);
				}
				return $this->_images;
			}
			
			public function GetImagesIds()
			{
				$images = $this->GetImages();
				if ($images !== null)
				{
					$liste = '';
					foreach ($images as $image)
					{
						if ($liste != '')
							$liste .= ',';
						$liste .= $image->id;
					}
					return $liste;
				}
				return '';
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
					$sql = 'INSERT INTO galerie_categorisation (id_galerie, id_categorie) VALUES ('.intval($this->id).', '.intval($idCategorie).')';
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
						$sql = 'DELETE FROM galerie_categorisation WHERE id_galerie = '.intval($this->id);
					else
						$sql = 'DELETE FROM galerie_categorisation WHERE id_galerie = '.intval($this->id).' AND id_categorie = '.intval($idCategorie);
					$rslt = $this->getBase()->deleteOne($sql, $autocommit);
					$this->_categorisations = null;
					if ($rslt !== false)
						return true;
					return false;
				}
				return null;
			}
			
			public function RemoveImages($idImage = null, $autocommit = true)
			{
				if (isset($this->id) && (intval($this->id) > 0))
				{
					if ($idImage === null)
						$sql = 'DELETE FROM galerie_image WHERE id_galerie = '.intval($this->id);
					else
						$sql = 'DELETE FROM galerie_image WHERE id_galerie = '.intval($this->id).' AND id_image = '.intval($idImage);
					$rslt = $this->getBase()->deleteOne($sql, $autocommit);
					$this->_images = null;
					if ($rslt !== false)
						return true;
					return false;
				}
				return null;
			}
			
			public static function	Search($idCategories = null, $brouillons = false, $archives = false, $filtre = null, $visible = null, $limit = null, $sortedByCategorie = false)
			{
				$class = 'Galerie';
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
							$sql .= ' ((i.nom LIKE \'%'.$base->escapeString($f).'%\') OR (i.description LIKE \'%'.$base->escapeString($f).'%\'))';
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
					$sql .= ' ORDER BY cat.nom ASC, i.publication DESC, i.expiration DESC, i.nom DESC';
				else
					$sql .= ' ORDER BY i.publication DESC, i.expiration DESC, i.nom DESC';
				if ($limit !== null)
					$sql .= ' LIMIT '.intval($limit);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}

			public static function	Get($id)
			{
				$galerie = new Galerie();
				return $galerie->Charge($id);
			}
			
			public function InsertImage($idImage, $autocommit = true)
			{
				if (isset($this->id) && (intval($this->id) > 0) && (intval($idImage) > 0))
				{
					$sql = 'INSERT INTO galerie_image (id_galerie, id_image) VALUES ('.intval($this->id).', '.intval($idImage).')';
					$rslt = $this->getBase()->insertOne($sql, $autocommit);
					if ($rslt !== false)
						return $idImage;
					return false;
				}
				return null;
			}
		}
	}
?>