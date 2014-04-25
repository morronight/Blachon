<?php
	$thisPageIsAdminPage = true;
	$isAdmin = false;
	if (isset($_SESSION['habilitation']) && (1 <= intval($_SESSION['habilitation'])))
	{
		$isAdmin = true;
		require_once 'include/camsii/Article.php';
		require_once 'include/camsii/Page.php';
		require_once 'include/camsii/Document.php';
		require_once 'include/camsii/Tableau.php';
		require_once 'include/camsii/TabXLSX.php';
		//require_once 'include/Configuration.php';

		$erreur = null;
		if (!isset($maxSize))
			$maxSize = intval(ini_get('upload_max_filesize')) * 1024 * 1024;
		$error_messages = array(
			UPLOAD_ERR_OK => 'Le téléchargement a réussi.',
			UPLOAD_ERR_INI_SIZE => 'Le fichier téléchargé excède la taille maximale autorisée ('.Formatage::GetReadeableFileSize(ini_get('upload_max_filesize')).').',
			UPLOAD_ERR_FORM_SIZE => 'Le fichier téléchargé excède la taille maximale autorisée pour ces fichiers ('.Formatage::GetReadeableFileSize($maxSize).').',
			UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement téléchargé.',
			UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été téléchargé.',
			UPLOAD_ERR_NO_TMP_DIR => 'Le dossier de stockage est introuvable.',
			UPLOAD_ERR_CANT_WRITE => 'Échec de l\'enregistrement du fichier.',
			UPLOAD_ERR_EXTENSION => 'L\'envoi de ce type de fichier n\'est pas autorisé.');

		$id = null;
		if (isset($_REQUEST['article']))
			$id = intval($_REQUEST['article']);
		$numparagraphe = null;
		if (isset($_REQUEST['paragraphe']))
			$numparagraphe = intval($_REQUEST['paragraphe']);
		$action = null;
		if (isset($_REQUEST['action']))
			$action = strtolower($_REQUEST['action']);
		if ($id !== null)
		{
			$article = new Article();
			$article = $article->Charge(intval($id));
		}
		else
			$article = null;

		$filepath = null;
		if (isset($_FILES['file']))
		{
			$file = $_FILES['file'];
			$filename = $file['name'];
			if (!preg_match('/\.(xlsx)$/i', $filename))
			{
				error_log('Envoi d\'un fichier de type non autorisé : '.$filename);
				$error = UPLOAD_ERR_EXTENSION;
			}
			else
			{
				$type = $file['type'];
				$path = $file['tmp_name'];
				$error = $file['error'];
				$taille = $file['size'];
				$hash = md5_file($path);
				$date = date('Y-m-d H:i:s', mktime());
			}
			if ($error != 0)
				$erreur = $error_messages[$error];
		}

		if (($action !== null) && ($article !== false))
		{
			switch($action)
			{
			case 'creer':
				$titre = null;
				if (isset($_REQUEST['titre']))
					$titre = Formatage::RemoveScript($_REQUEST['titre']);
				$resume = null;
				if (isset($_REQUEST['resume']))
					$resume = Formatage::RemoveScript($_REQUEST['resume']);
				$signature = null;
				if (isset($_REQUEST['signature']))
					$signature = Formatage::RemoveScript($_REQUEST['signature']);
				$categories = null;
				if (isset($_REQUEST['categories']))
					$categories = explode(',', $_REQUEST['categories']);
				$article = new Article();
				$article->commit();
				$nbParagraphes = 0;
				$charteId = null;
				if (isset($_REQUEST['charte']))
					$charteId = $_REQUEST['charte'];
				if ($article->Insert($titre, $resume, $signature, null, $charteId, false) !== null)
				{
					if ((isset($_REQUEST['image']) && (intval($_REQUEST['image']) > 0)) || (isset($_REQUEST['galerie']) && (intval($_REQUEST['galerie']) > 0)))
					{
						$image = null;
						if (isset($_REQUEST['image']) && (intval($_REQUEST['image']) > 0))
							$image = intval($_REQUEST['image']);
						$galerie = null;
						if (isset($_REQUEST['galerie']) && (intval($_REQUEST['galerie']) > 0))
							$galerie = intval($_REQUEST['galerie']);
						$legende = null;
						if (isset($_REQUEST['legende']))
							$legende = Formatage::RemoveScript($_REQUEST['legende']);
						$position = 0;
						if (isset($_REQUEST['position']))
							$position = intval($_REQUEST['position']);
						$largeur = null;
						if (isset($_REQUEST['largeur']) && ($_REQUEST['largeur'] != ''))
							$largeur = intval($_REQUEST['largeur']);
						$hauteur = null;
						if (isset($_REQUEST['hauteur']) && ($_REQUEST['hauteur'] != ''))
							$hauteur = intval($_REQUEST['hauteur']);
						if ($article->InsertIllustration($image, $galerie, $position, $legende, $largeur, $hauteur, false) === null)
							$erreur = 'Erreur lors du référencement de l\'illustration de l\'article.';
					}
					if (($erreur === null) && isset($_REQUEST['cellule_1_1']))
					{
						$tableau = new Tableau();
						if ($tableau->Insert($titre, $article->id, null, false) === null)
							$erreur = 'Erreur lors de la création du tableau de l\'article.';
						else
						{
							$nbLignes = 0;
							$texteCellule = null;
							while (($erreur === null) && (($texteCellule !== null) || ($nbLignes == 0)))
							{
								$nbLignes++;
								$nbColonnes = 0;
								while (($erreur === null) && (($texteCellule !== null) || ($nbColonnes == 0)))
								{
									$nbColonnes++;
									$texteCellule = null;
									if (isset($_REQUEST['cellule_'.$nbLignes.'_'.$nbColonnes]))
									{
										$texteCellule = $_REQUEST['cellule_'.$nbLignes.'_'.$nbColonnes];
										$cellule = new TableauCellule();
										if ($cellule->Insert($tableau->id, $nbLignes, $nbColonnes, $texteCellule, null, null, null, false) === null)
											$erreur = 'Erreur lors de la création de la cellule ('.$nbLignes.', '.$nbColonnes.') du tableau de l\'article.';
									}
								}
								if ($nbColonnes > 1)
									$texteCellule = '';
							}
						}
					}
					$texteParagraphe = null;
					while (($erreur === null) && (($texteParagraphe !== null) || ($nbParagraphes == 0)))
					{
						$titreParagraphe = null;
						if (isset($_REQUEST['paragraphe_titre_'.$nbParagraphes]))
							$titreParagraphe = Formatage::RemoveScript($_REQUEST['paragraphe_titre_'.$nbParagraphes]);
						$texteParagraphe = null;
						if (isset($_REQUEST['paragraphe_texte_'.$nbParagraphes]))
							$texteParagraphe = Formatage::RemoveScript($_REQUEST['paragraphe_texte_'.$nbParagraphes]);
						if ($texteParagraphe !== null)
						{
							$idParagraphe = $article->InsertParagraphe($titreParagraphe, $texteParagraphe, false);
							if ($idParagraphe === null)
								$erreur = 'Erreur lors de l\'enregistrement du paragraphe '.($nbParagraphes + 1).' de l\'article.';
							else
							{
								if ((isset($_REQUEST['paragraphe_image_'.$nbParagraphes]) && (intval($_REQUEST['paragraphe_image_'.$nbParagraphes]) > 0)) || (isset($_REQUEST['paragraphe_galerie_'.$nbParagraphes]) && (intval($_REQUEST['paragraphe_galerie_'.$nbParagraphes]) > 0)))
								{
									$imageParagraphe = null;
									if (isset($_REQUEST['paragraphe_image_'.$nbParagraphes]) && (intval($_REQUEST['paragraphe_image_'.$nbParagraphes]) > 0))
										$imageParagraphe = intval($_REQUEST['paragraphe_image_'.$nbParagraphes]);
									$galerieParagraphe = null;
									if (isset($_REQUEST['paragraphe_galerie_'.$nbParagraphes]) && (intval($_REQUEST['paragraphe_galerie_'.$nbParagraphes]) > 0))
										$galerieParagraphe = intval($_REQUEST['paragraphe_galerie_'.$nbParagraphes]);
									$legendeParagraphe = null;
									if (isset($_REQUEST['paragraphe_legende_'.$nbParagraphes]))
										$legendeParagraphe = Formatage::RemoveScript($_REQUEST['paragraphe_legende_'.$nbParagraphes]);
									$positionParagraphe = 0;
									if (isset($_REQUEST['paragraphe_position_'.$nbParagraphes]))
										$positionParagraphe = intval($_REQUEST['paragraphe_position_'.$nbParagraphes]);
									$largeurParagraphe = null;
									if (isset($_REQUEST['paragraphe_largeur_'.$nbParagraphes]) && ($_REQUEST['paragraphe_largeur_'.$nbParagraphes] != '') && (intval($_REQUEST['paragraphe_largeur_'.$nbParagraphes]) > 0))
										$largeurParagraphe = intval($_REQUEST['paragraphe_largeur_'.$nbParagraphes]);
									$hauteurParagraphe = null;
									if (isset($_REQUEST['paragraphe_hauteur_'.$nbParagraphes]) && ($_REQUEST['paragraphe_hauteur_'.$nbParagraphes] != '') && (intval($_REQUEST['paragraphe_hauteur_'.$nbParagraphes]) > 0))
										$hauteurParagraphe = intval($_REQUEST['paragraphe_hauteur_'.$nbParagraphes]);
									if ($article->InsertIllustrationParagraphe($idParagraphe, $imageParagraphe, $galerieParagraphe, $positionParagraphe, $legendeParagraphe, $largeurParagraphe, $hauteurParagraphe, false) === null)
										$erreur = 'Erreur lors du référencement de l\'illustration du paragraphe '.($nbParagraphes + 1).' de l\'article.';
								}
								if (($erreur === null) && isset($_REQUEST['paragraphe_cellule_'.$nbParagraphes.'_1_1']))
								{
									$tableau = new Tableau();
									if ($tableau->Insert($titre, $article->id, $idParagraphe, false) === null)
										$erreur = 'Erreur lors de la création du tableau du paragraphe '.($nbParagraphes + 1).' de l\'article.';
									else
									{
										$nbLignes = 0;
										$texteCellule = null;
										while (($erreur === null) && (($texteCellule !== null) || ($nbLignes == 0)))
										{
											$nbLignes++;
											$nbColonnes = 0;
											while (($erreur === null) && (($texteCellule !== null) || ($nbColonnes == 0)))
											{
												$nbColonnes++;
												$texteCellule = null;
												if (isset($_REQUEST['paragraphe_cellule_'.$nbParagraphes.'_'.$nbLignes.'_'.$nbColonnes]))
												{
													$texteCellule = $_REQUEST['paragraphe_cellule_'.$nbParagraphes.'_'.$nbLignes.'_'.$nbColonnes];
													$cellule = new TableauCellule();
													if ($cellule->Insert($tableau->id, $nbLignes, $nbColonnes, $texteCellule, null, null, null, false) === null)
														$erreur = 'Erreur lors de la création de la cellule ('.$nbLignes.', '.$nbColonnes.') du tableau du paragraphe '.($nbParagraphes + 1).' de l\'article.';
												}
											}
											if ($nbColonnes > 1)
												$texteCellule = '';
										}
									}
								}
							}
						}
						$nbParagraphes++;
					}
					if (($erreur === null) && ($categories !== null) && (count($categories) > 0))
					{
						foreach($categories as $categorieId)
						{
							if ($article->InsertCategorisation($categorieId, false) === null)
							{
								$erreur = 'Erreur lors de l\'enregistrement d\'une catégorie de l\'article.';
								break;
							}
						}
					}
				}
				else
				{
					if ((preg_match('/^Duplicate entry/', $article->error()) == 1) && (preg_match('/ for key 2$/', $article->error()) == 1))
						$erreur = 'Un autre article a déja ce titre !';
					else
						$erreur = 'Erreur lors de l\'enregistrement de l\'article.';
				}
				if ($erreur === null)
				{
					$article->commit();
					//$article->Publier();
					//$query_string = $article->GetLien();
					include 'template/contenu/adminArticleContenu.php';
					exit();
				}
				else
					$article->rollback();
				break;
			case 'modifier':
				$titre = null;
				if (isset($_REQUEST['titre']))
					$titre = Formatage::RemoveScript($_REQUEST['titre']);
				$resume = null;
				if (isset($_REQUEST['resume']))
					$resume = Formatage::RemoveScript($_REQUEST['resume']);
				$signature = null;
				if (isset($_REQUEST['signature']))
					$signature = Formatage::RemoveScript($_REQUEST['signature']);
				$categories = null;
				if (isset($_REQUEST['categories']))
					$categories = explode(',', $_REQUEST['categories']);
				$nbParagraphes = 0;
				$charteId = $article->id_charte;
				if (isset($_REQUEST['charte']))
					$charteId = $_REQUEST['charte'];
				$changement = $article->Update($titre, $resume, $signature, $charteId, false);
				if ($changement !== false)
				{
					$illustration = $article->GetIllustration();
					if ((isset($_REQUEST['image']) && (intval($_REQUEST['image']) > 0)) || (isset($_REQUEST['galerie']) && (intval($_REQUEST['galerie']) > 0)))
					{
						$image = null;
						if (isset($_REQUEST['image']) && (intval($_REQUEST['image']) > 0))
							$image = intval($_REQUEST['image']);
						$galerie = null;
						if (isset($_REQUEST['galerie']) && (intval($_REQUEST['galerie']) > 0))
							$galerie = intval($_REQUEST['galerie']);
						$legende = null;
						if (isset($_REQUEST['legende']))
							$legende = Formatage::RemoveScript($_REQUEST['legende']);
						$position = 0;
						if (isset($_REQUEST['position']))
							$position = intval($_REQUEST['position']);
						$largeur = null;
						if (isset($_REQUEST['largeur']) && ($_REQUEST['largeur'] != ''))
							$largeur = intval($_REQUEST['largeur']);
						$hauteur = null;
						if (isset($_REQUEST['hauteur']) && ($_REQUEST['hauteur'] != ''))
							$hauteur = intval($_REQUEST['hauteur']);
						if ($illustration === null)
						{
							if ($article->InsertIllustration($image, $galerie, $position, $legende, $largeur, $hauteur, false) === null)
								$erreur = 'Erreur lors du référencement de l\'illustration de l\'article.';
							$changement = true;
						}
						else
						{
							$res = $article->UpdateIllustration($image, $galerie, $position, $legende, $largeur, $hauteur, false);
							if ($res === true)
								$changement = true;
							if ($res === false)
								$erreur = 'Erreur lors de la modification du référencement de l\'illustration de l\'article.';
						}
					}
					else
					{
						$res = $article->DeleteIllustration(false);
						if ($res === true)
							$changement = true;
						if ($res === false)
							$erreur = 'Erreur lors du déréférencement de l\'illustration de l\'article.';
					}
					$tableau = null;
					$tableaux = Tableau::GetListe($article->id, false);
					if (($tableaux !== null) && ($tableaux !== false) && (count($tableaux) > 0))
						$tableau = $tableaux[0];
					if (($erreur === null) && isset($_REQUEST['cellule_1_1']))
					{
						if ($tableau === null)
						{
							$tableau = new Tableau();
							if ($tableau->Insert($titre, $article->id, null, false) === null)
								$erreur = 'Erreur lors de la création du tableau de l\'article.';
							$changement = true;
						}
						else
						{
							$res = $tableau->Update($titre, $article->id, null, false);
							if ($res === true)
								$changement = true;
							if ($res === false)
								$erreur = 'Erreur lors de la modification du tableau de l\'article.';
						}
						if ($erreur === null)
						{
							$cellules = TableauCellule::GetListeAssoc($tableau->id);
							$nbLignes = 0;
							$texteCellule = null;
							while (($erreur === null) && (($texteCellule !== null) || ($nbLignes == 0)))
							{
								$nbLignes++;
								$nbColonnes = 0;
								while (($erreur === null) && (($texteCellule !== null) || ($nbColonnes == 0)))
								{
									$nbColonnes++;
									$texteCellule = null;
									if (isset($_REQUEST['cellule_'.$nbLignes.'_'.$nbColonnes]))
									{
										$texteCellule = $_REQUEST['cellule_'.$nbLignes.'_'.$nbColonnes];
										if (isset($cellules[$nbLignes]) && isset($cellules[$nbLignes][$nbColonnes]))
										{
											$cellule = $cellules[$nbLignes][$nbColonnes];
											$res = $cellule->Update($nbLignes, $nbColonnes, $texteCellule, null, null, null, false);
											if ($res === true)
												$changement = true;
											if ($res === false)
												$erreur = 'Erreur lors de la modification de la cellule ('.$nbLignes.', '.$nbColonnes.') du tableau de l\'article.';
										}
										else
										{
											$cellule = new TableauCellule();
											if ($cellule->Insert($tableau->id, $nbLignes, $nbColonnes, $texteCellule, null, null, null, false) === null)
												$erreur = 'Erreur lors de la création de la cellule ('.$nbLignes.', '.$nbColonnes.') du tableau de l\'article.';
										}
									}
									else
									{
										if (isset($cellules[$nbLignes]) && isset($cellules[$nbLignes][$nbColonnes]))
										{
											$cellule = $cellules[$nbLignes][$nbColonnes];
											$texteCellule = '';
											$res = $cellule->Delete($tableau->id, $cellule->id, null, null, false);
											if ($res === true)
												$changement = true;
											if ($res === false)
												$erreur = 'Erreur lors de la suppression de la cellule ('.$nbLignes.', '.$nbColonnes.') du tableau de l\'article.';
										}
									}
								}
								if ($nbColonnes > 1)
									$texteCellule = '';
							}
						}
					}
					else
					{
						if (($erreur === null) && ($tableau !== null))
						{
							$res = $tableau->Delete(false);
							if ($res === true)
								$changement = true;
							if ($res === false)
								$erreur = 'Erreur lors de la suppression du tableau de l\'article.';
						}
					}
					$paragraphes = $article->GetParagraphes();
					$texteParagraphe = null;
					while (($erreur === null) && (($texteParagraphe !== null) || ($nbParagraphes == 0)))
					{
						$titreParagraphe = null;
						if (isset($_REQUEST['paragraphe_titre_'.$nbParagraphes]) && ($_REQUEST['paragraphe_titre_'.$nbParagraphes] != ''))
							$titreParagraphe = Formatage::RemoveScript($_REQUEST['paragraphe_titre_'.$nbParagraphes]);
						$texteParagraphe = null;
						if (isset($_REQUEST['paragraphe_texte_'.$nbParagraphes]) && ($_REQUEST['paragraphe_texte_'.$nbParagraphes] != ''))
							$texteParagraphe = Formatage::RemoveScript($_REQUEST['paragraphe_texte_'.$nbParagraphes]);
						if ($texteParagraphe !== null)
						{
							$idParagraphe = null;
							if (($paragraphes === null) || ($nbParagraphes >= count($paragraphes)))
							{
								$idParagraphe = $article->InsertParagraphe($titreParagraphe, $texteParagraphe, false);
								$changement = true;
							}
							else
							{
								$idParagraphe = $paragraphes[$nbParagraphes]->id;
								$res = $article->UpdateParagraphe($nbParagraphes, $titreParagraphe, $texteParagraphe, false);
								if ($res === true)
									$changement = true;
								if ($res === false)
									$idParagraphe = null;
							}
							if ($idParagraphe === null)
								$erreur = 'Erreur lors de l\'enregistrement du paragraphe '.($nbParagraphes + 1).' de l\'article.';
							else
							{
								if ((isset($_REQUEST['paragraphe_image_'.$nbParagraphes]) && (intval($_REQUEST['paragraphe_image_'.$nbParagraphes]) > 0)) || (isset($_REQUEST['paragraphe_galerie_'.$nbParagraphes]) && (intval($_REQUEST['paragraphe_galerie_'.$nbParagraphes]) > 0)))
								{
									$imageParagraphe = null;
									if (isset($_REQUEST['paragraphe_image_'.$nbParagraphes]) && (intval($_REQUEST['paragraphe_image_'.$nbParagraphes]) > 0))
										$imageParagraphe = intval($_REQUEST['paragraphe_image_'.$nbParagraphes]);
									$galerieParagraphe = null;
									if (isset($_REQUEST['paragraphe_galerie_'.$nbParagraphes]) && (intval($_REQUEST['paragraphe_galerie_'.$nbParagraphes]) > 0))
										$galerieParagraphe = intval($_REQUEST['paragraphe_galerie_'.$nbParagraphes]);
									$legendeParagraphe = null;
									if (isset($_REQUEST['paragraphe_legende_'.$nbParagraphes]))
										$legendeParagraphe = Formatage::RemoveScript($_REQUEST['paragraphe_legende_'.$nbParagraphes]);
									$positionParagraphe = 0;
									if (isset($_REQUEST['paragraphe_position_'.$nbParagraphes]))
										$positionParagraphe = intval($_REQUEST['paragraphe_position_'.$nbParagraphes]);
									$largeurParagraphe = null;
									if (isset($_REQUEST['paragraphe_largeur_'.$nbParagraphes]) && ($_REQUEST['paragraphe_largeur_'.$nbParagraphes] != ''))
										$largeurParagraphe = intval($_REQUEST['paragraphe_largeur_'.$nbParagraphes]);
									$hauteurParagraphe = null;
									if (isset($_REQUEST['paragraphe_hauteur_'.$nbParagraphes]) && ($_REQUEST['paragraphe_hauteur_'.$nbParagraphes] != ''))
										$hauteurParagraphe = intval($_REQUEST['paragraphe_hauteur_'.$nbParagraphes]);
									$paragraphe = $article->GetParagraphe($idParagraphe);
									if ($paragraphe !== null)
										$illustration = $paragraphe->GetIllustration();
									else
										$illustration = null;
									if ($illustration === null)
									{
										if ($article->InsertIllustrationParagraphe($idParagraphe, $imageParagraphe, $galerieParagraphe, $positionParagraphe, $legendeParagraphe, $largeurParagraphe, $hauteurParagraphe, false) === null)
											$erreur = 'Erreur lors du référencement de l\'illustration du paragraphe '.($nbParagraphes + 1).' de l\'article.';
										$changement = true;
									}
									else
									{
										$res = $article->UpdateIllustrationParagraphe($idParagraphe, $imageParagraphe, $galerieParagraphe, $positionParagraphe, $legendeParagraphe, $largeurParagraphe, $hauteurParagraphe, false);
										if ($res === true)
											$changement = true;
										if ($res === false)
											$erreur = 'Erreur lors de la modification du référencement de l\'illustration du paragraphe '.($nbParagraphes + 1).' de l\'article.';
									}
								}
								else
								{
									if ($article->DeleteIllustrationParagraphe($idParagraphe, false) === false)
										$erreur = 'Erreur lors du déréférencement de l\'illustration du paragraphe '.($nbParagraphes + 1).' de l\'article.';
									$changement = true;
								}
								$tableau = null;
								$tableaux = Tableau::GetListe($article->id, $idParagraphe);
								if (($tableaux !== null) && ($tableaux !== false) && (count($tableaux) > 0))
									$tableau = $tableaux[0];
								if (($erreur === null) && isset($_REQUEST['paragraphe_cellule_'.$nbParagraphes.'_1_1']))
								{
									if ($tableau === null)
									{
										$tableau = new Tableau();
										if ($tableau->Insert($titre, $article->id, $idParagraphe, false) === null)
											$erreur = 'Erreur lors de la création du tableau du paragraphe '.($nbParagraphes + 1).' de l\'article.';
										$changement = true;
									}
									else
									{
										$res = $tableau->Update($titre, $article->id, $idParagraphe, false);
										if ($res === true)
											$changement = true;
										if ($res === false)
											$erreur = 'Erreur lors de la modification du tableau du paragraphe '.($nbParagraphes + 1).' de l\'article.';
									}
									if ($erreur === null)
									{
										$cellules = TableauCellule::GetListeAssoc($tableau->id);
										$nbLignes = 0;
										$texteCellule = null;
										while (($erreur === null) && (($texteCellule !== null) || ($nbLignes == 0)))
										{
											$nbLignes++;
											$nbColonnes = 0;
											while (($erreur === null) && (($texteCellule !== null) || ($nbColonnes == 0)))
											{
												$nbColonnes++;
												$texteCellule = null;
												if (isset($_REQUEST['paragraphe_cellule_'.$nbParagraphes.'_'.$nbLignes.'_'.$nbColonnes]))
												{
													$texteCellule = $_REQUEST['paragraphe_cellule_'.$nbParagraphes.'_'.$nbLignes.'_'.$nbColonnes];
													if (isset($cellules[$nbLignes]) && isset($cellules[$nbLignes][$nbColonnes]))
													{
														$cellule = $cellules[$nbLignes][$nbColonnes];
														$res = $cellule->Update($nbLignes, $nbColonnes, $texteCellule, null, null, null, false);
														if ($res === true)
															$changement = true;
														if ($res === false)
															$erreur = 'Erreur lors de la modification de la cellule ('.$nbLignes.', '.$nbColonnes.') du tableau du paragraphe '.($nbParagraphes + 1).' de l\'article.';
													}
													else
													{
														$cellule = new TableauCellule();
														if ($cellule->Insert($tableau->id, $nbLignes, $nbColonnes, $texteCellule, null, null, null, false) === null)
															$erreur = 'Erreur lors de la création de la cellule ('.$nbLignes.', '.$nbColonnes.') du tableau du paragraphe '.($nbParagraphes + 1).' de l\'article.';
													}
												}
												else
												{
													if (isset($cellules[$nbLignes]) && isset($cellules[$nbLignes][$nbColonnes]))
													{
														$cellule = $cellules[$nbLignes][$nbColonnes];
														$texteCellule = '';
														$res = $cellule->Delete($tableau->id, $cellule->id, null, null, false);
														if ($res === true)
															$changement = true;
														if ($res === false)
															$erreur = 'Erreur lors de la suppression de la cellule ('.$nbLignes.', '.$nbColonnes.') du tableau du paragraphe '.($nbParagraphes + 1).' de l\'article.';
													}
												}
											}
											if ($nbColonnes > 1)
												$texteCellule = '';
										}
									}
								}
								else
								{
									if (($erreur === null) && ($tableau !== null))
									{
										$res = $tableau->Delete(false);
										if ($res === true)
											$changement = true;
										if ($res === false)
											$erreur = 'Erreur lors de la suppression du tableau du paragraphe '.($nbParagraphes + 1).' de l\'article.';
									}
								}
							}
						}
						else
						{
							if (($paragraphes === null) && ($nbParagraphes > 0))
								$changement = true;
							if (($paragraphes !== null) && ($nbParagraphes < count($paragraphes)))
							{
								for($p = (count($paragraphes) - 1); ($erreur === null) && ($p >= $nbParagraphes); $p--)
								{
									if ($article->DeleteParagraphe($p, false) === false)
										$erreur = 'Erreur lors de la suppression du paragraphe '.($p + 1).' de l\'article.';
								}
								$changement = true;
							}
						}
						$nbParagraphes++;
					}
					if (($erreur === null) && ($categories !== null))
					{
						$prevCategories = explode(',', $article->GetCategoriesIds());
						$news = array_diff($categories, $prevCategories);
						$olds = array_diff($prevCategories, $categories);
						if (count($olds) > 0)
						{
							$changement = true;
							foreach($olds as $categorieId)
							{
								if ($article->RemoveCategorisation($categorieId, false) === false)
								{
									$erreur = 'Erreur lors de la suppression d\'une catégorie de l\'article.';
									break;
								}
							}
						}
						if (count($news) > 0)
						{
							$changement = true;
							foreach($news as $categorieId)
							{
								if ($article->InsertCategorisation($categorieId, false) === null)
								{
									$erreur = 'Erreur lors de l\'ajout d\'une catégorie de l\'article.';
									break;
								}
							}
						}
					}
				}
				else
				{
					$erreur = 'Erreur lors de l\'enregistrement de l\'article.';
					$erreur .= PHP_EOL.$article->error();
				}
				if ($erreur === null)
				{
					if ($changement === true)
					{
						$erreur = 'Enregistrement réussi.';
						$article->commit();
						//$article->Publier();
					}
					else
						$erreur = 'Article non modifié, il n\'y a aucun changement.';
					include 'template/contenu/adminArticleContenu.php';
					exit();
				}
				else
					$article->rollback();
				break;
			case 'import':
				if (isset($filename))
				{
					if ($erreur === null)
					{
						$tableau = new Tableau();
						$article = new Article();
						if(($article->Insert(uniqid('temp_'), uniqid('temp_'), uniqid('temp_'), null, 1, false)) !== false)
						{
							$idparagraphe = null;
							if ($numparagraphe !== null)
							{
								$idparagraphe = $article->InsertParagraphe(uniqid('temp_'), uniqid('temp_'), false);
								if($idparagraphe === null)
								{
									$article->rollback;
									$erreur = 'Erreur lors de l\'importation du tableau.';
								}
							}
							if (($tableau !== null) && ($erreur === null))
							{
								if (($tableau->Import($filename,$path, $id, $idparagraphe, false)) === null)
									$erreur = 'Erreur lors de l\'importation du tableau de l\'article.';
								else
								{
									ob_start();
									require 'template/contenu/adminArticleContenu.php';
									ob_end_clean();
									if ($numparagraphe === null)
										afficheTableau($tableau);
									else
										afficheTableau($tableau, $numparagraphe);
									exit();
								}
								$article->rollback();
								break;
							}
						}
						else
						{
							$article->rollback();
							$erreur = 'Erreur lors de l\'importation du tableau.';
						}
					}
				}
				else
					$erreur = 'Erreur lors de l\'importation du tableau.';
				break;
			case 'selecttype':
				if ($article === null)
						$article = new Article();
					ob_start();
					require 'template/contenu/adminArticleLiens.php';
					ob_end_clean();
				$type = null;
				if (isset($_REQUEST['selectType']))
						$type = $_REQUEST['selectType'];
				$catVide = null;
				if (isset($_REQUEST['catvide']))
						$catVide = $_REQUEST['catvide'];
				if($type != null)
				{
					affCommandes();
					if($type == 'rien')
						affFctsLien("rien");
					if($type == 'document')
					{	
						affFctsLien("doc");
					}
					if($type == 'article')
					{
						affFctsLien("art");
					}
					if($type == 'page')
					{
						affFctsLien("page");
					}
					if($type == 'autre')
					{
						affFctsLien("autre");
					}
				}
				exit();
				break;
			case 'selectcategorie':
				if ($article === null)
					$article = new Article();
				ob_start();
				require 'template/contenu/adminArticleLiens.php';
				ob_end_clean();
				$id_categorie_a = null;
				if (isset($_REQUEST['selectCatIdA']))
					$id_categorie_a = intval($_REQUEST['selectCatIdA']);
				$id_categorie_d = null;
				if (isset($_REQUEST['selectCatIdD']))
					$id_categorie_d = intval($_REQUEST['selectCatIdD']);
				if($id_categorie_a != null)
					selectCategorieArt($id_categorie_a);
				if($id_categorie_d != null)
					selectCategorieDoc($id_categorie_d);
				exit();
				break;
			case 'selectionlien':
				if ($article === null)
					$article = new Article();
				ob_start();
				require 'template/contenu/adminArticleLiens.php';
				ob_end_clean();

				$selection = null;
				if (isset($_REQUEST['selection']))
					$selection = $_REQUEST['selection'];
				if (isset($selection))
				{
					if (preg_match('/^(http|https):\\/\\//', $selection))
					{
						$lien = preg_match('/([^"]+)(?:"([^"]+)")?/', $selection, $matches);
						$url = parse_url($selection);
						$urlLarnage = parse_url(Configuration::$Url);
						$preg = preg_match('"'.$urlLarnage['host'].'"',$matches[1]);
						if($preg!=false)
						{
							if(isset($url['path']))
							{
								$selected = $url['path'];
								$url_splitted = preg_split('[/]',$selected);
							}
						}
						if(isset($matches[2]))
							$texte = $matches[2];
						else
							$texte = null;
						if($preg != false && preg_match("/\\/documents\\//",$selection))
						{	
							if($texte === null)
								$nomDoc = $url_splitted[2];
							else
								$nomDoc = preg_replace('/"'.$texte.'"/',"",$url_splitted[2]);

							$document = Document::GetByPath($nomDoc);
							if(isset($document))
							{
								$catsId=$document->GetCategoriesIds();
								affFctsLien("doc",null,$texte,$catsId,$nomDoc);
							}
							else
								affFctsLien();
						}
						elseif($preg != false && preg_match("/\\/articles\\//",$selection))
						{
							if($texte == null)
								$nomArt = $url_splitted[2];
							else
								$nomArt = preg_replace('/"'.$texte.'"/',"",$url_splitted[2]);
							$articles = Article::GetListe();
							$theArticle = null;
							foreach($articles as $article)
							{
								if (strtolower($article->GetLien()) == strtolower($nomArt))
								{
									$theArticle = $article;
									break;
								}
							}
							if(isset($theArticle))
							{
								$catsId=$theArticle->GetCategoriesIds();
								affFctsLien("art",null,$texte,$catsId,$nomArt);
							}
							else
								affFctsLien();
						}
						elseif(($preg != false) && isset($url_splitted))
						{
							if($texte != null)
								$pageInterne = preg_replace('/"'.$texte.'"/',null,$url_splitted[1]);
							else
								$pageInterne = $url_splitted[1];
							affFctsLien("page",$pageInterne,$texte);
						}
						else
						{
							$pageExterne = $matches[1];
							affFctsLien("autre",$pageExterne,$texte);
						}
					}
					else
					{
						affFctsLien();
						echo 'La selection n\'est pas un lien valide';
					}
				}
				else
					affFctsLien();
				exit();
				break;
			case 'editer':
				if ($article->id === null)
					$erreur = 'Erreur lors du chargement de l\'article.';
				break;
			case 'publier':
				$publication = null;
				if (isset($_REQUEST['publication']))
					$publication = $_REQUEST['publication'];
				preg_replace('/T/', ' ', $publication);
				if (!$article->Publier($publication))
					$erreur = 'Erreur lors de la publication.';
				else
				{
					header('location: /administration/article?article='.$article->id);
					exit();
				}
				break;
			case 'depublier':
				if (!$article->Depublier())
					$erreur = 'Erreur lors de la dépublication.';
				else
				{
					header('location: /administration/article?article='.$article->id);
					exit();
				}
				break;
			case 'archiver':
				if (!$article->Archiver())
					$erreur = 'Erreur lors de l\'archivage.';
				else
				{
					header('location: /administration/article?article='.$article->id);
					exit();
				}
				break;
			case 'desarchiver':
				if (!$article->Desarchiver())
					$erreur = 'Erreur lors du désarchivage.';
				else
				{
					header('location: /administration/article?article='.$article->id);
					exit();
				}
				break;
			case 'searchimages':
				if ($article === null)
					$article = new Article();
				ob_start();
				require 'template/template_image.php';
				ob_end_clean();
				$categories = null;
				if (isset($_REQUEST['categories']))
					$categories = explode(',', $_REQUEST['categories']);
				afficheImagesAdminArticle(null, $categories);
				exit();
				break;
			case 'updatesearchimages':
				ob_start();
				require 'template/template_image.php';
				ob_end_clean();
				$archive = null;
				if (isset($_REQUEST['archives']))
					$archive = (intval($_REQUEST['archives']) > 0) ? true : false;
				$brouillon = null;
				if (isset($_REQUEST['brouillons']))
					$brouillon = (intval($_REQUEST['brouillons']) > 0) ? true : false;
				$categorieIds = null;
				if (isset($_REQUEST['categories']))
					$categorieIds = explode(',', $_REQUEST['categories']);
				$filtre = null;
				if (isset($_REQUEST['filtre']))
					$filtre = $_REQUEST['filtre'];
				afficheImagesAdminArticleSF(null, $categorieIds, $brouillon, $archive, $filtre);
				exit();
				break;
			case 'searchgaleries':
				if ($article === null)
					$article = new Article();
				ob_start();
				require 'template/template_galerie.php';
				ob_end_clean();
				$categories = null;
				if (isset($_REQUEST['categories']))
					$categories = explode(',', $_REQUEST['categories']);
				afficheGaleriesAdminArticle(null, $categories);
				exit();
				break;
			default:
				$erreur = 'Action non reconnue.';
				break;
			}
		}
		else
		{
			if ($article === false)
				$article = null;
		}
		$theArticle = $article;
	}
	header("HTTP/1.0 500");
	echo $erreur;
?>