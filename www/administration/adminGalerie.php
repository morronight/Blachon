<?php
	$thisPageIsAdminPage = true;
	$isAdmin = false;
	if (isset($_SESSION['habilitation']) && (1 <= intval($_SESSION['habilitation'])))
	{
		$isAdmin = true;
		require_once 'include/camsii/Galerie.php';

		$error = null;
		$erreur = null;
		$changement = false;
		$id = null;
		if (isset($_REQUEST['galerie']))
			$id = intval($_REQUEST['galerie']);
		$action = null;
		if (isset($_REQUEST['action']))
			$action = strtolower($_REQUEST['action']);
		if ($id !== null)
			$galerie = Galerie::Get(intval($id));
		else
			$galerie = null;
		if (($action !== null) && ($galerie !== false))
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
				$images = null;
				if (isset($_REQUEST['images']))
					$images = explode(',', $_REQUEST['images']);
				$visible = 1;
				if (isset($_REQUEST['visible']))
					$visible = intval($_REQUEST['visible']);
				$galerie = new Galerie();
				if ($galerie->Insert($nom, $description, $visible, false) === null)
				{
					$erreur = 'Erreur lors du référencement de la galerie.';
					$error = -2;
				}
				if (($error == 0) && ($categories !== null) && (count($categories) > 0))
				{
					foreach($categories as $categorieId)
					{
						$cat = $galerie->InsertCategorisation($categorieId, false);
						if (($cat === null) || ($cat === false))
						{
							$error = -1;
							$erreur = 'Erreur lors de l\'enregistrement d\'une catégorie de la galerie.';
						}
					}
				}
				if (($error == 0) && ($images !== null) && (count($images) > 0))
				{
					foreach($images as $imageId)
					{
						$img = $galerie->InsertImage($imageId, false);
						if (($img === null) || ($img === false))
						{
							$error = -3;
							$erreur = 'Erreur lors de l\'enregistrement d\'une image/photo de la galerie.';
						}
					}
				}
				if ($error == 0)
				{
					$galerie->Publier(null, null, false);
					$galerie->commit();
					$theGalerie = $galerie;
					include 'template/contenu/adminGalerieContenu.php';
					exit();
				}
				else
					$galerie->rollback();
				break;
			case 'supprimer':
				if ($galerie !== null)
				{
					$changement = $galerie->RemoveCategorisations(null, false);
					if ($changement !== false)
					{
						$changement = $galerie->RemoveImages(null, false);
						if ($changement !== false)
						{
							$changement = true;
							if ($galerie->Remove(null, false) === false)
								$erreur = 'Erreur lors de la suppression de la galerie.';
						}
						else
							$erreur = 'Erreur lors de la suppression des images de la galerie.';
					}
					else
						$erreur = 'Erreur lors de la suppression des catégories de la galerie.';
					if ($erreur === null)
					{
						if ($changement === true)
						{
							$erreur = 'Suppression réussie.';
							$galerie->commit();
							$galerie = null;
						}
						else					
							$erreur = 'Galerie non modifiée, il n\'y a aucun changement.';
						$theGalerie = $galerie;
						include 'template/contenu/adminGalerieContenu.php';
						exit();
					}
					else
						$galerie->rollback();
				}
				else
					$erreur = 'Galerie non trouvée.';
				break;
			case 'recharger':
				$theGalerie = $galerie;
				include 'template/contenu/adminGalerieContenu.php';
				exit();
				break;
			case 'searchimages':
				ob_start();
				require 'template/template_galerie.php';
				ob_end_clean();
				$theGalerie = $galerie;
				$categories = null;
				if (isset($_REQUEST['categories']))
					$categories = explode(',', $_REQUEST['categories']);
				if ($galerie !== null)
				{
					$images = $galerie->GetImages();
					afficheImagesGalerie($images);
				}
				else
					afficheImagesGalerie(null, $categories);
				exit();
				break;
			case 'modifier':
				$nom = null;
				if (isset($_REQUEST['nom']))
					$nom = Formatage::RemoveScript($_REQUEST['nom']);
				$description = null;
				if (isset($_REQUEST['description']))
					$description = Formatage::RemoveScript($_REQUEST['description']);
				$categories = null;
				if (isset($_REQUEST['categories']))
					$categories = explode(',', $_REQUEST['categories']);
				$images = null;
				if (isset($_REQUEST['images']))
					$images = explode(',', $_REQUEST['images']);
				$visible = intval($galerie->visible);
				if (isset($_REQUEST['visible']))
					$visible = intval($_REQUEST['visible']);
				$changement = $galerie->Update($nom, $description, $visible, false);
				if ($changement !== false)
					$changement = true;
				else
				{
					$error = -5;
					$erreur = 'Erreur lors du référencement de la nouvelle galerie.';
				}
				if ($changement !== false)
				{
					if ($erreur === null)
					{
						if ($images !== null)
						{
							$prevImages = explode(',', $galerie->GetImagesIds());
							$olds = array_diff($prevImages, $images);
							$news = array_diff($images, $prevImages);
							if (count($olds) > 0)
							{
								$changement = true;
								foreach($olds as $imageId)
								{
									if ($galerie->RemoveImages($imageId, false) === false)
									{
										$erreur = 'Erreur lors de la suppression d\'une image de la galerie.';
										break;
									}
								}
							}
							if (count($news) > 0)
							{
								$changement = true;
								foreach($news as $imageId)
								{
									if ($galerie->InsertImage($imageId, false) === null)
									{
										$erreur = 'Erreur lors de l\'enregistrement d\'une image de la galerie.';
										break;
									}
								}
							}
						}
						else
						{
							if (count($galerie->GetImagesIds()) > 0)
							{
								$changement = true;
								if ($galerie->RemoveImages(null, false) === false)
									$erreur = 'Erreur lors de la suppression des images de la galerie.';
							}
						}
					}
					if ($erreur === null)
					{
						if ($categories !== null)
						{
							$prevCategories = explode(',', $galerie->GetCategoriesIds());
							$olds = array_diff($prevCategories, $categories);
							$news = array_diff($categories, $prevCategories);
							if (count($olds) > 0)
							{
								$changement = true;
								foreach($olds as $categorieId)
								{
									if ($galerie->RemoveCategorisations($categorieId, false) === false)
									{
										$erreur = 'Erreur lors de la suppression d\'une catégorie de la galerie.';
										break;
									}
								}
							}
							if (count($news) > 0)
							{
								$changement = true;
								foreach($news as $categorieId)
								{
									if ($galerie->InsertCategorisation($categorieId, false) === null)
									{
										$erreur = 'Erreur lors de l\'enregistrement d\'une catégorie de la galerie.';
										break;
									}
								}
							}
						}
						else
						{
							if (count($galerie->GetCategoriesIds()) > 0)
							{
								$changement = true;
								if ($galerie->RemoveCategorisations(null, false) === false)
									$erreur = 'Erreur lors de la suppression des catégories de la galerie.';
							}
						}
					}
				}
				else
					$erreur = 'Erreur lors de l\'enregistrement de la galerie.';
				if ($erreur === null)
				{
					if ($changement === true)
					{
						$erreur = 'Enregistrement réussi.';
						$galerie->commit();
					}
					else
						$erreur = 'Galerie non modifiée, il n\'y a aucun changement.';
					$theGalerie = $galerie;
					include 'template/contenu/adminGalerieContenu.php';
					exit();
				}
				else
				{
					$galerie->rollback();
					$theGalerie = Galerie::Get($id);
					$filepathreal = Configuration::$Galeries['location'].$galerie->path;
				}
				break;
			case 'publier':
				if (!$galerie->Publier())
					$erreur = 'Erreur lors de la publication.';
				else
				{
					$theGalerie = $galerie;
					include 'template/contenu/adminGalerieContenu.php';
					exit();
				}
				break;
			case 'depublier':
				if (!$galerie->Depublier())
					$erreur = 'Erreur lors de la dépublication.';
				else
				{
					$theGalerie = $galerie;
					include 'template/contenu/adminGalerieContenu.php';
					exit();
				}
				break;
			case 'archiver':
				if (!$galerie->Archiver())
					$erreur = 'Erreur lors de l\'archivage.';
				else
				{
					$theGalerie = $galerie;
					include 'template/contenu/adminGalerieContenu.php';
					exit();
				}
				break;
			case 'desarchiver':
				if (!$galerie->Desarchiver())
					$erreur = 'Erreur lors du désarchivage.';
				else
				{
					$theGalerie = $galerie;
					include 'template/contenu/adminGalerieContenu.php';
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
			if ($galerie === false)
				$galerie = null;
		}
		$theGalerie = $galerie;
	}
	header("HTTP/1.0 500");
	echo $erreur;
?>