<?php
	if (!class_exists('Article'))
	{
		require_once 'include/Configuration.php';
		require_once 'TableWithPublication.php';
		require_once 'Paragraphe.php';
		require_once 'Illustration.php';
		require_once 'Categorisation.php';
		require_once 'Charte.php';
		require_once 'Menu.php';
		require_once 'Tableau.php';
		require_once 'Formatage.php';

		class	Article	extends TableWithPublication
		{
			public		$id;
			public		$titre;
			public		$resume;
			public		$publication;
			public		$expiration;
			public		$id_charte;
			public		$auteur;
			protected	$m_paragraphes;
			protected	$m_categorisations;
			protected	$m_illustration;
			protected	$m_charte;
			
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Article';
				$this->m_paragraphes = null;
				$this->m_categorisations = null;
				$this->m_illustration = null;
				$this->m_charte = null;
			}

			public function __destruct()
			{
				parent::__destruct();
			}

			public function Insert($titre, $resume, $auteur, $expiration = null, $charteId = 1, $autocommit = true)
			{
				$this->titre = substr($titre, 0, 128);
				$this->resume = $resume;
				$this->auteur = substr($auteur, 0, 20);
				$this->expiration = $expiration;
				$this->publication = null;
				$this->id_charte = null;
				$this->id_charte = intval($charteId);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (titre, resume, publication, expiration, id_charte, auteur) VALUES (\''.$this->getBase()->escapeString($this->titre).'\', \''.$this->getBase()->escapeString($this->resume).'\', NULL';
				if ($this->expiration === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->expiration).'\'';
				$sql .= ', '.intval($this->id_charte);
				if ($this->auteur === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->auteur).'\'';
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}

			public function Update($titre, $resume, $auteur, $charteId, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if (($this->titre != substr($titre, 0, 128)) || ($this->resume != $resume) || (intval($this->id_charte) != intval($charteId)) || ($this->auteur != $auteur))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET titre = \''.$this->getBase()->escapeString(substr($titre, 0, 128)).'\', resume = \''.$this->getBase()->escapeString($resume).'\', id_charte = '.intval($charteId);
					if ($auteur === null)
						$sql .= ', auteur = NULL';
					else
						$sql .= ', auteur = \''.$this->getBase()->escapeString(substr($auteur, 0, 20)).'\'';
					$sql .= ' WHERE id = '.intval($id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->titre = substr($titre, 0, 128);
						$this->resume = $resume;
						$this->id_charte = intval($charteId);
						$this->auteur = substr($auteur, 0, 20);
						$rslt = true;
					}
				}
				return $rslt;
			}
			
			public function	Delete($id = null, $autocommit = true)
			{
				if ($id === null)
					$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				$tableaux = Tableau::GetListe($id, null);
				if (($tableaux !== null) && ($tableaux !== false) && (count($tableaux) > 0))
				{
					foreach($tableaux as $tableau)
					{
						$rslt = $tableau->Delete(false);
						if ($rslt === false)
							break;
					}
				}
				if ($rslt !== false)
				{
					$rslt = $this->DeleteIllustration(false);
					if ($rslt !== false)
					{
						$rslt = $this->RemoveCategorisation(null, false);
						if ($rslt !== false)
						{
							while (count($this->GetParagraphes()) && ($rslt !== false))
								$rslt = $this->DeleteParagraphe(0, false);
							if ($rslt !== false)
							{
								$sql = 'DELETE FROM '.strtolower($this->m_class).' WHERE id = '.intval($id);
								$rslt = $this->getBase()->deleteOne($sql, $autocommit);
								if ($rslt !== false)
								{
									if ($autocommit)
										$this->commit();
									$rslt = true;
								}
								else
								{
									if ($autocommit)
										$this->rollback();
								}
							}
							else
							{
								if ($autocommit)
									$this->rollback();
							}
						}
						else
						{
							if ($autocommit)
								$this->rollback();
						}
					}
					else
					{
						if ($autocommit)
							$this->rollback();
					}
				}
				else
				{
					if ($autocommit)
						$this->rollback();
				}
				return $rslt;
			}
			
			public static function	GetListe($idCategories = null, $brouillons = false, $archives = false, $limit = null)
			{
				$class = 'Article';
				$base = BD::OuvrirBase();
				$brouillonsClause = '1 = 1';
				if ($brouillons == false)
					$brouillonsClause = '(NOT publication IS NULL AND publication <= NOW())';
				$archivesClause = '1 = 1';
				if ($archives == false)
					$archivesClause = '(expiration IS NULL OR expiration > NOW())';
				if ($idCategories !== null)
				{ 
					if (is_array($idCategories))
						$sql = 'SELECT * FROM v_article_categorise '.strtolower($class).' WHERE id_categorie IN ('.implode(',', $idCategories).') AND '.$brouillonsClause.' AND '.$archivesClause.' ORDER BY publication DESC, id DESC';
					else
						$sql = 'SELECT * FROM v_article_categorise '.strtolower($class).' WHERE id_categorie = '.intval($idCategories).' AND '.$brouillonsClause.' AND '.$archivesClause.' ORDER BY publication DESC, id DESC';
				}
				else
					$sql = 'SELECT * FROM '.strtolower($class).' WHERE '.$brouillonsClause.' AND '.$archivesClause.' ORDER BY publication DESC, id DESC';
				if ($limit !== null)
					$sql .= ' LIMIT '.intval($limit);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}

			public static function	GetListeByIds($ids = null, $brouillons = false, $archives = false)
			{
				$class = 'Article';
				$base = BD::OuvrirBase();
				$brouillonsClause = '1 = 1';
				if ($brouillons == false)
					$brouillonsClause = '(NOT publication IS NULL AND publication <= NOW())';
				$archivesClause = '1 = 1';
				if ($archives == false)
					$archivesClause = '(expiration IS NULL OR expiration > NOW())';
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE id IN ('.implode(',', $ids).') AND '.$brouillonsClause.' AND '.$archivesClause.' ORDER BY publication DESC, id DESC';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}

			public function GetLien()
			{
				return Formatage::Lien($this->titre);
			}
			
			public static function	GetListeCategorisee($brouillons = false, $archives = false)
			{
				$class = 'Article';
				$base = BD::OuvrirBase();
				$brouillonsClause = '1 = 1';
				if ($brouillons == false)
					$brouillonsClause = '(NOT publication IS NULL AND publication <= NOW())';
				$archivesClause = '1 = 1';
				if ($archives == false)
					$archivesClause = '(expiration IS NULL OR expiration > NOW())';

				$sql = 'SELECT * FROM v_article_categorise '.strtolower($class).' WHERE '.$brouillonsClause.' AND '.$archivesClause.' ORDER BY categorie ASC, publication DESC, id DESC';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}

			public static function	GetListeBrouillon()
			{
				return TableWithPublication::GetListeBrouillon('Article');
			}

			public static function	GetListeArchive()
			{
				return TableWithPublication::GetListeArchive('Article');
			}
			
			public function GetParagraphes($force = false)
			{
				if ((($this->m_paragraphes === null) || $force) && isset($this->id) && (intval($this->id) > 0))
					$this->m_paragraphes = Paragraphe::GetListe(intval($this->id));
				return $this->m_paragraphes;
			}
			
			public function InsertParagraphe($titre, $texte, $autocommit = true)
			{
				if (isset($this->id) && (intval($this->id) > 0))
				{
					$paragraphe = new Paragraphe();
					$paragraphe->Insert(intval($this->id), $titre, $texte, $autocommit);
					$this->m_paragraphes = null;
					return $paragraphe->id;
				}
				return null;
			}
			
			public function UpdateParagraphe($index, $titre, $texte, $autocommit = true)
			{
				if (isset($this->id) && (intval($this->id) > 0))
				{
					$this->GetParagraphes();
					$paragraphe =& $this->m_paragraphes[intval($index)];
					return $paragraphe->Update($titre, $texte, $autocommit);
				}
				return false;
			}
			
			public function DeleteParagraphe($index, $autocommit = true)
			{
				if (isset($this->id) && (intval($this->id) > 0))
				{
					$this->GetParagraphes();
					if (isset($this->m_paragraphes[intval($index)]))
					{
						$paragraphe = $this->m_paragraphes[intval($index)];
						$rslt = $paragraphe->Delete(null, null, $autocommit);
						if ($rslt !== false)
							$this->GetParagraphes(true);
						return $rslt;
					}
				}
				return false;
			}
			
			public function GetCategorisations($force = false)
			{
				if ($force === true)
					$this->m_categorisations = null;
				if (($this->m_categorisations === null) && isset($this->id) && (intval($this->id) > 0))
					$this->m_categorisations = Categorisation::GetListe(intval($this->id));
				return $this->m_categorisations;
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
				if (isset($this->id) && (intval($this->id) > 0))
				{
					$categorisation = new Categorisation();
					$categorisation->Insert(intval($this->id), $idCategorie, $autocommit);
					$this->m_categorisations = null;
					return $categorisation->id_categorie;
				}
				return null;
			}
			
			public function RemoveCategorisation($idCategorie, $autocommit = true)
			{
				$rslt = null;
				if (isset($this->id) && (intval($this->id) > 0))
				{
					$categorisation = null;
					$categories = $this->GetCategorisations();
					if ($idCategorie !== null)
					{
						if ($categories !== null)
						{
							foreach ($categories as $categorie)
							{
								if ($categorie->id_categorie == intval($idCategorie))
									$categorisation = $categorie;
							}
						}
						if ($categorisation !== null)
						{
							$rslt = $categorisation->Remove(intval($this->id), $idCategorie, $autocommit);
							$this->m_categorisations = null;
						}
					}
					else
					{
						if (count($categories) > 0)
						{
							$categorisation = $categories[0];
							$rslt = $categorisation->Remove(intval($this->id), null, $autocommit);
						}
						$this->m_categorisations = null;
					}
				}
				return $rslt;
			}
			
			public function GetIllustration($force = false)
			{
				if (($force || ($this->m_illustration === null)) && isset($this->id_illustration) && (intval($this->id_illustration) > 0))
				{
					$this->m_illustration = Illustration::Get(intval($this->id_illustration));
					if ($this->m_illustration === false)
						$this->m_illustration = null;
				}
				return $this->m_illustration;
			}
			
			public function InsertIllustration($idImage, $idGalerie, $position, $legende = null, $largeur = null, $hauteur = null, $autocommit = true)
			{
				if (isset($this->id) && (intval($this->id) > 0) && (!isset($this->id_illustration) || ($this->id_illustration === null)))
				{
					$this->m_illustration = new Illustration();
					if ($autocommit)
						$this->commit();
					$this->id_illustration = $this->m_illustration->Insert($idImage, $idGalerie, $position, $legende, $largeur, $hauteur, false);
					if ($this->id_illustration !== false)
					{
						$sql = 'UPDATE '.strtolower($this->m_class).' SET id_illustration = '.intval($this->id_illustration).' WHERE id = '.intval($this->id);
						$rslt = $this->getBase()->update($sql, false);
						if ($rslt !== false)
						{
							if ($autocommit)
								$this->commit();
						}
						else
						{
							if ($autocommit)
								$this->rollback();
							$this->id_illustration = null;
							$this->m_illustration = null;
						}
					}
					else
					{
						if ($autocommit)
							$this->rollback();
						$this->id_illustration = null;
						$this->m_illustration = null;
					}
					return $this->m_illustration;
				}
				return null;
			}
			
			public function UpdateIllustration($idImage, $idGalerie, $position, $legende = null, $largeur = null, $hauteur = null, $autocommit = true)
			{
				$illustration = $this->GetIllustration();
				if ($illustration !== null)
					return $this->m_illustration->Update($idImage, $idGalerie, $position, $legende, $largeur, $hauteur, $autocommit);
				return null;
			}
			
			public function DeleteIllustration($autocommit = true)
			{
				$illustration = $this->GetIllustration();
				if ($illustration !== null)
				{
					if ($autocommit)
						$this->commit();
					$sql = 'UPDATE '.strtolower($this->m_class).' SET id_illustration = NULL WHERE id = '.intval($this->id);
					$rslt = $this->getBase()->update($sql, false);
					if ($rslt !== false)
					{
						$rslt = Illustration::Delete($illustration->id, false);
						if ($rslt !== false)
						{
							if ($autocommit)
								$this->commit();
							$this->id_illustration = null;
							$this->m_illustration = null;
							$rslt = true;
						}
						else
						{
							if ($autocommit)
								$this->rollback();
						}
					}
					else
					{
						if ($autocommit)
							$this->rollback();
					}
					return $rslt;
				}
				return null;
			}
			
			public function InsertIllustrationParagraphe($idParagraphe, $idImage, $idGalerie, $position, $legende = null, $largeur = null, $hauteur = null, $autocommit = true)
			{
				$paragraphe = $this->GetParagraphe($idParagraphe);
				if ($paragraphe !== null)
					return $paragraphe->InsertIllustration($idImage, $idGalerie, $position, $legende, $largeur, $hauteur, $autocommit);
				return null;
			}
			
			public function UpdateIllustrationParagraphe($idParagraphe, $idImage, $idGalerie, $position, $legende = null, $largeur = null, $hauteur = null, $autocommit = true)
			{
				$paragraphe = $this->GetParagraphe($idParagraphe);
				if ($paragraphe !== null)
					return $paragraphe->UpdateIllustration($idImage, $idGalerie, $position, $legende, $largeur, $hauteur, $autocommit);
				return null;
			}
			
			public function DeleteIllustrationParagraphe($idParagraphe, $autocommit = true)
			{
				$paragraphe = $this->GetParagraphe($idParagraphe);
				if ($paragraphe !== null)
					return $paragraphe->DeleteIllustration($autocommit);
				return null;
			}
			
			public function &GetParagraphe($idParagraphe)
			{
				if (intval($idParagraphe) > 0)
				{
					foreach ($this->GetParagraphes() as $paragraphe)
					{
						if ($paragraphe->id == $idParagraphe)
							return $paragraphe;
					}
				}
				return null;
			}
			
			public function GetCharte()
			{
				if (($this->m_charte === null) && ($this->id_charte !== null))
				{
					$charte = new Charte();
					$this->m_charte = $charte->Charge($this->id_charte);
				}
				return $this->m_charte;
			}
			
			public static function	Get($id)
			{
				$article = new Article();
				return $article->Charge($id);
			}
			
			/*public static function	GetPresentation()
			{
				$class = 'Article';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE id = 1';
				$pres = $base->selectOne($sql, $class);
				$base->FermerBase();
				if ($pres !== false)
					return $pres;
				return null;
			}*/
			
			public static function	Search($idCategories = null, $brouillons = false, $archives = false, $filtre = null, $limit = null)
			{
				$class = 'Article';
				$base = BD::OuvrirBase();
				$brouillonsClause = '1 = 1';
				if ($brouillons == false)
					$brouillonsClause = '(NOT publication IS NULL AND publication <= NOW())';
				$archivesClause = '1 = 1';
				if ($archives == false)
					$archivesClause = '(expiration IS NULL OR expiration > NOW())';

				if ($idCategories === null)
					$idCategories = array();
				if (is_array($idCategories))
				{
					if (count($idCategories) > 0)
						$sql = 'SELECT DISTINCT a.* FROM '.strtolower($class).' a LEFT JOIN categorisation c ON c.id_article = a.id WHERE (c.id_categorie IS NULL OR c.id_categorie IN ('.implode(',', $idCategories).')) AND '.$brouillonsClause.' AND '.$archivesClause;
					else
						$sql = 'SELECT DISTINCT a.* FROM '.strtolower($class).' a LEFT JOIN categorisation c ON c.id_article = a.id WHERE c.id_categorie IS NULL AND '.$brouillonsClause.' AND '.$archivesClause;
				}
				else
					$sql = 'SELECT DISTINCT a.* FROM '.strtolower($class).' a LEFT JOIN categorisation c ON c.id_article = a.id WHERE (c.id_categorie IS NULL OR c.id_categorie = '.intval($idCategories).') AND '.$brouillonsClause.' AND '.$archivesClause;
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
							$sql .= ' (titre LIKE \'%'.$base->escapeString($f).'%\' OR resume LIKE \'%'.$base->escapeString($f).'%\')';
						}
						$sql .= ')';
					}
				}
				$sql .= ' ORDER BY publication DESC, id DESC';
				if ($limit !== null)
					$sql .= ' LIMIT '.intval($limit);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function	testTitreExist($titre, $id = null)
			{
				$class = 'Article';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE LOWER(titre) = LOWER(\''.$base->escapeString($titre).'\')';
				if ($id !== null)
					$sql .= ' AND id != '.intval($id);
				$test = $base->selectOne($sql, $class);
				$base->FermerBase();
				if ($test !== false)
					return true;
				return false;
			}
			
			public static function	getIdByTitre($titre)
			{
				$class = 'Article';
				$base = BD::OuvrirBase();
				$sql = 'SELECT id FROM '.strtolower($class).' WHERE LOWER(titre) = LOWER(\''.$base->escapeString($titre).'\')';
				$id = $base->selectOne($sql, $class);
				$base->FermerBase();
				if ($id !== false)
					return $id;
				return null;
			}
			
			public function BuildAriane()
			{
				return Menu::buildAriane($this->id);
			}
		}
	}
?>