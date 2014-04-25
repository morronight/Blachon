<?php
	if (!class_exists('Utilisateur'))
	{
		require_once 'Table.php';
		
		class	Utilisateur	extends Table
		{
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Utilisateur';
			}

			public function __destruct()
			{
				parent::__destruct();
			}

			public static function	Get($id)
			{
				$utilisateur = new Utilisateur();
				return $utilisateur->Charge($id);
			}
			
			public function Insert($mail, $motdepasse, $droits = 0, $pseudo = null, $google_id = null, $autocommit = true)
			{
				$this->mail = substr($mail, 0, 255);
				$this->motdepasse = substr($motdepasse, 0, 64);
				$this->droits = intval($droits);
				if ($pseudo !== null)
					$this->pseudo = substr($pseudo, 0, 30);
				else
					$this->pseudo = null;
				if ($google_id !== null)
					$this->google_id = substr($google_id, 0, 30);
				else
					$this->google_id = null;
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (mail, motdepasse, droits, pseudo, google_id) VALUES (\''.$this->getBase()->escapeString($this->mail).'\', \''.$this->getBase()->escapeString($this->motdepasse).'\', '.$this->droits;
				if ($this->pseudo === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->pseudo).'\'';
				if ($this->google_id === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->google_id).'\'';
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}
			
			public function Remove($autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$sql = 'DELETE FROM '.strtolower($this->m_class).' WHERE id = '.intval($id);
				$rslt = $this->getBase()->deleteOne($sql, $autocommit);
				if ($rslt !== false)
					$this->id = null;
				return $rslt;
			}

			public function UpdateMotDePasse($mail, $motdepasse, $autocommit = true)
			{
				if (($this->id !== null) && (intval($this->id) > 0))
				{
					if ($motdepasse === null)
						return 'Le mot de passe ne peut être vide.';
					if (strlen($motdepasse) < 5)
						return 'Le mot de passe doit comporter au moins 5 caractères.';
					$query = 'UPDATE '.strtolower($this->m_class).' SET motdepasse = \''.$this->getBase()->escapeString($motdepasse).'\', mail = \''.$this->getBase()->escapeString(substr($mail, 0, 255)).'\' WHERE id = '.intval($this->id);
					$res = $this->getBase()->update($query, $autocommit);
					if ($res === false)
					{
						error_log(mysql_error());
						error_log($query);
						return 'Erreur lors de l\'enregistrement du nouveau mot de passe.';
					}
					$this->mail = substr($mail, 0, 255);
					$this->motdepasse = $motdepasse;
					return true;
				}
				return false;
			}

			public function Update($droits, $pseudo, $autocommit = true)
			{
				if ($this->id === null)
					return false;
				$rslt = null;
				if (($this->pseudo != substr($pseudo, 0, 30)) || (intval($droits) != intval($this->droits)))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET droits = '.intval($droits);
					if ($pseudo !== null)
						$sql .= ', pseudo = \''.$this->getBase()->escapeString(substr($pseudo, 0, 30)).'\'';
					else
						$sql .= ', pseudo = NULL';
					$sql .= ' WHERE id = '.intval($this->id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						if ($pseudo !== null)
							$this->pseudo = substr($pseudo, 0, 30);
						else
							$this->pseudo = null;
						$this->droits = intval($droits);
						$rslt = true;
					}
				}
				return $rslt;
			}
			
			public function UpdateGoogleId($google_id, $autocommit = true)
			{
				if ($this->id === null)
					return false;
				$rslt = null;
				if ($this->google_id != substr($google_id, 0, 30))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET google_id = '.$google_id;
					$sql .= ' WHERE id = '.intval($this->id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						if ($google_id !== null)
							$this->google_id = substr($google_id, 0, 30);
						else
							$this->google_id = null;
						$rslt = true;
					}
				}
				return $rslt;
			}
			
			public function UpdateFacebookId($facebook_id, $autocommit = true)
			{
				if ($this->id === null)
					return false;
				$rslt = null;
				if ($this->facebook_id != substr($facebook_id, 0, 30))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET facebook_id = '.$facebook_id;
					$sql .= ' WHERE id = '.intval($this->id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						if ($facebook_id !== null)
							$this->facebook_id = substr($facebook_id, 0, 30);
						else
							$this->facebook_id = null;
						$rslt = true;
					}
				}
				return $rslt;
			}
			
			public function UpdateTwitterId($twitter_id, $autocommit = true)
			{
				if ($this->id === null)
					return false;
				$rslt = null;
				if ($this->twitter_id != substr($twitter_id, 0, 30))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET twitter_id = '.$twitter_id;
					$sql .= ' WHERE id = '.intval($this->id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						if ($twitter_id !== null)
							$this->twitter_id = substr($twitter_id, 0, 30);
						else
							$this->twitter_id = null;
						$rslt = true;
					}
				}
				return $rslt;
			}

			public static function	Identify($mail, $key, $cipher)
			{
				$utilisateur = null;
				$class = 'Utilisateur';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE mail = \''.$base->escapeString($mail).'\'';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				if (is_array($liste))
				{
					foreach($liste as $entry)
					{
						if ((strlen($key) > 0) && (hash("sha256", $key.$entry->motdepasse) == $cipher))
							$utilisateur = $entry;
					}
				}
				return $utilisateur;
			}
			
			public static function	Exist($mail)
			{
				$utilisateur = null;
				$class = 'Utilisateur';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE mail = \''.$base->escapeString($mail).'\'';
				$utilisateur = $base->selectOne($sql, $class);
				$base->FermerBase();
				if (($utilisateur === null) || ($utilisateur === false))
					return false;
				return true;
			}
			
			public static function	GetListe($droits = null)
			{
				$class = 'Utilisateur';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class);
				if ($droits !== null)
					$sql .= ' WHERE droits = '.intval($droits);
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function	GetAccesGoogle($google_id)
			{
				$class = 'utilisateur';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class);
				$sql .= ' WHERE google_id = "'.$google_id.'"';
				$utilisateur = $base->selectOne($sql, $class);
				if($utilisateur === false)
					return false;	
				$base->FermerBase();
				return $utilisateur;
			}
			
			public static function	GetAccesFacebook($facebook_id)
			{
				$class = 'utilisateur';
				$base = BD::OuvrirBase();
				var_dump($facebook_id);
				$sql = 'SELECT * FROM '.strtolower($class);
				$sql .= ' WHERE facebook_id ='.$facebook_id;
				$utilisateur = $base->selectOne($sql, $class);
				if($utilisateur === false)
					return false;	
				$base->FermerBase();
				return $utilisateur;
			}
			
			public static function	GetAccesTwitter($twitter_id)
			{
				$class = 'utilisateur';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class);
				$sql .= ' WHERE twitter_id = "'.$twitter_id.'"';
				$utilisateur = $base->selectOne($sql, $class);
				if($utilisateur === false)
					return false;	
				$base->FermerBase();
				return $utilisateur;
			}
			
			public function DemandeMotDePasse()
			{
				if (($this->id !== null) && (intval($this->id) > 0) && ($this->mail !== null))
				{
					if ($this->UpdateMotDePasse($this->mail, date('c')) !== true)
						return false;
					$key = hash("sha256", strval($this->id).$this->mail.$this->motdepasse);
					$headers = 'From: "Cave Sebastien Blachon"<contact@caveblachon.fr>'.PHP_EOL;
					$headers .= 'Content-Type: text/plain; charset="utf-8"'.PHP_EOL;
					$msg = 'Une demande de changement de mot de passe pour votre compte sur le site Internet de la Cave Sebastien Blachon vient d\'être faite.'.PHP_EOL;
					$msg .= 'Afin de choisir votre nouveau mot de passe, veuillez vous rendre à l\'adresse '.Configuration::$Url.'/ChangeMotDePasse?id='.intval($this->id).'&key='.rawurlencode($key).PHP_EOL;
					$msg .= 'A bientôt sur Cave Sebastien Blachon.'.PHP_EOL;
					if(mail($this->mail, 'Demande de nouveau mot de passe pour le site '.Configuration::$Url, $msg, $headers) !== true)
					{
						error_log('Erreur lors de l\'envoi de la demande de nouveau mot de passe'.$this->mail.'.');
						return false;
					}
					return true;
				}
				return false;
			}
		}
	}
?>