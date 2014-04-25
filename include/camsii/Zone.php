<?php
	if (!class_exists('Zone'))
	{
		require_once('Table.php');

		class	Zone	extends Table
		{
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Zone';
			}

			public function __destruct()
			{
				parent::__destruct();
			}
		
			public static function	GetListe($idCharte)
			{
				$class = 'Zone';
				$base = BD::OuvrirBase();
				$sql = 'SELECT z.* FROM relation_charte_zone r JOIN '.strtolower($class);
				$sql .= ' z ON r.id_zone = z.id WHERE r.id_charte = '.intval($idCharte);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}			
		}
	}
?>