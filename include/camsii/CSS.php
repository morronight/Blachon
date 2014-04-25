<?php
	if (!class_exists('Css'))
	{
		require_once('Table.php');

		class	Css	extends Table
		{
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Css';
			}

			public function __destruct()
			{
				parent::__destruct();
			}
			
			public function Insert($charteId, $fichier, $ordre = null, $user_agent, $autocommit = true)
			{
				$this->fichier = substr($fichier, 0, 64);
			
				if ($charteId !== null)
					$this->id_charte = $charteId;
				else
					return false;
				if ($ordre !== null)
					$this->ordre = intval($ordre);
				else
					$this->ordre = null;
				if ($user_agent !== null && $user_agent !== "")
					$this->user_agent = $user_agent;
				else
					$this->user_agent = null;
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (id_charte, ordre, fichier, user_agent) VALUES (';
				$sql .= '\''.intval($this->id_charte).'\'';
				if ($this->ordre === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.$this->ordre;
				if ($this->fichier === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.'"'.$this->fichier.'"';
				if ($this->user_agent === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.'"'.$this->user_agent.'"';
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					return false;
			}
			
			public function Update($charteId, $fichier, $ordre, $autocommit = true)
			{
				$rslt = null;
				$sql = 'UPDATE '.strtolower($this->m_class).' SET ordre = \''.intval($ordre).'\'';
				$sql .= ' WHERE id_charte = '.intval($charteId);
				$sql .= ' AND fichier = "'.$this->getBase()->escapeString(substr($fichier, 0, 64)).'"';
				$rslt = $this->getBase()->update($sql, $autocommit);
				if ($rslt !== false)
					$rslt = true;
				return $rslt;
			}
		
			public static function	GetListe($idCharte)
			{
				$class = 'Css';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class);
				$sql .= ' WHERE id_charte = '.intval($idCharte);
				$sql .= ' ORDER BY ordre';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public function	GetListeFichiers()
			{
				$class = 'Css';
				$base = BD::OuvrirBase();
				$sql = 'SELECT DISTINCT fichier,user_agent FROM '.strtolower($class);
				$sql .= ' ORDER BY ordre';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public function Format()
			{
				$str = '';
				if (($this->user_agent === null) || (preg_match($this->user_agent, $_SERVER['HTTP_USER_AGENT']) > 0))
					$str = '<link href="'.Configuration::$Static['url'].'/Css/'.$this->fichier.'" rel="stylesheet" type="text/css"/>';
				return $str;
			}

			public function	Delete($idCharte = null, $css = null, $autocommit = true)
			{
				$class = 'CSS';
				if ($idCharte === null)
					return false;
				$sql = 'DELETE FROM '.strtolower($class).' WHERE id_charte = '.intval($idCharte).' AND fichier="'.$css.'"';
				$rslt = $this->getBase()->deleteOne($sql, $autocommit);
				return $rslt;
			}			
		}
	}
?>