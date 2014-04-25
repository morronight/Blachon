<!DOCTYPE html>
<html lang="fr">
	<?php include 'template/template_head.php'; ?>
		<body>
			<div id="wrapper">
				<?php
					if (isset($_SESSION['utilisateur']))
					{
						$action = null;
						if(isset($_REQUEST['action']))
							$action = $_REQUEST['action'];
						include("template/contenu/administration.php");
						/*switch($action)
						{
							case 'ajouterArticle':
							{
								include("template/contenu/adminArticle.php");
								exit();
								break;
							}
							case 'listeArticles':
								require("template/contenu/adminArticles.php");
							case 'ajouterDocument':
								include("template/contenu/adminDocument.php");
							case 'listeDocuments':
								include("template/contenu/adminDocuments.php");
							case 'ajouterImage':
								include("template/contenu/adminImage.php");
							case 'listeImages':
								include("template/contenu/adminImages.php");
							case 'ajouterGalerie':
								include("template/contenu/adminGalerie.php");
							case 'listeGaleries':
								include("template/contenu/adminGaleries.php");
							case 'ajouterUtilisateur':
								include("template/contenu/adminUtilisateur.php");
							case 'listeUtilisateurs':
								include("template/contenu/adminUtilisateurs.php");
							case 'ajouterVideo':
								include("template/contenu/adminVideo.php");
							case 'listeVideos':
								include("template/contenu/adminVideos.php");
							default:
								include("template/contenu/administration.php");
						}*/
					}
					else
					{
						include("template/contenu/identification.php");
					}	
				?>
			</div> <!-- wrapper -->
	</body>
</html>