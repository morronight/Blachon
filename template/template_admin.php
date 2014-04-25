<!DOCTYPE html>
<html lang="fr">
	<?php include 'template/template_head.php'; ?>
		<body>
			<div id="wrapper">
				<?php
					if ($_SESSION['utilisateur'])
					{
						include("template/contenu/administration.php");
					}
					else
					{
						include("template/contenu/identification.php");
					}	
				?>
			</div> <!-- wrapper -->
	</body>
</html>