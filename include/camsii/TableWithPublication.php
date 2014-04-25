<?php
	if (!class_exists('TableWithPublication'))
	{
		require_once 'Table.php';

		class	TableWithPublication	extends Table
		{
			public function	__construct($pkey)
			{
				parent::__construct($pkey);
			}

			public function __destruct()
			{
				parent::__destruct();
			}
			
			public function Publier($date = null, $id = null, $autocommit = true)
			{
				if ($id === null)
					eval('$id = $this->'.$this->m_pkey.';');
				if ($id === null)
					return false;
				if ($date === null)
					$sql = 'UPDATE '.strtolower($this->m_class).' SET publication = NOW(), expiration = NULL WHERE '.$this->m_pkey.' = '.intval($id);
				else
					$sql = 'UPDATE '.strtolower($this->m_class).' SET publication = \''.$this->getBase()->escapeString($date).'\', expiration = NULL WHERE '.$this->m_pkey.' = '.intval($id);
				$rslt = $this->getBase()->update($sql, $autocommit);
				if ($rslt !== false)
				{
					if ($date === null)
						$this->publication = date('Y-m-d H:i:s');
					else
						$this->publication = $date;
					$rslt = true;
				}
				return $rslt;
			}
			
			public function Archiver($date = null, $id = null, $autocommit = true)
			{
				if ($id === null)
					eval('$id = $this->'.$this->m_pkey.';');
				if ($id === null)
					return false;
				if ($date === null)
					$sql = 'UPDATE '.strtolower($this->m_class).' SET expiration = NOW(), publication = IFNULL(publication, NOW()) WHERE '.$this->m_pkey.' = '.intval($id);
				else
					$sql = 'UPDATE '.strtolower($this->m_class).' SET expiration = \''.$this->getBase()->escapeString($date).'\', publication = IFNULL(publication, \''.$this->getBase()->escapeString($date).'\') WHERE '.$this->m_pkey.' = '.intval($id);
				$rslt = $this->getBase()->update($sql, $autocommit);
				if ($rslt !== false)
				{
					if ($date === null)
						$this->expiration = date('Y-m-d H:i:s');
					else
						$this->expiration = $date;
					$rslt = true;
				}
				return $rslt;
			}
			
			public function Desarchiver($date = null, $id = null, $autocommit = true)
			{
				if ($id === null)
					eval('$id = $this->'.$this->m_pkey.';');
				if ($id === null)
					return false;
				if ($date === null)
					$sql = 'UPDATE '.strtolower($this->m_class).' SET expiration = NULL WHERE '.$this->m_pkey.' = '.intval($id);
				else
					$sql = 'UPDATE '.strtolower($this->m_class).' SET expiration = \''.$this->getBase()->escapeString($date).'\' WHERE '.$this->m_pkey.' = '.intval($id);
				$rslt = $this->getBase()->update($sql, $autocommit);
				if ($rslt !== false)
				{
					$this->expiration = $date;
					$rslt = true;
				}
				return $rslt;
			}
			
			public function Depublier($date = null, $id = null, $autocommit = true)
			{
				if ($id === null)
					eval('$id = $this->'.$this->m_pkey.';');
				if ($id === null)
					return false;
				if ($date === null)
					$sql = 'UPDATE '.strtolower($this->m_class).' SET publication = NULL, expiration = NULL WHERE '.$this->m_pkey.' = '.intval($id);
				else
					$sql = 'UPDATE '.strtolower($this->m_class).' SET publication = \''.$this->getBase()->escapeString($date).'\', expiration = NULL WHERE '.$this->m_pkey.' = '.intval($id);
				$rslt = $this->getBase()->update($sql, $autocommit);
				if ($rslt !== false)
				{
					$this->publication = $date;
					$rslt = true;
				}
				return $rslt;
			}
			
			protected static function	GetListeBrouillon($class, $pkey = 'id')
			{
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE publication IS NULL OR publication > NOW() AND (expiration IS NULL OR expiration > NOW()) ORDER BY publication DESC, '.$pkey.' DESC';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}

			protected static function	GetListeArchive($class, $pkey = 'id')
			{
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE expiration <= NOW() ORDER BY publication DESC, '.$pkey.' DESC';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public function GetDatePublication()
			{
				$rslt = '-';
				if (isset($this->publication))
					$rslt = substr($this->publication, 8, 2).'/'.substr($this->publication, 5, 2).'/'.substr($this->publication, 0, 4);
				return $rslt;
			}
					
			public function GetDateExpiration()
			{
				$rslt = '-';
				if (isset($this->expiration) && ($this->expiration !== null))
					$rslt = substr($this->expiration, 8, 2).'/'.substr($this->expiration, 5, 2).'/'.substr($this->expiration, 0, 4);
				return $rslt;
			}
			
			public function IsBrouillon()
			{
				$rslt = false;
				if (!isset($this->publication) && ($this->publication == null) && !isset($this->expiration))
					$rslt = true;
				return $rslt;
			}
			
			public function IsArchive()
			{
				$rslt = false;
				if (isset($this->publication) && ($this->expiration !== null) && ($this->expiration <= date('Y-m-d H:i:s')))
					$rslt = true;
				return $rslt;
			}
			
			public function IsPublished()
			{
				$rslt = false;
				if (isset($this->publication) && ($this->publication !== null) && ($this->expiration == null) && ($this->publication <= date('Y-m-d H:i:s')))
					$rslt = true;
				return $rslt;
			}
			
			public function IsPlanned()
			{
				$rslt = false;
				if (isset($this->publication) && (!isset($this->expiration)) && ($this->publication >= date('Y-m-d H:i:s')))
					$rslt = true;
				return $rslt;
			}
			
			public function canExpire()
			{
				$rslt = false;
				if (isset($this->expiration) && ($this->expiration !== null))
					$rslt = true;
				return $rslt;
			}
		}
	}
?>