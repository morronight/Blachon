<?php
	$thisPageIsAdminPage = true;
	$isAdmin = false;
	if (isset($_SESSION['habilitation']) && (1 <= intval($_SESSION['habilitation'])))
	{
		$isAdmin = true;
		require_once 'include/camsii/Document.php';

		//$maxSize = 512 * 1024;
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

		$error = null;
		$erreur = null;
		$changement = false;
		$id = null;
		if (isset($_REQUEST['document']))
			$id = intval($_REQUEST['document']);
		$action = null;
		if (isset($_REQUEST['action']))
			$action = strtolower($_REQUEST['action']);
		if ($id !== null)
			$document = Document::Get(intval($id));
		else
			$document = null;
		$filepath = null;
		if (isset($_FILES['Filedata']))
		{
			$file = $_FILES['Filedata'];
			$filename = $file['name'];
			if (/*!preg_match('/\.(zip)$/i', $filename) && */!preg_match('/\.(pdf)$/i', $filename))
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
			if ($error == 0)
			{
				$filepath = tempnam(Configuration::$Documents['location'], 'document_');
				if (is_file($filepath))
					unlink($filepath);
				if (preg_match('/\.(zip)$/i', $filename))
					$extpath = 'zip';
				if (preg_match('/\.(pdf)$/i', $filename))
					$extpath = 'pdf';
			}
		}
		if (($action !== null) && ($document !== false))
		{
			switch($action)
			{
			case 'creer':
				$nom = null;
				if (isset($_REQUEST['nom']))
					$nom = Formatage::RemoveScript($_REQUEST['nom']);
				$description = null;
				if (isset($_REQUEST['description']))
					$description = Formatage::RemoveScript($_REQUEST['description']);
				$categories = null;
				if (isset($_REQUEST['categories']))
					$categories = explode(',', $_REQUEST['categories']);
				$visible = 1;
				if (isset($_REQUEST['visible']))
					$visible = intval($_REQUEST['visible']);
				if ($filepath !== null)
				{
					if (move_uploaded_file($path, $filepath.'.'.$extpath) !== false)
					{
						$file = $filepath.'.'.$extpath;
						if (Document::GetByName($nom) !== false)
						{
							$erreur = 'Un document avec ce nom existe déjà.';
							$error = -3;
						}
						else
						{
							if ($extpath == 'zip')
							{
								// TODO
							}
							else
							{
								$document = new Document();
								if ($document->Insert($nom, basename($file), $taille, $description, $visible, false) === null)
								{
									$erreur = 'Erreur lors du référencement du document.';
									$error = -2;
								}
								if (($error == 0) && ($categories !== null) && (count($categories) > 0))
								{
									foreach($categories as $categorieId)
									{
										$cat = $document->InsertCategorisation($categorieId, false);
										if (($cat === null) || ($cat === false))
										{
											$error = -1;
											$erreur = 'Erreur lors de l\'enregistrement d\'une catégorie du document.';
										}
									}
								}
								if ($error == 0)
								{
									$document->Publier(null, null, false);
									$document->commit();
									$theDocument = $document;
									include 'template/contenu/adminDocumentContenu.php';
									exit();
								}
								else
									$document->rollback();
								$filepathreal = $filepath;
							}
							if (($error != 0) && is_file($file))
								unlink($file);
						}
					}
					else
						$erreur = 'Erreur lors de la récupération du document.';
				}
				break;
			case 'exists':
				$nom = null;
				if (isset($_REQUEST['nom']))
					$nom = Formatage::RemoveScript($_REQUEST['nom']);
				$document = Document::GetByName($nom);
				if ($document !== false)
					exit(1);
				exit(0);
				break;
			case 'supprimer':
				if ($document !== null)
				{
					$changement = $document->RemoveCategorisations(null, false);
					if ($changement !== false)
					{
						$changement = true;
						$filepath = $document->GetFilePath();
						if ($document->Remove(null, false) === false)
							$erreur = 'Erreur lors de la suppression du document.';
						else
						{
							if (unlink($filepath) === false)
								$erreur = 'Erreur lors de la suppression du fichier.';							
						}
					}
					else
						$erreur = 'Erreur lors de la suppression des catégories du document.';
					if ($erreur === null)
					{
						if ($changement === true)
						{
							$erreur = 'Suppression réussie.';
							$document->commit();
							$document = null;
						}
						else					
							$erreur = 'Document non modifié, il n\'y a aucun changement.';
						$theDocument = $document;
						include 'include/contenu/adminDocumentContenu.php';
						exit();
					}
					else
						$document->rollback();
				}
				else
					$erreur = 'Document non trouvé.';
				break;
			case 'recharger':
				$theDocument = $document;
				include 'include/contenu/adminDocumentContenu.php';
				exit();
				break;
			case 'modifier':
				$nom = $document->nom;
				if (isset($_REQUEST['nom']))
					$nom = Formatage::RemoveScript($_REQUEST['nom']);
				$description = null;
				if (isset($_REQUEST['description']))
					$description = Formatage::RemoveScript($_REQUEST['description']);
				$categories = null;
				if (isset($_REQUEST['categories']))
					$categories = explode(',', $_REQUEST['categories']);
				$visible = intval($document->visible);
				if (isset($_REQUEST['visible']))
					$visible = intval($_REQUEST['visible']);
				if ($filepath !== null)
				{
					if (move_uploaded_file($path, $filepath.'.'.$extpath) !== false)
					{
						$file = $filepath.'.'.$extpath;
						if ($extpath == 'zip')
						{
							// TODO
						}
						else
						{
							$filepathreal = Configuration::$Documents['location'].$document->path;
							$changement = $document->Update($nom, $taille, $description, $visible, false);
							if ($changement !== false)
							{
								if (!rename($file, $filepathreal))
								{
									$error = -4;
									$erreur = 'Erreur lors du remplacement du document.';
								}
								else
									$changement = true;
							}
							else
							{
								$error = -5;
								$erreur = 'Erreur lors du référencement du nouveau document.';
							}
						}						
						if (($error != 0) && is_file($file))
							unlink($file);
					}
					else
						$erreur = 'Erreur lors de la récupération du document.';
				}
				else			
					$changement = $document->Update($nom, null, $description, $visible, false);
				if ($changement !== false)
				{
					if ($erreur === null)
					{
						if ($categories !== null)
						{
							$prevCategories = explode(',', $document->GetCategoriesIds());
							$olds = array_diff($prevCategories, $categories);
							$news = array_diff($categories, $prevCategories);
							if (count($olds) > 0)
							{
								$changement = true;
								foreach($olds as $categorieId)
								{
									if ($document->RemoveCategorisations($categorieId, false) === false)
									{
										$erreur = 'Erreur lors de la suppression d\'une catégorie du document.';
										break;
									}
								}
							}
							if (count($news) > 0)
							{
								$changement = true;
								foreach($news as $categorieId)
								{
									if ($document->InsertCategorisation($categorieId, false) === null)
									{
										$erreur = 'Erreur lors de l\'enregistrement d\'une catégorie du document.';
										break;
									}
								}
							}
						}
						else
						{
							if (count($document->GetCategoriesIds()) > 0)
							{
								$changement = true;
								if ($document->RemoveCategorisations(null, false) === false)
									$erreur = 'Erreur lors de la suppression des catégories du document.';
							}
						}
					}
				}
				else
					$erreur = 'Erreur lors de l\'enregistrement du document.';
				if ($erreur === null)
				{
					if ($changement === true)
					{
						$erreur = 'Enregistrement réussi.';
						$document->commit();
					}
					else
						$erreur = 'Document non modifié, il n\'y a aucun changement.';
					$theDocument = $document;
					include 'template/contenu/adminDocumentContenu.php';
					exit();
				}
				else
				{
					$document->rollback();
					$theDocument = Document::Get($id);
					$filepathreal = Configuration::$Documents['location'].$document->path;
				}
				break;
			case 'publier':
				if (!$document->Publier())
					$erreur = 'Erreur lors de la publication.';
				else
				{
					$theDocument = $document;
					include 'template/contenu/adminDocumentContenu.php';
					exit();
				}
				break;
			case 'depublier':
				if (!$document->Depublier())
					$erreur = 'Erreur lors de la dépublication.';
				else
				{
					$theDocument = $document;
					include 'template/contenu/adminDocumentContenu.php';
					exit();
				}
				break;
			case 'archiver':
				if (!$document->Archiver())
					$erreur = 'Erreur lors de l\'archivage.';
				else
				{
					$theDocument = $document;
					include 'template/contenu/adminDocumentContenu.php';
					exit();
				}
				break;
			case 'desarchiver':
				if (!$document->Desarchiver())
					$erreur = 'Erreur lors du désarchivage.';
				else
				{
					$theDocument = $document;
					include 'template/contenu/adminDocumentContenu.php';
					exit();
				}
				break;
			default:
				$erreur = 'Action non reconnue.';
				break;
			}
		}
		else
		{
			if ($document === false)
				$document = null;
		}
		$theDocument = $document;
	}
	header("HTTP/1.0 500");
	echo $erreur;
?>