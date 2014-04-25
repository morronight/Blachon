<?php
	if (!class_exists('Reponse'))
	{
		require_once 'Table.php';

		class	Reponse	extends Table
		{
			public function	__construct()
			{
				parent::__construct(null);
				$this->m_class = 'Reponse';
			}

			public function __destruct()
			{
				parent::__destruct();
			}
			
			public static function	GetListe($questionnaireId = null, $questionId = null, $id = null)
			{
				$class = 'Reponse';
				$base = BD::OuvrirBase();
				$sql = 'SELECT r.* FROM '.strtolower($class).' r LEFT JOIN question q ON q.id = r.question_id WHERE 1=1';
				if ($questionnaireId !== null)
					$sql .= ' AND r.questionnaire_id = '.intval($questionnaireId);
				if ($questionId !== null)
					$sql .= ' AND r.question_id = '.intval($questionId);
				if ($id !== null)
					$sql .= ' AND r.id = '.intval($id);
				$sql .= ' ORDER BY r.questionnaire_id asc, r.id asc, q.ordre asc';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function	GetResultats($questionnaireId, $questionId = null)
			{
				$class = 'Reponse';
				$base = BD::OuvrirBase();
				$sql = 'SELECT r.questionnaire_id, r.question_id, r.reponse, COUNT(r.id) as nombre FROM '.strtolower($class).' r LEFT JOIN question q ON q.id = r.question_id WHERE r.questionnaire_id = '.intval($questionnaireId);
				if ($questionId !== null)
					$sql .= ' AND r.question_id = '.intval($questionId);
				$sql .= ' GROUP BY r.questionnaire_id, r.question_id, r.reponse ORDER BY r.questionnaire_id asc, q.ordre asc, nombre desc';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function	GetResultatsComplets($questionnaireId, $questionId = null)
			{
				$class = 'Reponse';
				$base = BD::OuvrirBase();
				$sql = 'SELECT u.id, q.questionnaire_id, q.id AS question_id, r.reponse FROM (SELECT DISTINCT id FROM '.strtolower($class).' WHERE questionnaire_id = '.intval($questionnaireId).') u JOIN (SELECT * FROM question WHERE questionnaire_id = '.intval($questionnaireId).') q LEFT JOIN '.strtolower($class).' r ON (r.question_id = q.id AND r.id = u.id)';
				if ($questionId !== null)
					$sql .= ' WHERE q.id = '.intval($questionId);
				$sql .= ' ORDER BY q.ordre asc, u.id asc';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function	GetNombreResultats($questionnaireId, $questionId = null)
			{
				$class = 'Reponse';
				$base = BD::OuvrirBase();
				$sql = 'SELECT COUNT(*) as nombre FROM (SELECT DISTINCT id FROM '.strtolower($class).' WHERE questionnaire_id = '.intval($questionnaireId);
				if ($questionId !== null)
					$sql .= ' AND question_id = '.intval($questionId);
				$sql .= ') u';
				$nombre = $base->selectOne($sql);
				$base->FermerBase();
				if ($nombre === false)
					return false;
				return intval($nombre->nombre);
			}
			
			public function Insert($id, $questionnaireId, $questionId, $reponse, $autocommit = true)
			{
				$this->id = null;
				if (($id !== null) && (intval($id) > 0))
					$this->id = intval($id);
				$this->questionnaire_id = intval($questionnaireId);
				$this->question_id = intval($questionId);
				$this->reponse = $reponse;
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (questionnaire_id, question_id, id, reponse) VALUES ('.intval($this->questionnaire_id).', '.intval($this->question_id);
				if ($this->id === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.intval($this->id);
				if ($this->reponse === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->reponse).'\'';
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}
		}
	}
?>