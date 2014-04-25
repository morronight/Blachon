<?php
	if (!class_exists('Page'))
	{
		require_once('include/Configuration.php');
		require_once('Table.php');
		require_once 'Formatage.php';

		class	Page	extends Table
		{
			protected	$m_charte;
			
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Page';
				$this->m_charte = null;
			}

			public function __destruct()
			{
				parent::__destruct();
			}

			public function	ChargeDepuisShortPath($nom)
			{
				$sql = $this->prepare_ChargeDepuisShortPath($nom);
				return $this->getBase()->selectOne($sql, $this->m_class);
			}
			
			public static function	GetListe()
			{
				$class = 'Page';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			protected function	prepare_ChargeDepuisShortPath($nom)
			{
				$sql = null;
				if ($this->getBase() !== false)
					$sql = 'SELECT * FROM '.strtolower(get_class($this)).' WHERE short_path = \''.$this->getBase()->escapeString($nom).'\'';
				return $sql;
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
				$page = new Page();
				return $page->Charge($id);
			}
		}
	}
?>