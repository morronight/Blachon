<?php
	if (!class_exists('Formulaire'))
	{
		require_once 'Table.php';
		require_once 'Lieu.php';
		require_once 'Charte.php';
		require_once 'Formatage.php';

		class	Formulaire	extends Table
		{
			protected 	$m_charte;
		
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Formulaire';
				$this->m_charte = null;
			}

			public function __destruct()
			{
				parent::__destruct();
			}
			
			public static function	GetListe()
			{
				$class = 'Formulaire';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public function Insert($nom, $description, $societe, $nb, $ressource, $horaire, $charteId = 1, $autocommit = true)
			{
				$this->nom = substr($nom, 0, 64);
				$this->description = $description;
				$this->societe = null;
				if ($societe !== null)
					$this->societe = substr($societe, 0, 32);
				$this->nb = null;
				if ($nb !== null)
					$this->nb = substr($nb, 0, 32);
				$this->ressource = null;
				if ($ressource !== null)
					$this->ressource = substr($ressource, 0, 32);
				$this->horaire = null;
				if ($horaire !== null)
					$this->horaire = substr($horaire, 0, 32);
				$this->id_charte = intval($charteId);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (nom, description, societe, nb, ressource, horaire, id_charte) VALUES (\''.$this->getBase()->escapeString($this->nom).'\'';
				if ($this->description === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->description).'\'';
				if ($this->societe === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->societe).'\'';
				if ($this->nb === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->nb).'\'';
				if ($this->ressource === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->ressource).'\'';
				if ($this->horaire === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->horaire).'\'';
				$sql .= ', '.intval($this->id_charte);
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}
			
			public function Update($nom, $description, $societe, $nb, $ressource, $horaire, $charteId, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if (($this->nom != substr($nom, 0, 64))
				|| (($this->description !== null) && ($description === null))
				|| (($this->description === null) && ($description !== null))
				|| ($this->description != $description)
				|| (($this->societe !== null) && ($societe === null))
				|| (($this->societe === null) && ($societe !== null))
				|| ($this->societe != substr($societe, 0, 32))
				|| (($this->nb !== null) && ($nb === null))
				|| (($this->nb === null) && ($nb !== null))
				|| ($this->nb != substr($nb, 0, 32))
				|| (($this->ressource !== null) && ($ressource === null))
				|| (($this->ressource === null) && ($ressource !== null))
				|| ($this->ressource != substr($ressource, 0, 32))
				|| (($this->horaire !== null) && ($horaire === null))
				|| (($this->horaire === null) && ($horaire !== null))
				|| ($this->horaire != substr($horaire, 0, 32))
				|| (intval($this->id_charte) != intval($charteId)))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET nom = \''.$this->getBase()->escapeString(substr($nom, 0, 64)).'\'';
					if ($description !== null)
						$sql .= ', description = \''.$this->getBase()->escapeString($description).'\'';
					else
						$sql .= ', description = NULL';
					if ($societe !== null)
						$sql .= ', societe = \''.$this->getBase()->escapeString(substr($societe, 0, 32)).'\'';
					else
						$sql .= ', societe = NULL';
					if ($nb !== null)
						$sql .= ', nb = \''.$this->getBase()->escapeString(substr($nb, 0, 32)).'\'';
					else
						$sql .= ', nb = NULL';
					if ($ressource !== null)
						$sql .= ', ressource = \''.$this->getBase()->escapeString(substr($ressource, 0, 32)).'\'';
					else
						$sql .= ', ressource = NULL';
					if ($horaire !== null)
						$sql .= ', horaire = \''.$this->getBase()->escapeString(substr($horaire, 0, 32)).'\'';
					else
						$sql .= ', horaire = NULL';
					$sql .= ', id_charte = '.intval($charteId);
					$sql .= ' WHERE id = '.intval($id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->nom = substr($nom, 0, 64);
						$this->description = $description;
						$this->societe = null;
						if ($societe !== null)
							$this->societe = substr($societe, 0, 32);
						$this->nb = null;
						if ($nb !== null)
							$this->nb = substr($nb, 0, 32);
						$this->ressource = null;
						if ($ressource !== null)
							$this->ressource = substr($ressource, 0, 32);
						$this->horaire = null;
						if ($horaire !== null)
							$this->horaire = substr($horaire, 0, 32);
						$this->id_charte = intval($charteId);
						$rslt = true;
					}
				}
				return $rslt;
			}
			
			public function	GetLieux()
			{
				$lieux = Lieu::GetListe(intval($this->id));
				return $lieux;
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
				require_once 'Article.php';
				return Formatage::Lien($this->nom);
			}
			
			public static function	Get($id)
			{
				$formulaire = new Formulaire();
				return $formulaire->Charge($id);
			}
		}
	}
?>