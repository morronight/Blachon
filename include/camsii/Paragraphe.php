<?php
	if (!class_exists('Paragraphe'))
	{
		require_once 'Table.php';

		class	Paragraphe	extends Table
		{
			public $m_illustration;
			
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Paragraphe';
				$this->m_illustration = null;
			}

			public function __destruct()
			{
				parent::__destruct();
			}

			public function Insert($articleId, $titre, $texte, $autocommit = true)
			{
				if ($titre !== null)
					$this->titre = substr($titre, 0, 255);
				else
					$this->titre = null;
				$this->texte = $texte;
				$this->id_article = intval($articleId);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (id_article, texte, titre) VALUES ('.$this->id_article.', \''.$this->getBase()->escapeString($this->texte).'\', ';
				if ($this->titre === null)
					$sql .= 'NULL';
				else
					$sql .= '\''.$this->getBase()->escapeString($this->titre).'\'';
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}

			public function Update($titre, $texte, $autocommit = true)
			{
				$id = $this->id;
				$articleId = $this->id_article;
				if (($id === null) || ($articleId === null))
					return false;
				$rslt = null;
				if ((($this->titre === null) && ($titre !== null)) || (($this->titre !== null) && ($titre === null)) || (($this->titre !== null) && ($titre !== null) && ($this->titre != substr($titre, 0, 128))) || ($this->texte != $texte))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET';
					if ($titre !== null)
						$sql .= ' titre = \''.$this->getBase()->escapeString(substr($titre, 0, 128)).'\',';
					else
						$sql .= ' titre = NULL,';
					$sql .= ' texte = \''.$this->getBase()->escapeString($texte).'\' WHERE id = '.intval($id).' AND id_article = '.intval($articleId);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						if ($titre !== null)
							$this->titre = substr($titre, 0, 128);
						else
							$this->titre = null;
						$this->texte = $texte;
						$rslt = true;
					}
				}
				return $rslt;
			}
			
			public static function	GetListe($articleId)
			{
				$class = 'Paragraphe';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_article = '.intval($articleId).' ORDER BY id';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public function	Delete($articleId = null, $id = null, $autocommit = true)
			{
				if ($articleId === null)
					$articleId = $this->id_article;
				if ($id === null)
					$id = $this->id;
				if (($id === null) || ($articleId === null))
					return false;
				if ($autocommit)
					$this->commit();
				$rslt = null;
				$tableaux = Tableau::GetListe($articleId, $id);
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
						$sql = 'DELETE FROM '.strtolower($this->m_class).' WHERE id_article = '.intval($articleId).' AND id = '.intval($id);
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
				return $rslt;
			}
			
			public function GetIllustration()
			{
				if (($this->m_illustration === null) && isset($this->id_illustration) && (intval($this->id_illustration) > 0))
				{
					$illustration = new Illustration();
					$this->m_illustration = $illustration->Charge(intval($this->id_illustration));
					if ($this->m_illustration === false)
						$this->m_illustration = null;
				}
				return $this->m_illustration;
			}
			
			public function InsertIllustration($idImage, $idGalerie, $position, $legende = null, $largeur = null, $hauteur = null, $autocommit = true)
			{
				if (isset($this->id) && (intval($this->id) > 0) && (!isset($this->id_illustration) || ($this->id_illustration === null) || ($this->GetIllustration() === null)))
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
		}
	}
?>