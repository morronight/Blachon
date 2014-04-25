<?php
	if (!class_exists('ChoixReponse'))
	{
		require_once 'Table.php';

		class	ChoixReponse	extends Table
		{
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'ChoixReponse';
			}

			public function __destruct()
			{
				parent::__destruct();
			}
			
			public static function	GetListe($questionId = null)
			{
				$class = 'ChoixReponse';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class);
				if ($questionId !== null)
					$sql .= ' WHERE question_id = '.intval($questionId);
				$sql .= ' ORDER BY question_id asc, ordre asc, id asc';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public function Insert($questionId, $reponse, $aide = null, $ordre = null, $autocommit = true)
			{
				$this->question_id = intval($questionId);
				$this->reponse = substr($reponse, 0, 128);
				$this->aide = $aide;
				if ($ordre !== null)
					$this->ordre = intval($ordre);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (question_id, reponse, aide, ordre) VALUES ('.intval($this->question_id).', \''.$this->getBase()->escapeString($this->reponse).'\'';
				if ($this->aide === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->aide).'\'';
				$sql .= ', '.intval($this->ordre);
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}
			
			public function Update($questionId, $reponse, $aide = null, $ordre = null, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if ((intval($this->question_id) != intval($questionId))
				|| ($this->reponse != substr($reponse, 0, 128))
				|| (($this->aide !== null) && ($aide === null))
				|| (($this->aide === null) && ($aide !== null))
				|| (($aide !== null) && ($this->aide != $aide))
				|| (($this->ordre !== null) && ($ordre === null))
				|| (($this->ordre === null) && ($ordre !== null))
				|| (($ordre !== null) && (intval($this->ordre) != intval($ordre))))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET question_id = '.intval($questionId).', reponse = \''.$this->getBase()->escapeString(substr($reponse, 0, 128)).'\'';
					if ($aide === null)
						$sql .= ', aide = NULL';
					else
						$sql .= ', aide = \''.$this->getBase()->escapeString($aide).'\'';
					if ($ordre === null)
						$sql .= ', ordre = NULL';
					else
						$sql .= ', ordre = '.intval($ordre);
					$sql .= ' WHERE id = '.intval($id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->reponse = substr($reponse, 0, 128);
						$this->aide = $aide;
						if ($ordre !== null)
							$this->ordre = intval($ordre);
						else
							$this->ordre = null;
						$rslt = true;
					}
				}
				return $rslt;
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
			
			public static function	Get($id)
			{
				$choixReponse = new ChoixReponse();
				return $choixReponse->Charge($id);
			}
		}
	}
?>