<?php
	if (!class_exists('Lieu'))
	{
		require_once('Formulaire.php');
		require_once('Horaire.php');

		class	Lieu	extends Table
		{
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Lieu';
			}

			public function __destruct()
			{
				parent::__destruct();
			}
			
			public function Insert($formulaireId, $nom, $nb, $autocommit = true)
			{
				$this->id_formulaire = null;
				if ($formulaireId !== null)
					$this->id_formulaire = intval($formulaireId);
				$this->nom = substr($nom, 0, 64);
				$this->nb = null;
				if ($nb !== null)
					$this->nb = intval($nb);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (id_formulaire, nom, nb) VALUES ('.intval($this->id_formulaire).', \''.$this->getBase()->escapeString($this->nom).'\'';
				if ($this->nb !== null)
					$sql .= ', '.intval($this->nb);
				else
					$sql .= ', NULL';
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}
			
			public function Update($nom, $nb, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if (($this->nom != substr($nom, 0, 64))
				|| (($nb === null) && ($this->nb !== null))
				|| (($nb !== null) && ($this->nb === null))
				|| (intval($nb) != intval($this->nb)))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET nom = \''.$this->getBase()->escapeString(substr($nom, 0, 64)).'\'';
					if ($nb !== null)
						$sql .= ', nb = '.intval($nb);
					else
						$sql .= ', nb = NULL';
					$sql .= ' WHERE id = '.intval($id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->nom = substr($nom, 0, 64);
						if ($nb !== null)
							$this->nb = intval($nb);
						else
							$this->nb = null;
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
				$rslt = null;
				$sql = 'DELETE FROM '.strtolower($this->m_class).' WHERE id = '.intval($id);
				$rslt = $this->getBase()->deleteOne($sql, $autocommit);
				if ($rslt !== false)
					$this->id = null;
				return $rslt;
			}
			
			public static function	GetListe($formulaireId)
			{
				$class = 'Lieu';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_formulaire = '.intval($formulaireId);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public function	GetHoraires()
			{
				$horaires = Horaire::GetListe(intval($this->id));
				return $horaires;
			}
			
			public function GetNbLibre()
			{
				$rslt = null;
				if ($this->nb !== null)
				{
					$nb = Inscription::GetNb($this->id, null);
					if (intval($nb) > 0)
						$rslt = intval($this->nb) - intval($nb);
					else
						$rslt = intval($this->nb);
				}
				return $rslt;
			}

			public static function	Get($id)
			{
				$lieu = new Lieu();
				return $lieu->Charge($id);
			}
		}
	}
?>