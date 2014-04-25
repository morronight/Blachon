<?php
	if (!class_exists('Tableau'))
	{
		require_once 'Table.php';
		require_once 'TableauCellule.php';

		class	Tableau	extends Table
		{
			public function	__construct()
			{
				parent::__construct('id');
				$this->m_class = 'Tableau';
			}

			public function __destruct()
			{
				parent::__destruct();
			}
			
			public static function	GetListe($articleId = null, $paragrapheId = null)
			{
				$class = 'Tableau';
				$base = BD::OuvrirBase();
				$sql = 'SELECT * FROM '.strtolower($class);
				if ($articleId !== null)
				{
					$sql .= ' WHERE id_article = '.intval($articleId);
					if ($paragrapheId === false)
						$sql .= ' AND id_paragraphe IS NULL';
					else
					{
						if ($paragrapheId !== null)
							$sql .= ' AND id_paragraphe = '.intval($paragrapheId);
					}
				}
				$liste = $base->selectMany($sql, $class);
				if ($liste === false)
				{
					error_log('Erreur Sql : '.$sql);
					error_log($base->error());
				}
				$base->FermerBase();
				return $liste;
			}
			
			public function Insert($titre, $articleId = null, $paragrapheId = null, $autocommit = true)
			{
				if ($titre !== null)
					$this->titre = substr($titre, 0, 128);
				else
					$this->titre = null;
				if ($articleId !== null)
					$this->id_article = intval($articleId);
				else
					$this->id_article = null;
				if ($paragrapheId !== null)
					$this->id_paragraphe = intval($paragrapheId);
				else
					$this->id_paragraphe = null;
				$sql = 'INSERT INTO '.strtolower($this->m_class).' (titre, id_article, id_paragraphe) VALUES (';
				if ($this->titre !== null)
					$sql .= '\''.$this->getBase()->escapeString($this->titre).'\'';
				else
					$sql .= 'NULL';
				if ($articleId === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.intval($articleId);
				if ($paragrapheId === null)
					$sql .= ', NULL';
				else
					$sql .= ', '.intval($paragrapheId);
				$sql .= ')';
				$id = $this->getBase()->insertOne($sql, $autocommit);
				if ($id === false)
					$this->id = null;
				else
					$this->id = $id;
				return $this->id;
			}

			public function Import($titre, $file, $articleId = null, $paragrapheId = null, $autocommit = true)
			{
				$rslt = false;
				if(is_file($file)) //&& is_readable($file)
				{ 
					$xlsx = new SimpleXLSX($file);

						if($this->Insert($titre, $articleId, $paragrapheId,false) !== null)
						{
						
							/*if ($xlsx->success())
								print_r( $xlsx->rows() );
							else
								echo 'xlsx error: '.$xlsx->error();*/
						
							list($cols,) = $xlsx->dimension(1);
							$nbLignes = 0;
							$texteCellule = null;
							if ($xlsx->rows())
							{
								foreach( $xlsx->rows() as $k => $r) 
								{ //ligne
									$nbLignes++;
									$nbColonnes = 0;
									$texteCellule = null;
									for( $i = 0; $i < $cols; $i++) 
									{ //colonne
										$nbColonnes++;
										$texteCellule = $r[$i];
										$cellule = new TableauCellule();
										if (($rslt = $cellule->Insert($this->id, $nbLignes, $nbColonnes, $texteCellule, null, null, null, false)) !== null)
										{
											//	$erreur = 'Erreur lors de la création de la cellule ('.$nbLignes.', '.$nbColonnes.') du tableau de l\'article.';
											if ($autocommit)
												$cellule->commit();
											$rslt = true;
										}
										else
										{
											if ($autocommit)
												$cellule->rollback();
										}	
										if ($nbColonnes > 1)
											$texteCellule = '';
									}
								}
							}
							if ($rslt !== false)
							{
								if ($autocommit)
									$this->commit();
								$rslt = true;
							}
							else
							{
								if ($autocommit)
									$this->rollback();
							}
						}
						else return false;
					//}	
					
				}	
				return $rslt;
			
			}
			
			public function Update($titre, $articleId = null, $paragrapheId = null, $autocommit = true)
			{
				$id = $this->id;
				if ($id === null)
					return false;
				$rslt = null;
				if ((($this->id_article === null) && ($articleId !== null))
				|| (($this->id_article !== null) && ($articleId === null))
				|| (($this->id_article !== null) && ($articleId !== null) && (intval($this->id_article) != intval($articleId)))
				|| (($this->id_paragraphe === null) && ($paragrapheId !== null))
				|| (($this->id_paragraphe !== null) && ($paragrapheId === null))
				|| (($this->id_paragraphe !== null) && ($paragrapheId !== null) && (intval($this->id_paragraphe) != intval($paragrapheId)))
				|| (($this->titre === null) && ($titre !== null))
				|| (($this->titre !== null) && ($titre === null))
				|| (($this->titre !== null) && ($titre !== null) && ($this->titre != substr($titre, 0, 128)))
				)
				{
					$sql = 'UPDATE '.strtolower($this->m_class).' SET';
					if ($titre !== null)
						$sql .= ' titre = \''.$this->getBase()->escapeString(substr($titre, 0, 128)).'\'';
					else
						$sql .= ' titre = NULL';
					if ($articleId === null)
						$sql .= ', id_article = NULL';
					else
						$sql .= ', id_article = '.intval($articleId);
					if ($paragrapheId === null)
						$sql .= ', id_paragraphe = NULL';
					else
						$sql .= ', id_paragraphe = '.intval($paragrapheId);
					$sql .= ' WHERE id = '.intval($id);
					$rslt = $this->getBase()->update($sql, $autocommit);
					if ($rslt !== false)
					{
						if ($titre !== null)
							$this->titre = substr($titre, 0, 128);
						else
							$this->titre = null;
						if ($articleId !== null)
							$this->id_article = intval($articleId);
						else
							$this->id_article = null;
						if ($paragrapheId !== null)
							$this->id_paragraphe = intval($paragrapheId);
						else
							$this->id_paragraphe = null;
						$rslt = true;
					}
				}
				return $rslt;
			}
			
			public function	Delete($autocommit = true)
			{
				if ($this->id === null)
					return false;
				$cellule = new TableauCellule();
				$rslt = $cellule->Delete($this->id, null, null, null, false);
				if ($rslt !== false)
				{				
					$sql = 'DELETE FROM '.strtolower($this->m_class).' WHERE id = '.intval($this->id);
					$rslt = $this->getBase()->deleteOne($sql, $autocommit);
					if ($rslt !== false)
					{
						if ($autocommit)
							$this->commit();
						$rslt = true;
					}
					else
					{
						if ($autocommit)
							$this->rollback();
					}
				}
				else
				{
					if ($autocommit)
						$this->rollback();
				}
				return $rslt;
			}
			
			public static function	Get($id)
			{
				$tableau = new Tableau();
				return $tableau->Charge($id);
			}
		
			private function formatEntCol($prefix,$cell)
			{	
				$str = '<td';
				if ($cell->rowspan !== null)
					$str .= ' rowspan="'.intval($cell->rowspan).'"';
				if ($cell->colspan !== null)
					$str .= ' colspan="'.intval($cell->colspan).'"';
				if ($cell->style !== null)
					$str .= ' class="'.htmlentities($cell->style, ENT_COMPAT, 'UTF-8').'"';
				if (($cell->texte !== null) || ($prefix !== null))
				{
					if ($prefix !== null)
					{
						$str .=
						'>
				<input type="text" name="'.htmlentities($prefix, ENT_COMPAT, 'UTF-8').'_'.intval($cell->ligne).'_'.intval($cell->colonne).'" value="'.htmlentities($cell->texte, ENT_COMPAT, 'UTF-8').'"/>			
				</td>';
				
					}
					else
						$str .= '>'.htmlentities($cell->texte, ENT_NOQUOTES, 'UTF-8').'</td>';
				}
				else
					$str .= '/>';
				return $str;
			}

			private function formatEntLigne($prefix,$cell)
			{
				$str = '<td';
				if ($cell->rowspan !== null)
					$str .= ' rowspan="'.intval($cell->rowspan).'"';
				if ($cell->colspan !== null)
					$str .= ' colspan="'.intval($cell->colspan).'"';
				if ($cell->style !== null)
					$str .= ' class="'.htmlentities($cell->style, ENT_COMPAT, 'UTF-8').'"';
				if (($cell->texte !== null) || ($prefix !== null))
				{
					if ($prefix !== null)
					{
						$str .=
						'>
				
				<input type="text" name="'.htmlentities($prefix, ENT_COMPAT, 'UTF-8').'_'.intval($cell->ligne).'_'.intval($cell->colonne).'" value="'.htmlentities($cell->texte, ENT_COMPAT, 'UTF-8').'"/>
				</td>';
				
					}
					else
						$str .= '>'.htmlentities($cell->texte, ENT_NOQUOTES, 'UTF-8').'</td>';
				}
				else
					$str .= '/>';
				return $str;
			}

			private function pasFormatEnt($cellprefix,$cell)
			{
				$str = '<td';
				//onMouseOver="document.getElementById(\''.$cellprefix.'_entCols_'.$cell->colonne.'\').style.display=\'block\'" 
				//onmouseout="document.getElementById(\'entCols_'.$cell->colonne.'\').style.display=\'none\'"
				if ($cell->rowspan !== null)
					$str .= ' rowspan="'.intval($cell->rowspan).'"';
				if ($cell->colspan !== null)
					$str .= ' colspan="'.intval($cell->colspan).'"';
				if ($cell->style !== null)
					$str .= ' class="'.htmlentities($cell->style, ENT_COMPAT, 'UTF-8').'"';
				if (($cell->texte !== null) || ($cellprefix !== null))
				{
					if ($cellprefix !== null)
					{
						$str .=
						'>';
						$str .= '<input type="text" name="'.htmlentities($cellprefix, ENT_COMPAT, 'UTF-8').'_'.intval($cell->ligne).'_'.intval($cell->colonne).'" value="'.htmlentities($cell->texte, ENT_COMPAT, 'UTF-8').'"/>
				</td>';
				
					}
					else
						$str .= '>'.htmlentities($cell->texte, ENT_NOQUOTES, 'UTF-8').'</td>';
				}
				else
					$str .= '/>';
				return $str;
			}
			
			public function Format($nom = null, $style = null, $cellprefix = null)
			{
				$str = '<table';
				if ($nom !== null)
					$str .= ' id="'.htmlentities($nom, ENT_COMPAT, 'UTF-8').'"';
				if ($this->titre !== null)
					$str .= ' summary="'.htmlentities($this->titre, ENT_COMPAT, 'UTF-8').'"';
				if ($style !== null)
					$str .= ' class="'.htmlentities($style, ENT_COMPAT, 'UTF-8').'"';
				$str .= '>';
				$cellules = TableauCellule::GetListe($this->id);
				$ligne = null;
				$strLigne = '';
				$empty = true;
				$str .= '<tr><td id="enteteColonne"></td>'.PHP_EOL;
				foreach($cellules as $cell)
				{
					if($cell->ligne==1)
					{
						$test = '<div class="commandes" id="'.$cellprefix.'_'.'entCols_'.$cell->colonne.'">
					<input type="hidden" name="cellule_entete_'.$cell->ligne.'_'.$cell->colonne.'" class="idcolonne">
					<span class="icone gauche" title="Reculer la colonne" onclick="return adminArticle_reculerColonne(this.parentNode.parentNode);"></span>
					<span class="icone droite" title="Avancer la colonne" onclick="return adminArticle_avancerColonne(this.parentNode.parentNode);"></span>
					<span class="icone deleteColonne" title="Supprimer la colonne" onclick="return adminArticle_supprimerColonne(this.parentNode.parentNode,\''.$nom.'\');"></span>
					</div>';
						$str .= '<td class="colonne_'.$cell->colonne.'" id="enteteColonne">'.$test.'</td>';
					}
				}
				$str .= '</tr>';
				foreach($cellules as $cell)
				{	
					if (($ligne === null) || ($ligne != intval($cell->ligne)))
					{		
						
						if (($ligne !== null) && (($empty !== true) || ($cellprefix !== null)))
							$str .= '<tr>'.$strLigne.'</tr>'.PHP_EOL;
						$strLigne = '';
						$empty = true;
						$ligne = intval($cell->ligne);
					}
					
					if($cell->ligne==1 && $cell->colonne==1)
					{	
						$strLigne .= '<td class="enteteLigne"><div class="commandes">
				<input type="hidden" name="cellule_entete_'.$cell->ligne.'_'.$cell->colonne.'" class="idligne">
				<span class="icone up" title="Monter la ligne" onclick="return adminArticle_monterLigne(this.parentNode.parentNode);"></span>
				<span class="icone down" title="Descendre la ligne" onclick="return adminArticle_descendreLigne(this.parentNode.parentNode);"></span>
				<span class="icone deleteLigne" title="Supprimer la ligne" onclick="return adminArticle_supprimerLigne(this.parentNode.parentNode);"></span>
				</div></td>';
						$strLigne .= $this->formatEntLigne($cellprefix,$cell);

					}
					if($cell->ligne==1 && $cell->colonne!=1)
					{
						$strLigne .= $this->formatEntLigne($cellprefix,$cell);
					}
					if($cell->colonne==1 && $cell->ligne!=1)
					{		
						$strLigne .= '<td class="enteteLigne"><div class="commandes">
				<input type="hidden" name="cellule_entete_'.$cell->ligne.'_'.$cell->colonne.'" class="idligne">
				<span class="icone up" title="Monter la ligne" onclick="return adminArticle_monterLigne(this.parentNode.parentNode);"></span>
				<span class="icone down" title="Descendre la ligne" onclick="return adminArticle_descendreLigne(this.parentNode.parentNode);"></span>
				<span class="icone deleteLigne" title="Supprimer la ligne" onclick="return adminArticle_supprimerLigne(this.parentNode.parentNode);"></span>
				</div></td>';
						$strLigne .= $this->formatEntCol($cellprefix,$cell);	
					}
					if ($cell->ligne != 1 && $cell->colonne != 1)
						$strLigne .= $this->pasFormatEnt($cellprefix,$cell);
					if (($cell->texte !== null) && ($cell->texte != ''))
						$empty = false;
				
				}
				
				if (($ligne !== null) && (($empty !== true) || ($cellprefix !== null)))
					$str .= '<tr>'.$strLigne.'</tr>'.PHP_EOL;
				$str .= '</table>';
				return $str;
			}			
		}
	}
?>
