<?php
	if (!class_exists('Credit'))
	{
		require_once('Table.php');

		class	Credit	extends Table
		{
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Credit';
			}

			public function __destruct()
			{
				parent::__destruct();
			}
		
			public static function	GetListe($idCharte)
			{
				$class = 'Credit';
				$base = BD::OuvrirBase();
				$sql = 'SELECT c.* FROM relation_charte_credit r JOIN '.strtolower($class);
				$sql .= ' c ON r.id_credit = c.id WHERE r.id_charte = '.intval($idCharte);
				$sql .= ' ORDER BY c.id';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public function Format()
			{
				$str = '';
				if ($this->texte !== null)
				{
					if ($this->url !== null)
					{
						$str .= '<a href="'.$this->url.'"';
						if ($this->title !== null)
							$str.= ' title="'.htmlentities($this->title, ENT_COMPAT, 'UTF-8').'"';
						$str .= '>';
					}
					else
						$str .= '<span>';
					$str .= htmlentities($this->texte, ENT_COMPAT, 'UTF-8');
					if ($this->url !== null)
						$str .= '</a>';
					else
						$str .= '</span>';
				}
				return $str;
			}			
		}
	}
?>