<?php
	if (!class_exists('Question'))
	{
		require_once 'Table.php';

		class	Question	extends Table
		{
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Question';
			}

			public function __destruct()
			{
				parent::__destruct();
			}
			
			public static function	GetListe($questionnaireId = null)
			{
				$class = 'Question';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class);
				if ($questionnaireId !== null)
					$sql .= ' WHERE questionnaire_id = '.intval($questionnaireId);
				$sql .= ' ORDER BY questionnaire_id asc, ordre asc, id asc';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public function Insert($questionnaireId, $texte, $type, $autre = 0, $groupe = null, $aide = null, $ordre = null, $autocommit = true)
			{
				$this->questionnaire_id = intval($questionnaireId);
				$this->texte = substr($texte, 0, 255);
				$this->type = substr($type, 0, 16);
				$this->autre = intval($autre);
				if ($groupe !== null)
					$this->groupe = substr($groupe, 0, 128);
				$this->aide = $aide;
				if ($ordre !== null)
					$this->ordre = intval($ordre);
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (questionnaire_id, texte, type, autre, groupe, aide, ordre) VALUES ('.intval($this->questionnaire_id).', \''.$this->getBase()->escapeString($this->texte).'\', \''.$this->getBase()->escapeString($this->type).'\', '.intval($this->autre);
				if ($this->groupe === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->groupe).'\'';
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
			
			public function Update($questionnaireId, $texte, $type, $autre = 0, $groupe = null, $aide = null, $ordre = null, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if ((intval($this->questionnaire_id) != intval($questionnaireId))
				|| ($this->texte != substr($texte, 0, 255))
				|| ($this->type != substr($type, 0, 16))
				|| (intval($this->autre) != intval($autre))
				|| (($this->groupe !== null) && ($groupe === null))
				|| (($this->groupe === null) && ($groupe !== null))
				|| (($groupe !== null) && ($this->groupe != substr($groupe, 0, 128)))
				|| (($this->aide !== null) && ($aide === null))
				|| (($this->aide === null) && ($aide !== null))
				|| (($aide !== null) && ($this->aide != $aide))
				|| (($this->ordre !== null) && ($ordre === null))
				|| (($this->ordre === null) && ($ordre !== null))
				|| (($ordre !== null) && (intval($this->ordre) != intval($ordre))))
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET questionnaire_id = '.intval($questionnaireId).', texte = \''.$this->getBase()->escapeString(substr($texte, 0, 255)).'\', `type` = \''.$this->getBase()->escapeString(substr($type, 0, 16)).'\', autre = '.intval($autre);
					if ($groupe === null)
						$sql .= ', groupe = NULL';
					else
						$sql .= ', groupe = \''.$this->getBase()->escapeString($groupe).'\'';
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
						$this->texte = substr($texte, 0, 255);
						$this->type = substr($type, 0, 16);
						$this->autre = intval($autre);
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
				$question = new Question();
				return $question->Charge($id);
			}
		}
	}
?>