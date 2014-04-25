<?php
	if (!class_exists('Bandeau'))
	{
		require_once 'include/camsii/Table.php';
		require_once 'include/camsii/Formatage.php';

		class	Bandeau	extends Table
		{
			public $actif;
			public $titre;
		
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Bandeau';
			}

			public function __destruct()
			{
				parent::__destruct();
			}

			public function Update($titre, $actif = 1, $autocommit = true)
			{
				$rslt = null;
				if ((intval($this->actif) != intval($actif)) || ($this->titre != substr($titre, 0, 255)))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET titre = \''.$this->getBase()->escapeString(substr($titre, 0, 255)).'\', actif = '.intval($actif);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->titre = substr($titre, 0, 128);
						$this->actif = intval($actif);
						$rslt = true;
					}
				}
				return $rslt;
			}
			
			protected function	prepare_Charge($id)
			{
				$sql = 'SELECT * FROM '.strtolower($this->m_class).' LIMIT 1';
				return $sql;
			}
			
			public static function	Get()
			{
				$bandeau = new Bandeau();
				return $bandeau->Charge(null);
			}
			
			public function EstActif()
			{
				if (intval($this->actif) == 1)
					return true;
				return false;
			}
		}
	}
?>