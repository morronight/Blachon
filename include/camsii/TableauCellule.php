<?php
	if (!class_exists('TableauCellule'))
	{
		require_once 'Table.php';
		require_once 'Tableau.php';

		class	TableauCellule	extends Table
		{
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'TableauCellule';
			}

			public function __destruct()
			{
				parent::__destruct();
			}

			public function Insert($tableauId, $ligne, $colonne, $texte, $style= null, $rowspan = null, $colspan = null, $autocommit = true)
			{
				if ($texte !== null)
					$this->texte = substr($texte, 0, 255);
				else
					$this->texte = null;
				$this->id_tableau = intval($tableauId);
				$this->ligne = intval($ligne);
				$this->colonne = intval($colonne);
				if ($rowspan !== null)
					$this->rowspan = intval($rowspan);
				else
					$this->rowspan = null;
				if ($colspan !== null)
					$this->colspan = intval($colspan);
				else
					$this->colspan = null;
				if ($style !== null)
					$this->style = substr($style, 0, 20);
				else
					$this->style = null;
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (id_tableau, ligne, colonne, rowspan, colspan, texte, style) VALUES ('.$this->id_tableau.', '.$this->ligne.', '.$this->colonne;
				if ($this->rowspan === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.$this->rowspan;
				if ($this->colspan === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.$this->colspan;
				if ($this->texte === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->texte).'\'';
				if ($this->style === null)
					$sql .= ', NULL';
				else
					$sql .= ', \''.$this->getBase()->escapeString($this->style).'\'';
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}

			public function Update($ligne, $colonne, $texte, $style= null, $rowspan = null, $colspan = null, $autocommit = true)
			{
				$id = $this->id;
				$tableauId = $this->id_tableau;
				if (($id === null) || ($tableauId === null))
					return false;
				$rslt = null;
				if ((intval($this->ligne) != intval($ligne))
				|| (intval($this->colonne) != intval($colonne))
				|| (($this->texte === null) && ($texte !== null))
				|| (($this->texte !== null) && ($texte === null))
				|| (($this->texte !== null) && ($texte !== null) && ($this->texte != substr($texte, 0, 255)))
				|| (($this->style === null) && ($style !== null))
				|| (($this->style !== null) && ($style === null))
				|| (($this->style !== null) && ($style !== null) && ($this->style != substr($style, 0, 20)))
				|| (($this->rowspan === null) && ($rowspan !== null))
				|| (($this->rowspan !== null) && ($rowspan === null))
				|| (($this->rowspan !== null) && ($rowspan !== null) && (intval($this->rowspan) != intval($rowspan)))
				|| (($this->colspan === null) && ($colspan !== null))
				|| (($this->colspan !== null) && ($colspan === null))
				|| (($this->colspan !== null) && ($colspan !== null) && (intval($this->colspan) != intval($colspan)))
				)
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET ligne = '.intval($ligne).', colonne = '.intval($colonne);
					if ($texte !== null)
						$sql .= ', texte = \''.$this->getBase()->escapeString(substr($texte, 0, 255)).'\'';
					else
						$sql .= ', texte = NULL';
					if ($style !== null)
						$sql .= ', style = \''.$this->getBase()->escapeString(substr($style, 0, 20)).'\'';
					else
						$sql .= ', style = NULL';
					if ($rowspan !== null)
						$sql .= ', rowspan = '.intval($rowspan);
					else
						$sql .= ', rowspan = NULL';
					if ($colspan !== null)
						$sql .= ', colspan = '.intval($colspan);
					else
						$sql .= ', colspan = NULL';
					$sql .= ' WHERE id = '.intval($id).' AND id_tableau = '.intval($tableauId);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						$this->ligne = intval($ligne);
						$this->colonne = intval($colonne);
						if ($texte !== null)
							$this->texte = substr($texte, 0, 255);
						else
							$this->texte = null;
						if ($style !== null)
							$this->style = substr($style, 0, 20);
						else
							$this->style = null;
						if ($rowspan !== null)
							$this->rowspan = intval($rowspan);
						else
							$this->rowspan = null;
						if ($colspan !== null)
							$this->colspan = intval($colspan);
						else
							$this->colspan = null;
						$rslt = true;
					}
				}
				return $rslt;
			}
			
			public static function	GetListe($tableauId)
			{
				$class = 'TableauCellule';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class).' WHERE id_tableau = '.intval($tableauId).' ORDER BY ligne, colonne';
				$liste = $base->selectMany($sql, $class);
				$base->FermerBase();
				return $liste;
			}
			
			public static function	GetListeAssoc($tableauId)
			{
				$listeAssoc = null;
				$liste = TableauCellule::GetListe($tableauId);
				if (($liste !== null) && ($liste !== false))
				{
					$listeAssoc = array();
					foreach($liste as $cellule)
					{
						if (!isset($listeAssoc[$cellule->ligne]))
							$listeAssoc[$cellule->ligne] = array();
						$listeAssoc[$cellule->ligne][$cellule->colonne] = $cellule;
					}
				}
				return $listeAssoc;
			}
			
			public function	Delete($tableauId = null, $id = null, $ligne = null, $colonne = null, $autocommit = true)
			{
				if ($tableauId === null)
					$tableauId = $this->id_tableau;
				if ($tableauId === null)
					return false;
				$sql = 'DELETE FROM '.strtolower($this->m_class).' WHERE id_tableau = '.intval($tableauId);
				if ($id !== null)
					$sql .= ' AND id = '.intval($id);
				if ($ligne !== null)
					$sql .= ' AND ligne = '.intval($ligne);
				if ($colonne !== null)
					$sql .= ' AND colonne = '.intval($colonne);
				$rslt = $this->getBase()->deleteOne($sql, $autocommit);
				return $rslt;
			}
			
			public function Format($prefix = null)
			{
				$str = '<td';
				if ($this->rowspan !== null)
					$str .= ' rowspan="'.intval($this->rowspan).'"';
				if ($this->colspan !== null)
					$str .= ' colspan="'.intval($this->colspan).'"';
				if ($this->style !== null)
					$str .= ' class="'.htmlentities($this->style, ENT_COMPAT, 'UTF-8').'"';
				if (($this->texte !== null) || ($prefix !== null))
				{
					if ($prefix !== null)
					{
						$str .=
						'>
							<div class="commandes">
								<span class="icone up" title="Supprimer la ligne" onclick="return adminArticle_monterLigne(this.parentNode.parentNode);"></span>
								<span class="icone down" title="Supprimer la ligne" onclick="return adminArticle_descendreLigne(this.parentNode.parentNode);"></span>
								<span class="icone gauche" title="Supprimer la ligne" onclick="return adminArticle_reculerColonne(this.parentNode.parentNode);"></span>
								<span class="icone droite" title="Supprimer la ligne" onclick="return adminArticle_avancerColonne(this.parentNode.parentNode);"></span>
								<span class="icone deleteColonne" title="Supprimer la colonne" onclick="return adminArticle_supprimerColonne(this.parentNode.parentNode);"></span>
								<span class="icone deleteLigne" title="Supprimer la ligne" onclick="return adminArticle_supprimerLigne(this.parentNode.parentNode);"></span>
							</div>
							<input type="text" name="'.htmlentities($prefix, ENT_COMPAT, 'UTF-8').'_'.intval($this->ligne).'_'.intval($this->colonne).'" value="'.htmlentities($this->texte, ENT_COMPAT, 'UTF-8').'"/>
							</td>
						';
					}
					else
						$str .= '>'.htmlentities($this->texte, ENT_NOQUOTES, 'UTF-8').'</td>';
				}
				else
					$str .= '/>';
				return $str;
			}			
		}
	}
?>