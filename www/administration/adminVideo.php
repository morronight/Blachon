<?php
	$thisPageIsAdminPage = true;
	$isAdmin = false;
	if (isset($_SESSION['habilitation']) && (1 <= intval($_SESSION['habilitation'])))
	{
		$isAdmin = true;
		require_once 'include/camsii/Video.php';

		$error = null;
		$erreur = null;
		$changement = false;
		$id = null;
		if (isset($_REQUEST['video']))
			$id = intval($_REQUEST['video']);
		$action = null;
		if (isset($_REQUEST['action']))
			$action = strtolower($_REQUEST['action']);
		if ($id !== null)
			$video = Video::Get(intval($id));
		else
			$video = null;
		if (($action !== null) && ($video !== false))
		{
			switch($action)
			{
			case 'creer':
				$url = null;
				if (isset($_REQUEST['url']))
					$url = $_REQUEST['url'];
				$description = null;
				if (isset($_REQUEST['description']))
					$description = Formatage::RemoveScript($_REQUEST['description']);
				$categories = null;
				if (isset($_REQUEST['categories']))
					$categories = explode(',', $_REQUEST['categories']);
				$visible = 1;
				if (isset($_REQUEST['visible']))
					$visible = intval($_REQUEST['visible']);
				$video = new Video();
				if ($video->Insert($url, $description, $visible, false) === null)
				{
					$erreur = 'Erreur lors du référencement de la vidéo.';
					$error = -2;
				}
				if (($error == 0) && ($categories !== null) && (count($categories) > 0))
				{
					foreach($categories as $categorieId)
					{
						$cat = $video->InsertCategorisation($categorieId, false);
						if (($cat === null) || ($cat === false))
						{
							$error = -1;
							$erreur = 'Erreur lors de l\'enregistrement d\'une catégorie de la vidéo.';
						}
					}
				}
				if ($error == 0)
				{
					$video->Publier(null, null, false);
					$video->commit();
					$theVideo = $video;
					include 'template/contenu/adminVideoContenu.php';
					exit();
				}
				else
					$video->rollback();
				break;
			case 'supprimer':
				if ($video !== null)
				{
					$changement = $video->RemoveCategorisations(null, false);
					if ($changement !== false)
					{
						$changement = true;
						if ($video->Remove(null, false) === false)
							$erreur = 'Erreur lors de la suppression de la vidéo.';
					}
					else
						$erreur = 'Erreur lors de la suppression des catégories de la vidéo.';
					if ($erreur === null)
					{
						if ($changement === true)
						{
							$erreur = 'Suppression réussie.';
							$video->commit();
							$video = null;
						}
						else					
							$erreur = 'Vidéo non modifiée, il n\'y a aucun changement.';
						$theVideo = $video;
						include 'template/contenu/adminVideoContenu.php';
						exit();
					}
					else
						$video->rollback();
				}
				else
					$erreur = 'Vidéo non trouvée.';
				break;
			case 'recharger':
				$theVideo = $video;
				include 'template/contenu/adminVideoContenu.php';
				exit();
				break;
			case 'modifier':
				$url = null;
				if (isset($_REQUEST['url']))
					$url = $_REQUEST['url'];
				$description = null;
				if (isset($_REQUEST['description']))
					$description = Formatage::RemoveScript($_REQUEST['description']);
				$categories = null;
				if (isset($_REQUEST['categories']))
					$categories = explode(',', $_REQUEST['categories']);
				$visible = intval($video->visible);
				if (isset($_REQUEST['visible']))
					$visible = intval($_REQUEST['visible']);
				$changement = $video->Update($url, $description, $visible, false);
				if ($changement !== false)
					$changement = true;
				else
				{
					$error = -5;
					$erreur = 'Erreur lors du référencement de la nouvelle vidéo.';
				}
				if ($changement !== false)
				{
					if ($erreur === null)
					{
						if ($categories !== null)
						{
							$prevCategories = explode(',', $video->GetCategoriesIds());
							$olds = array_diff($prevCategories, $categories);
							$news = array_diff($categories, $prevCategories);
							if (count($olds) > 0)
							{
								$changement = true;
								foreach($olds as $categorieId)
								{
									if ($video->RemoveCategorisations($categorieId, false) === false)
									{
										$erreur = 'Erreur lors de la suppression d\'une catégorie de la vidéo.';
										break;
									}
								}
							}
							if (count($news) > 0)
							{
								$changement = true;
								foreach($news as $categorieId)
								{
									if ($video->InsertCategorisation($categorieId, false) === null)
									{
										$erreur = 'Erreur lors de l\'enregistrement d\'une catégorie de la vidéo.';
										break;
									}
								}
							}
						}
						else
						{
							if (count($video->GetCategoriesIds()) > 0)
							{
								$changement = true;
								if ($video->RemoveCategorisations(null, false) === false)
									$erreur = 'Erreur lors de la suppression des catégories de la vidéo.';
							}
						}
					}
				}
				else
					$erreur = 'Erreur lors de l\'enregistrement de la vidéo.';
				if ($erreur === null)
				{
					if ($changement === true)
					{
						$erreur = 'Enregistrement réussi.';
						$video->commit();
					}
					else
						$erreur = 'Vidéo non modifiée, il n\'y a aucun changement.';
					$theVideo = $video;
					include 'template/contenu/adminVideoContenu.php';
					exit();
				}
				else
				{
					$video->rollback();
					$theVideo = Video::Get($id);
				}
				break;
			case 'publier':
				if (!$video->Publier())
					$erreur = 'Erreur lors de la publication.';
				else
				{
					$theVideo = $video;
					include 'template/contenu/adminVideoContenu.php';
					exit();
				}
				break;
			case 'depublier':
				if (!$video->Depublier())
					$erreur = 'Erreur lors de la dépublication.';
				else
				{
					$theVideo = $video;
					include 'template/contenu/adminVideoContenu.php';
					exit();
				}
				break;
			case 'archiver':
				if (!$video->Archiver())
					$erreur = 'Erreur lors de l\'archivage.';
				else
				{
					$theVideo = $video;
					include 'template/contenu/adminVideoContenu.php';
					exit();
				}
				break;
			case 'desarchiver':
				if (!$video->Desarchiver())
					$erreur = 'Erreur lors du désarchivage.';
				else
				{
					$theVideo = $video;
					include 'template/contenu/adminVideoContenu.php';
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
			if ($video === false)
				$video = null;
		}
		$theVideo = $video;
	}
	header("HTTP/1.0 500");
	echo $erreur;
?>