<?php
	if (!class_exists('Horaire'))
	{
		require_once('Formulaire.php');
		require_once('Inscription.php');

		class	Horaire	extends Table
		{
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Horaire';
			}

			public function __destruct()
			{
				parent::__destruct();
			}
			
			public static function	GetListe($lieuId)
			{
				$class = 'Horaire';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_lieu = '.intval($lieuId);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}			

			public function Insert($ressourceId, $libelle, $nb, $autocommit = true)
			{
				$this->id_lieu = null;
				if ($ressourceId !== null)
					$this->id_lieu = intval($ressourceId);
				$this->libelle = substr($libelle, 0, 64);
				$this->nb = null;
				if ($nb !== null)
					$this->nb = intval($nb);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (id_lieu, libelle, nb) VALUES ('.intval($this->id_lieu).', \''.$this->getBase()->escapeString($this->libelle).'\'';
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
			
			public function Update($libelle, $nb, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if (($this->libelle != substr($libelle, 0, 64))
				|| (($nb === null) && ($this->nb !== null))
				|| (($nb !== null) && ($this->nb === null))
				|| (intval($nb) != intval($this->nb)))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET libelle = \''.$this->getBase()->escapeString(substr($libelle, 0, 64)).'\'';
					if ($nb !== null)
						$sql .= ', nb = '.intval($nb);
					else
						$sql .= ', nb = NULL';
					$sql .= ' WHERE id = '.intval($id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->libelle = substr($libelle, 0, 64);
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
			
			public function GetNbLibre()
			{
				$rslt = null;
				if ($this->nb !== null)
				{
					$nb = Inscription::GetNb(null, $this->id);
					if (intval($nb) > 0)
						$rslt = intval($this->nb) - intval($nb);
					else
						$rslt = intval($this->nb);
				}
				return $rslt;
			}

			public static function	Get($id)
			{
				$horaire = new Horaire();
				return $horaire->Charge($id);
			}
		}
	}
?>