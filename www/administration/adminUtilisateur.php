<?php
	$thisPageIsAdminPage = true;
	$isAdmin = false;
	$erreur = null;
	if ((isset($_SESSION['habilitation']) && (1 <= intval($_SESSION['habilitation']))) || (isset($_REQUEST['action'])))
	{
		$isAdmin = true;
		require_once 'include/camsii/Utilisateur.php';

		$error = null;
		$changement = false;
		$id = null;
		if (isset($_REQUEST['utilisateur']))
			$id = intval($_REQUEST['utilisateur']);
		$action = null;
		if (isset($_REQUEST['action']))
			$action = strtolower($_REQUEST['action']);
		$methode = null;
		if (isset($_REQUEST['methode']))
			$methode = strtolower($_REQUEST['methode']);
		if ($id !== null)
			$utilisateur = Utilisateur::Get(intval($id));
		else
			$utilisateur = null;
		if (($action !== null) && ($utilisateur !== false))
		{
			switch($action)
			{
			case 'creer':
				$mail = null;
				if (isset($_REQUEST['mail']))
					$mail = $_REQUEST['mail'];
				$pseudo = null;
				if (isset($_REQUEST['pseudo']))
					$pseudo = $_REQUEST['pseudo'];
				$utilisateur = new Utilisateur();
				if ($utilisateur->Insert($mail, '', 1, $pseudo) === null)
				{
					$erreur = 'Erreur lors de la création de l\'utilisateur.';
					$error = 1;
				}
				if ($error == 0)
				{
					$utilisateur->DemandeMotDePasse();
					$theUtilisateur = $utilisateur;
					include 'template/contenu/adminUtilisateurContenu.php';
					exit();
				}
				break;
			case 'supprimer':
				if ($utilisateur !== null)
				{
					$changement = $utilisateur->Remove();
					if ($changement === false)
						$erreur = 'Erreur lors de la suppression de la utilisateur.';
					else
					{
						$erreur = 'Suppression réussie.';
						$utilisateur = null;
						exit();
					}
					$theUtilisateur = $utilisateur;
				}
				else
					$erreur = 'Utilisateur non trouvé.';
				break;
			case 'recharger':
				$theUtilisateur = $utilisateur;
				include 'template/contenu/adminUtilisateurContenu.php';
				exit();
				break;
			case 'modifier':
				$pseudo = null;
				if (isset($_REQUEST['pseudo']))
					$pseudo = $_REQUEST['pseudo'];
				$changement = $utilisateur->Update(intval($utilisateur->droits), $pseudo, false);
				if ($changement !== false)
					$changement = true;
				else
				{
					$error = 1;
					$erreur = 'Erreur lors de la modification de l\'utilisateur.';
				}
				if ($erreur === null)
				{
					if ($changement === true)
					{
						$erreur = 'Enregistrement réussi.';
						$utilisateur->commit();
					}
					else
						$erreur = 'Utilisateur non modifié, il n\'y a aucun changement.';
					$theUtilisateur = $utilisateur;
					include 'template/contenu/adminUtilisateurContenu.php';
					exit();
				}
				else
				{
					$utilisateur->rollback();
					$theUtilisateur = Utilisateur::Get($id);
				}
				break;
			case 'resetmotdepasse':
				if ($utilisateur->id !== null)
				{
					$rslt = $utilisateur->DemandeMotDePasse();
					if ($rslt !== true)
						$erreur = 'Erreur lors de la demande de réinitialisation du mot de passe.';
					else
					{
						echo 'Demande de mot de passe envoyée.';
						exit();
					}
				}
				else
					$erreur = 'Action non autorisée.';
				break;
			case 'changemotdepasse':
				$key = null;
				if (isset($_REQUEST['key']))
					$key = $_REQUEST['key'];
				$motdepasse = null;
				if (isset($_REQUEST['motdepasse']))
					$motdepasse = $_REQUEST['motdepasse'];
				$motdepasse2 = null;
				if (isset($_REQUEST['motdepasse2']))
					$motdepasse2 = $_REQUEST['motdepasse2'];
				$cipher = null;
				if (isset($_REQUEST['cipher']))
					$cipher = $_REQUEST['cipher'];
				if ($utilisateur->id !== null)
				{
					if (($cipher === null) && ($motdepasse !== null))
					{
						$cipher = hash('sha256', $utilisateur->mail.$motdepasse);
						if (strlen($motdepasse) < 5)
							$erreur = 'Le mot de passe doit comporter au moins 5 caractères.';
						if ($motdepasse != $motdepasse2)
							$erreur = 'Les 2 mots de passes doivent être identiques.';
					}
					if ($erreur === null)
					{
						$genkey = hash('sha256', strval($utilisateur->id).$utilisateur->mail.$utilisateur->motdepasse);
						if (($key == $genkey) && ($cipher !== null))
						{
							$rslt = $utilisateur->UpdateMotDePasse($utilisateur->mail, $cipher);
							if ($rslt !== true)
							{
								if ($rslt !== false)
								{
									$erreur = $rslt;
									error_log($rslt);
								}
								else
								{
									$erreur = 'Action non autorisée.';
									error_log('Erreur lors de la modification du mot de passe.');
								}
							}
						}
						else
						{
							$erreur = 'Action non autorisée.';
							error_log('Pas de cipher ou clés différentes.');
						}
					}
					else
						error_log($erreur);
				}
				else
				{
					$erreur = 'Action non autorisée.';
					error_log('Utilisateur non trouvé.');
				}
				if ($methode == 'ajax')
				{
					if ($erreur !== null)
						echo $erreur;
					exit();
				}
				if ($erreur !== null)
				{
					header('Location: /ChangeMotDePasse?id='.intval($id).'&key='.$key.'&erreur='.rawurlencode($erreur));
					echo $erreur;
				}
				else
					header('Location: /administration');
				exit();
				$utilisateur = null;
				break;
			default:
				$erreur = 'Action non reconnue.';
				break;
			}
		}
		else
		{
			if ($utilisateur === false)
				$utilisateur = null;
		}
		$theUtilisateur = $utilisateur;
	}
	header("HTTP/1.0 500");
	echo $erreur;
?>