	<head>
		<meta charset="UTF-8">
	<?php
		require_once 'include/camsii/Charte.php';
		if (isset($theCharte) && ($theCharte !== null))
			echo $theCharte->GetCss();
		if(!isset($_SESSION['utilisateur']) && isset($theCharte->id) && ($theCharte->id !== "3"))
		{
			if (!isset($titre) || ($titre === null))
				$titre = 'Site Cansii';
			else
				$titre .= ' - Cave Sébastien Blachon';
	?>
		<title><?php echo htmlentities($titre, ENT_COMPAT, 'UTF-8'); ?></title>
		<meta name="application-name" content="<?php echo htmlentities($titre, ENT_COMPAT, 'UTF-8'); ?>"/>
		<meta name="apple-mobile-web-app-capable" content="yes"/>
		<link rel="shortcut icon" type="image/ico" href="/favicon.ico" />
		<script type="text/javascript" src="/Scripts/accueil.js"></script>
		<script type="text/javascript" src="/Scripts/sha256.js"></script>
		<script type="text/javascript" src="/Scripts/identification.js"></script>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDVpfibafRmQB5l3bxV2xx8Q5Jc8A4mliQ&sensor=true"></script>
		<script type="text/javascript" src="/Scripts/googlemap.js"></script>
		<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
		<script type="text/javascript">
				var _gaq = _gaq || [];
				_gaq.push(["_setAccount", "UA-36895188-1"]);
				_gaq.push(["_setDomainName", "www.caveblachon.fr"]);
				_gaq.push(["_trackPageview"]);
				(function() {
					var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
					ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
				})();
		</script>
		<?php include('template/analytics.php'); ?>
<?php } 
	else
	{
		if (!isset($titre))
			$titre = 'Adminsitration du site de la Cave Blachon';
		echo '<title>', $titre, ' - Cave Sébastien Blachon</title>';
		if (!isset($theCharte))
		{
			$charte = new Charte();		
			$theCharte = $charte->Charge(2);
		}

		?>
		<script type="text/javascript" src="/Scripts/accueil.js"></script>
		<script type="text/javascript" src="/Scripts/adminArticle.js"></script>
		<script type="text/javascript" src="/Scripts/adminArticles.js"></script>
		<script type="text/javascript" src="/Scripts/adminBandeau.js"></script>
		<script type="text/javascript" src="/Scripts/adminDocument.js"></script>
		<script type="text/javascript" src="/Scripts/adminDocuments.js"></script>
		<script type="text/javascript" src="/Scripts/adminGalerie.js"></script>
		<script type="text/javascript" src="/Scripts/adminGaleries.js"></script>
		<script type="text/javascript" src="/Scripts/adminImage.js"></script>
		<script type="text/javascript" src="/Scripts/adminImages.js"></script>
		<script type="text/javascript" src="/Scripts/adminVideo.js"></script>
		<script type="text/javascript" src="/Scripts/adminVideos.js"></script>
		<script type="text/javascript" src="/Scripts/adminUtilisateur.js"></script>
		<script type="text/javascript" src="/Scripts/adminUtilisateurs.js"></script>
		<script type="text/javascript" src="/Scripts/adminCharte.js"></script>
		<script type="text/javascript" src="/Scripts/adminImages.js"></script>
		<script type="text/javascript" src="/Scripts/sha256.js"></script>
		<script type="text/javascript" src="/Scripts/html5slider.js"></script>
		<script type="text/javascript" src="/Scripts/identification.js"></script>
		<script type="text/javascript" src="/Scripts/googlemap.js"></script>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDVpfibafRmQB5l3bxV2xx8Q5Jc8A4mliQ&sensor=true"></script>
		<?php
			include 'template/analytics.php';
	}
	?>
	</head>