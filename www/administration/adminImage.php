<?php
	$thisPageIsAdminPage = true;
	$isAdmin = false;
	if (isset($_SESSION['habilitation']) && (1 <= intval($_SESSION['habilitation'])))
	{
		$isAdmin = true;
		require_once 'include/camsii/Image.php';

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
		if (isset($_REQUEST['image']))
			$id = intval($_REQUEST['image']);
		$action = null;
		if (isset($_REQUEST['action']))
			$action = strtolower($_REQUEST['action']);
		if ($id !== null)
			$image = Image::Get(intval($id));
		else
			$image = null;
		$filepath = null;
		if (isset($_FILES['Filedata0']))
		{
			$erreurs = '';
			$errors = array();
			$filenames = array();
			$types = array();
			$paths = array();
			$filepaths = array();
			$extpaths = array();
			$countFileErr = 0;		
			for ($i = 0; isset($_FILES['Filedata'.$i]); $i++)
			{
				$file = $_FILES['Filedata'.$i];
				$filenames[$i] = $file['name'];
				$filepaths[$i] = null;
				if (!preg_match('/\.(png|gif|jpe?g)$/i', $filenames[$i]))
				{
					error_log('Envoi d\'un fichier de type non autorisé : '.$filenames[$i]);
					$errors[$i] = UPLOAD_ERR_EXTENSION;
				}
				else
				{
					$types[$i] = $file['type'];
					$paths[$i] = $file['tmp_name'];
					$errors[$i] = $file['error'];
				}
				if ($errors[$i] != 0)
				{
					$countFileErr++;
					$erreurs .= 'Fichier '.($i + 1).' : '.$error_messages[$errors[$i]].PHP_EOL;
					$erreur = $error_messages[$errors[$i]];
				}
				if ($errors[$i] == 0)
				{
					$filepaths[$i] = tempnam(Configuration::$Images['location'], 'image_');
					if (is_file($filepaths[$i]))
						unlink($filepaths[$i]);
					if (preg_match('/\.(png|gif|jpe?g)$/i', $filenames[$i], $regs))
						$extpaths[$i] = $regs[1];
					$filename = $filenames[$i];
					$type = $types[$i];
					$path = $paths[$i];
					$filepath = $filepaths[$i];
					$extpath = $extpaths[$i];
				}
			}
			if ($countFileErr < count($filenames))
			{
				$error = 0;
				$erreur = null;
			}
			else
				$erreur = $erreurs;
			$file = null;
		}
		if (($action !== null) && ($image !== false))
		{
			switch($action)
			{
			case 'creer':
				$legende = null;
				if (isset($_REQUEST['legende']))
					$legende = Formatage::RemoveScript($_REQUEST['legende']);
				$categories = null;
				if (isset($_REQUEST['categories']))
					$categories = explode(',', $_REQUEST['categories']);
				$visible = 1;
				if (isset($_REQUEST['visible']))
					$visible = intval($_REQUEST['visible']);
				if (isset($filepaths) && (count($filepaths) > 0))
				{
					$done = 0;
					for ($i = 0; $i < count($filepaths); $i++)
					{
						if ($filepaths[$i] !== null)
						{
							if (move_uploaded_file($paths[$i], $filepaths[$i].'.'.$extpaths[$i]) !== false)
							{
								$file = $filepaths[$i].'.'.$extpaths[$i];
								$image = new Image();
								if ($image->Insert(basename($file), $legende, $visible, false) === null)
								{
									$erreurs .= 'Fichier '.($i + 1).' : Erreur lors du référencement de l\'image/photo.'.PHP_EOL;
									$errors[$i] = -2;
								}
								if (($error == 0) && ($categories !== null) && (count($categories) > 0))
								{
									foreach($categories as $categorieId)
									{
										$cat = $image->InsertCategorisation($categorieId, false);
										if (($cat === null) || ($cat === false))
										{
											$errors[$i] = -1;
											$erreurs .= 'Fichier '.($i + 1).' : Erreur lors de l\'enregistrement d\'une catégorie de l\'image/photo.'.PHP_EOL;
										}
									}
								}
								if ($error == 0)
								{
									$image->Publier(null, null, false);
									$image->commit();
									$theImage = $image;
									$done++;
								}
								else
								{
									$image->rollback();
									if (($error != 0) && is_file($file))
										unlink($file);
								}
							}
							else
								$erreurs .= 'Fichier '.($i + 1).' : Erreur lors de la récupération de l\'image/photo.'.PHP_EOL;
						}
					}
					$message = $done.' fichiers sur '.count($filepaths).' enregistrés correctement.'.PHP_EOL.$erreurs;
					if ($done > 0)
					{
						include 'template/contenu/adminImageContenu.php';
						exit();
					}
				}
				break;
			case 'supprimer':
				if ($image !== null)
				{
					$changement = $image->RemoveCategorisations(null, false);
					if ($changement !== false)
					{
						$changement = true;
						$filepath = $image->GetFilePath();
						if ($image->Remove(null, false) === false)
							$erreur = 'Erreur lors de la suppression de l\'image/photo.';
						else
						{
							if (unlink($filepath) === false)
								$erreur = 'Erreur lors de la suppression du fichier.';							
						}
					}
					else
						$erreur = 'Erreur lors de la suppression des catégories de l\'image/photo.';
					if ($erreur === null)
					{
						if ($changement === true)
						{
							$erreur = 'Suppression réussie.';
							$image->commit();
							$image = null;
						}
						else					
							$erreur = 'Image non modifiée, il n\'y a aucun changement.';
						$theImage = $image;
						include 'template/contenu/adminImageContenu.php';
						exit();
					}
					else
						$image->rollback();
				}
				else
					$erreur = 'Image non trouvée.';
				break;
			case 'recharger':
				$theImage = $image;
				include 'template/contenu/adminImageContenu.php';
				exit();
				break;
			case 'modifier':
				$legende = null;
				if (isset($_REQUEST['legende']))
					$legende = Formatage::RemoveScript($_REQUEST['legende']);
				$categories = null;
				if (isset($_REQUEST['categories']))
					$categories = explode(',', $_REQUEST['categories']);
				$visible = intval($image->visible);
				if (isset($_REQUEST['visible']))
					$visible = intval($_REQUEST['visible']);
				if ($filepath !== null)
				{
					if (move_uploaded_file($path, $filepath.'.'.$extpath) !== false)
					{
						$file = $filepath.'.'.$extpath;
						$filepathreal = Configuration::$Images['location'].$image->path;
						$changement = $image->Update($legende, $visible, false);
						if ($changement !== false)
						{
							if (!rename($file, $filepathreal))
							{
								$error = -4;
								$erreur = 'Erreur lors du remplacement de l\'image/photo.';
							}
							else
								$changement = true;
						}
						else
						{
							$error = -5;
							$erreur = 'Erreur lors du référencement de la nouvelle image/photo.';
						}
						if (($error != 0) && is_file($file))
							unlink($file);
					}
					else
						$erreur = 'Erreur lors de la récupération de l\'image/photo.';
				}
				else			
					$changement = $image->Update($legende, $visible, false);
				if ($changement !== false)
				{
					if ($erreur === null)
					{
						if ($categories !== null)
						{
							$prevCategories = explode(',', $image->GetCategoriesIds());
							$olds = array_diff($prevCategories, $categories);
							$news = array_diff($categories, $prevCategories);
							if (count($olds) > 0)
							{
								$changement = true;
								foreach($olds as $categorieId)
								{
									if ($image->RemoveCategorisations($categorieId, false) === false)
									{
										$erreur = 'Erreur lors de la suppression d\'une catégorie de l\'image/photo.';
										break;
									}
								}
							}
							if (count($news) > 0)
							{
								$changement = true;
								foreach($news as $categorieId)
								{
									if ($image->InsertCategorisation($categorieId, false) === null)
									{
										$erreur = 'Erreur lors de l\'enregistrement d\'une catégorie de l\'image/photo.';
										break;
									}
								}
							}
						}
						else
						{
							if (count($image->GetCategoriesIds()) > 0)
							{
								$changement = true;
								if ($image->RemoveCategorisations(null, false) === false)
									$erreur = 'Erreur lors de la suppression des catégories de l\'image/photo.';
							}
						}
					}
				}
				else
					$erreur = 'Erreur lors de l\'enregistrement de l\'image/photo.';
				if ($erreur === null)
				{
					if ($changement === true)
					{
						$erreur = 'Enregistrement réussi.';
						$image->commit();
					}
					else
						$erreur = 'Image non modifiée, il n\'y a aucun changement.';
					$theImage = $image;
					include 'template/contenu/adminImageContenu.php';
					exit();
				}
				else
				{
					$image->rollback();
					$theImage = Image::Get($id);
					$filepathreal = Configuration::$Images['location'].$image->path;
				}
				break;
			case 'publier':
				if (!$image->Publier())
					$erreur = 'Erreur lors de la publication.';
				else
				{
					$theImage = $image;
					include 'template/contenu/adminImageContenu.php';
					exit();
				}
				break;
			case 'depublier':
				if (!$image->Depublier())
					$erreur = 'Erreur lors de la dépublication.';
				else
				{
					$theImage = $image;
					include 'template/contenu/adminImageContenu.php';
					exit();
				}
				break;
			case 'archiver':
				if (!$image->Archiver())
					$erreur = 'Erreur lors de l\'archivage.';
				else
				{
					$theImage = $image;
					include 'template/contenu/adminImageContenu.php';
					exit();
				}
				break;
			case 'desarchiver':
				if (!$image->Desarchiver())
					$erreur = 'Erreur lors du désarchivage.';
				else
				{
					$theImage = $image;
					include 'template/contenu/adminImageContenu.php';
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
			if ($image === false)
				$image = null;
		}
		$theImage = $image;
	}
	header("HTTP/1.0 500");
	echo $erreur;
?>