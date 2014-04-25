<?php
	if (!class_exists('Questionnaire'))
	{
		require_once 'TableWithPublication.php';
		require_once 'Charte.php';
		require_once 'Formatage.php';

		class	Questionnaire	extends TableWithPublication
		{
			protected 	$m_charte;
		
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Questionnaire';
				$this->m_charte = null;
			}

			public function __destruct()
			{
				parent::__destruct();
			}
			
			public static function	GetListe($brouillons = false, $archives = false, $limit = null)
			{
				$class = 'Questionnaire';
				$base = BD::OuvrirBase();
				$brouillonsClause = '1 = 1';
				if ($brouillons == false)
					$brouillonsClause = '(NOT publication IS NULL AND publication <= NOW())';
				$archivesClause = '1 = 1';
				if ($archives == false)
					$archivesClause = '(expiration IS NULL OR expiration > NOW())';
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE '.$brouillonsClause.' AND '.$archivesClause.' ORDER BY publication DESC, id DESC';
				if ($limit !== null)
					$sql .= ' LIMIT '.intval($limit);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public function Insert($nom, $description, $charteId = 1, $autocommit = true)
			{
				$this->nom = substr($nom, 0, 64);
				$this->description = $description;
				$this->id_charte = intval($charteId);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (nom, description, id_charte) VALUES (\''.$this->getBase()->escapeString($this->nom).'\'';
				if ($this->description === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->description).'\'';
				$sql .= ', '.intval($this->id_charte);
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}
			
			public function Update($nom, $description, $charteId, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if (($this->nom != substr($nom, 0, 64))
				|| (($this->description !== null) && ($description === null))
				|| (($this->description === null) && ($description !== null))
				|| ($this->description != $description)
				|| (intval($this->id_charte) != intval($charteId)))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET nom = \''.$this->getBase()->escapeString(substr($nom, 0, 64)).'\'';
					if ($description !== null)
						$sql .= ', description = \''.$this->getBase()->escapeString($description).'\'';
					else
						$sql .= ', description = NULL';
					$sql .= ', id_charte = '.intval($charteId);
					$sql .= ' WHERE id = '.intval($id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->nom = substr($nom, 0, 64);
						$this->description = $description;
						$this->id_charte = intval($charteId);
						$rslt = true;
					}
				}
				return $rslt;
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
			
			public function GetLien()
			{
				return Formatage::Lien($this->nom);
			}
			
			public static function	Get($id)
			{
				$questionnaire = new Questionnaire();
				return $questionnaire->Charge($id);
			}
			
			public static function	testTitreExist($titre, $id = null)
			{
				$class = 'Questionnaire';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE LOWER(nom) = LOWER(\''.$base->escapeString($titre).'\')';
				if ($id !== null)
					$sql .= ' AND id != '.intval($id);
				$test = $base->selectOne($sql, $class);
				$base->FermerBase();
				if ($test !== false)
					return true;
				return false;
			}
			
			public static function	GetListeBrouillon()
			{
				return TableWithPublication::GetListeBrouillon('Article');
			}

			public static function	GetListeArchive()
			{
				return TableWithPublication::GetListeArchive('Article');
			}
		}
	}
?>