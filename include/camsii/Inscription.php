<?php
	if (!class_exists('Inscription'))
	{
		require_once('Formulaire.php');
		require_once('Lieu.php');

		class	Inscription	extends Table
		{
			protected	$m_lieu;
		
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Inscription';
				$this->m_lieux = null;
			}

			public function __destruct()
			{
				parent::__destruct();
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
			
			public function Insert($formulaireId, $nom, $prenom, $societe, $mail, $telephone, $lieuId, $nb, $horaire, $autocommit = true)
			{
				$this->nom = substr($nom, 0, 64);
				$this->prenom = substr($prenom, 0, 32);
				$this->mail = substr($mail, 0, 128);
				$this->telephone = null;
				if ($telephone !== null)
					$this->telephone = substr($telephone, 0, 16);
				$this->societe = null;
				if ($societe !== null)
					$this->societe = substr($societe, 0, 64);
				$this->nb = null;
				if (intval($nb) > 0)
					$this->nb = intval($nb);
				$this->horaire = null;
				if ($horaire !== null)
					$this->horaire = intval($horaire);
				$this->id_lieu = intval($lieuId);
				$this->id_formulaire = intval($formulaireId);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (id_formulaire, creation, nom, prenom, societe, mail, telephone, id_lieu, nb, horaire) VALUES ('.intval($this->id_formulaire).', NOW(), \''.$this->getBase()->escapeString($this->nom).'\', \''.$this->getBase()->escapeString($this->prenom).'\'';
				if ($this->societe === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->societe).'\'';
				$sql .= ', \''.$this->getBase()->escapeString($this->mail).'\'';
				if ($this->telephone === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->telephone).'\'';
				$sql .= ', '.intval($this->id_lieu);
				if ($this->nb === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.intval($this->nb);
				if ($this->horaire === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.intval($this->horaire);
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}

			public function Update($nom, $prenom, $societe, $mail, $telephone, $lieuId, $nb, $horaire, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if (($this->nom != substr($nom, 0, 64))
					|| ($this->prenom != substr($prenom, 0, 32))
					|| (($this->societe !== null) && ($societe === null))
					|| (($this->societe === null) && ($societe !== null))
					|| ($this->societe != substr($societe, 0, 64))
					|| ($this->mail != substr($mail, 0, 128))
					|| (($this->telephone !== null) && ($telephone === null))
					|| (($this->telephone === null) && ($telephone !== null))
					|| ($this->telephone != substr($telephone, 0, 16))
					|| (($this->nb !== null) && ($nb === null))
					|| (($this->nb === null) && ($nb !== null))
					|| (intval($this->nb) != intval($nb))
					|| (($this->horaire !== null) && ($horaire === null))
					|| (($this->horaire === null) && ($horaire !== null))
					|| (intval($this->horaire) != intval($horaire))
					|| (intval($this->id_lieu) != intval($lieuId))) {
					$sql = 'UPDATE '.strtolower($this->m_class).' SET nom = \''.$this->getBase()->escapeString(substr($nom, 0, 64)).'\', prenom = \''.$this->getBase()->escapeString(substr($prenom, 0, 32)).'\', \''.$this->getBase()->escapeString(substr($mail, 0, 128)).'\'';
					if ($telephone !== null)
						$sql .= ', telephone = \''.$this->getBase()->escapeString(substr($telephone, 0, 16)).'\'';
					else
						$sql .= ', telephone = NULL';
					if ($societe !== null)
						$sql .= ', societe = \''.$this->getBase()->escapeString(substr($societe, 0, 64)).'\'';
					else
						$sql .= ', societe = NULL';
					$sql .= ', id_lieu = '.intval($lieuId);
					if ($this->nb === null)
						$sql .= ', nb = NULL';
					else
						$sql .= ', nb = '.intval($this->nb);
					if ($this->horaire === null)
						$sql .= ', horaire = NULL';
					else
						$sql .= ', horaire = '.intval($this->horaire);
					$sql .= ' WHERE id = '.intval($id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->nom = substr($nom, 0, 64);
						$this->prenom = substr($prenom, 0, 32);
						$this->mail = substr($mail, 0, 128);
						$this->telephone = null;
						if ($telephone !== null)
							$this->telephone = substr($telephone, 0, 16);
						$this->societe = null;
						if ($societe !== null)
							$this->societe = substr($societe, 0, 64);
						$this->id_lieu = intval($lieuId);
						$this->nb = null;
						if (intval($nb) > 0)
							$this->nb = intval($nb);
						$this->horaire = null;
						if ($horaire !== null)
							$this->horaire = intval($horaire);
						$rslt = true;
					}
				}
				return $rslt;
			}

			public function GetLieu()
			{
				if (($this->m_lieu === null) && ($this->lieu !== null))
				{
					$lieu = new Lieu();
					$this->m_lieu = $lieu->Charge($this->lieu);
				}
				return $this->m_lieu;
			}
			
			public static function	GetListe($formulaireId, $lieuId = null)
			{
				$class = 'Inscription';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_formulaire = '.intval($formulaireId);
				if ($lieuId !== null)
					$sql .= ' AND id_lieu = '.intval($lieuId);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function	GetNb($lieuId = null, $horaireId = null)
			{
				$class = 'Inscription';
				$sql = 'SELECT SUM(nb) AS nb FROM '.strtolower($class).' WHERE NOT nb IS NULL';
				if ($lieuId !== null)
					$sql .= ' AND id_lieu = '.intval($lieuId);
				if ($horaireId !== null)
					$sql .= ' AND horaire = '.intval($horaireId);
				$base = BD::OuvrirBase();
				$rslt = $base->selectOne($sql);
				$base->FermerBase();
				return $rslt->nb;
			}			

			public static function	Get($id)
			{
				$inscription = new Inscription();
				return $inscription->Charge($id);
			}
		}
	}
?>