<?php
	if (!class_exists('BD'))
	{
		require_once 'include/Configuration.php';
		
		class	BD
		{
			private	static $s_base = null;
			private	static $s_log = false;
			private		$link = null;
			private		$base;
			private		$login;
			private		$pwd;
			private		$host;
			private		$counter;
			private		$transaction;

			protected function	__construct()
			{
				$this->base = Configuration::$Mysql['base'];
				$this->login = Configuration::$Mysql['login'];
				$this->pwd = Configuration::$Mysql['pwd'];
				$this->host = Configuration::$Mysql['host'];
				$this->counter = 0;
				$this->transaction = 0;
				while (($this->link === null) || (($this->link === false) && (mysqli_connect_errno() == 1203)))
				{
					$this->link = mysqli_connect($this->host, $this->login, $this->pwd);
					if (($this->link === false) && (mysqli_connect_errno() != 1203))
					{
						error_log('Connexion impossible ('.mysqli_connect_errno().'): '.mysqli_connect_error());
						self::$s_base = null;
						return false;
					}
					else
					{
						if (($this->link === false) && (mysqli_connect_errno() == 1203))
							sleep(1);
					}
				}
				if (!mysqli_select_db($this->link, $this->base))
				{
					error_log('Erreur : '.mysqli_error($this->link));
					$this->FermerBase();
					return false;
				}
				mysqli_set_charset($this->link, 'utf8');
				if (self::$s_log === true)
					error_log('Nouvelle connexion ouverte');
			}

			public static function &OuvrirBase()
			{
				if (self::$s_base === null)
					self::$s_base = new BD();
				self::$s_base->counter++;
				if (self::$s_log === true)
					error_log(self::$s_base->counter.' lien(s)');
				return self::$s_base;
			}

			public function	selectOne($sql, $classname = null)
			{
				if (($this->link !== false) && ($sql !== null))
				{
					if (self::$s_log === true)
						error_log('selectOne : '.$sql);
					$resultat = mysqli_query($this->link, $sql);
					if (!$resultat)
					{
						error_log('Requête invalide : '.mysqli_error($this->link));
						error_log($sql);
						return false;
					}
					if (mysqli_num_rows($resultat) != 1)
						return false;
					if ($classname !== null)
						$ligne = mysqli_fetch_object($resultat, $classname);
					else
						$ligne = mysqli_fetch_object($resultat);
					if ($ligne === null)
					{
						error_log('Erreur de lecture de l\'enregistrement');
						error_log($sql);
						return false;
					}
					mysqli_free_result($resultat);
					return $ligne;
				}
				return false;
			}
			
			public function	insertOne($sql, $autocommit = true)
			{
				if (($this->link !== false) && ($sql !== null))
				{
					if ($autocommit === false)
					{
						if (self::$s_base->transaction == 0)
						{
							self::$s_base->counter++;
							if (self::$s_log === true)
								error_log(self::$s_base->counter.' lien(s)');
							self::$s_base->transaction = 1;
						}
					}
					mysqli_autocommit($this->link, $autocommit);
					if (self::$s_log === true)
						error_log('insertOne : '.$sql);
					$resultat = mysqli_query($this->link, $sql);
					if (!$resultat)
					{
						error_log('Requête invalide : '.mysqli_error($this->link));
						error_log($sql);
						return false;
					}
					return mysqli_insert_id($this->link);
				}
				return false;
			}
			
			public function error()
			{
				if ($this->link !== false)
					return mysqli_error($this->link);
				return null;
			}
			
			public function	deleteOne($sql, $autocommit = true)
			{
				if (($this->link !== false) && ($sql !== null))
				{
					if ($autocommit === false)
					{
						if (self::$s_base->transaction == 0)
						{
							self::$s_base->counter++;
							if (self::$s_log === true)
								error_log(self::$s_base->counter.' lien(s)');
							self::$s_base->transaction = 1;
						}
					}
					mysqli_autocommit($this->link, $autocommit);
					if (self::$s_log === true)
						error_log('deleteOne : '.$sql);
					$resultat = mysqli_query($this->link, $sql);
					if (!$resultat)
					{
						error_log('Requête invalide : '.mysqli_error($this->link));
						error_log($sql);
						return false;
					}
					return mysqli_affected_rows($this->link);
				}
				return false;
			}
			
			public function	update($sql, $autocommit = true)
			{
				if (($this->link !== false) && ($sql !== null))
				{
					if ($autocommit === false)
					{
						if (self::$s_base->transaction == 0)
						{
							self::$s_base->counter++;
							if (self::$s_log === true)
								error_log(self::$s_base->counter.' lien(s)');
							self::$s_base->transaction = 1;
						}
					}
					mysqli_autocommit($this->link, $autocommit);
					if (self::$s_log === true)
						error_log('update : '.$sql);
					$resultat = mysqli_query($this->link, $sql);
					if (!$resultat)
					{
						error_log('Requête invalide : '.mysqli_error($this->link));
						error_log($sql);
						return false;
					}
					return mysqli_affected_rows($this->link);
				}
				return false;
			}
			
			public function commit()
			{
				if (self::$s_log === true)
					error_log('Commit');
				if (self::$s_base->transaction > 0)
				{
					if (self::$s_base->counter > 0)
						self::$s_base->counter--;
					self::$s_base->transaction = 0;
					if (self::$s_log === true)
						error_log(self::$s_base->counter.' lien(s)');
				}
				if ($this->link !== false)
					return mysqli_commit($this->link);
				return false;
			}
			
			public function rollback()
			{
				if (self::$s_log === true)
					error_log('Rollback');
				if (self::$s_base->transaction > 0)
				{
					if (self::$s_base->counter > 0)
						self::$s_base->counter--;
					self::$s_base->transaction = 0;
					if (self::$s_log === true)
						error_log(self::$s_base->counter.' lien(s)');
				}
				if ($this->link !== false)
					return mysqli_rollback($this->link);
				return false;
			}
			
			public function	selectMany($sql, $classname = null)
			{
				$rslt = array();
				if (($this->link !== false) && ($sql !== null))
				{
					if (self::$s_log === true)
						error_log('selectMany : '.$sql);
					$resultat = mysqli_query($this->link, $sql);
					if (!$resultat)
					{
						error_log('Requête invalide : '.mysqli_error($this->link));
						error_log($sql);
						return false;
					}
					if (mysqli_num_rows($resultat) == 0)
						return $rslt;
					if ($classname !== null)
					{
						while (($ligne = mysqli_fetch_object($resultat, $classname)) !== null)
							$rslt[] = $ligne;
					}
					else
					{
						while (($ligne = mysqli_fetch_object($resultat)) !== null)
							$rslt[] = $ligne;
					}
					mysqli_free_result($resultat);
					return $rslt;
				}
				return false;
			}
			
			public function escapeString($str)
			{
				if ($this->link !== false)
				{
					return mysqli_real_escape_string($this->link, $str);
				}
				return $str;
			}
			
			public function FermerBase()
			{
				if (self::$s_log === true)
					error_log('Plus que '.($this->counter - 1).' lien(s)');
				if ($this->counter <= 1)
				{
					if ($this->link !== false)
					{
						if (self::$s_log === true)
							error_log('Fermeture de la connexion');
						mysqli_close($this->link);
						$this->link = false;
					}
					$this->counter = 0;
					self::$s_base = null;
				}
				else
					$this->counter--;
			}
		}
	}
?>