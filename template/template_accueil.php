<?php
	require_once 'include/Configuration.php';
	ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
	<?php include 'template/template_head.php'; ?>
	<body onload="clients_initPageBlanche()">
	<?php
		switch($mode)
		{
			case 'page':
				echo '<div id="centre">';
				if (isset($fichier))
					include 'template/contenu/'.$fichier.'.php';
				echo '</div>';
				//if($fichier=='accueil')

				break;
			case 'article':
				echo '<div id="centre">';
				echo '<article id="contenu" class="article">';
				if ($article !== null)
				{
					require_once 'template/template_general.php';
					echo afficheArticle($article);
				}
				echo '</article>';
				echo '</div>';
				break;
		}
		if (isset($theCharte) && ($theCharte->id != 3))
		{
			include 'template/contenu/menuLateral.php';
		}
		echo '<header id="header">';
		if (!isset($theCharte) || ($theCharte === null))
		{
			$theCharte = Charte::Get(1);
			if ($theCharte === false)
				$theCharte = null;
		}
		echo '<div id="bandeauHaut"><div id="nuageMot"><span id="mot1" class="arrierePlan">St Joseph rouge</span><span id="mot2" class="secondPlan">St Joseph blanc</span><span id="mot3" class="premierPlan">Vin de France</span><span id="mot4" class="secondPlan">Rosé blanc rouge</span><span id="mot5">Cave Sébastien Blachon</span><span id="mot6" class="secondPlan">Septentrionale</span><span id="mot7" class="arrierePlan">Tradition</span><span id="mot8" class="premierPlan">Agriculture durable</span><span id="mot9" class="secondPlan">St jean de Muzols</span><span id="mot10" class="arrierePlan">Ardèche</span><span id="mot11" class="premierPlan">Terroir</span><span id="mot12" class="secondPlan">Vin</span><span id="mot13" class="premierPlan">Syrah Marsanne Roussane</span></div></div>';
		echo '<a href="/" style="position: absolute;left: 0;"><img id="logo" src="'.Configuration::$Static['url'].'/Images/caveblachon_286_210.png" alt="" width="350" height="250" style="box-shadow:0px 0px 5px #A12C2E;"/></a>';
		echo '</header>';
		if($theCharte->id !== "3")
			echo '<div id="pageBlanche" ></div>';
		
		echo '<footer>';
		echo '<section id="ouverture">Caveau ouvert sur rendez vous toute l\'année';
		?>
		<div id="fb-root"></div>
		<script>
		
		(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		<div class="boutonssociaux">
			<ul id="like" >
			<li class="google_plus_un" >
			<g:plusone annotation="none" size="medium"></g:plusone>
			</li>
			<li class="twitter">
			<a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-lang="fr">Tweeter</a>
			</li>
			<li class="facebook">
			<div class="fb-like" data-send="false" data-layout="button_count" data-width="500" data-show-faces="true" style="overflow:hidden"></div>
			</li>
		</div>
		<?php
		echo '</section>';
		echo '<section id="adresse">';
		echo '<section itemprop="address" itemscope="" itemtype="http://data-vocabulary.org/Address"><span itemprop="street-address">16 chemin de margiriat</span><br><span itemprop="postal-code">07300</span> <span itemprop="locality">Saint Jean de Muzols</span></section>';
		echo '<section>Téléphone <span itemprop="tel">+33 6 51 30 63 18</span><br><a href="'.Configuration::$Static['url'].'/Contact">Nous contacter</a></section>';
		echo '<section><a itemprop="url" href="/">Cave Sebastien Blachon</a> © 2013 <br><a target="_blank" href="http://www.cansii.com" title="Réalisation de logiciels et sites Internet">Réalisation Cansii</a> - v '.Configuration::$Version.'</section>';
		echo '</section>';
		echo '</footer>';
	?>
	</body>	
</html>

<?php
if (!isset($_SERVER['HTTP_USER_AGENT']) || (preg_match('/Chrome|Safari|Opera/', $_SERVER['HTTP_USER_AGENT']) > 0))
ob_end_flush();
else
{
$page = ob_get_contents();
ob_end_clean();
if (preg_match('/MSIE 8/', $_SERVER['HTTP_USER_AGENT']) > 0)
{
	$page = preg_replace
	(
		array('/<article(?:\s+class="([A-Za-z0-9_ ]+)")?/', '/<\/article>/', '/<section(?:\s+([A-Za-z0-9]+="[A-Za-z0-9_ ]*"\s+)*class="([A-Za-z0-9_ ]+)")?/', '/<\/section>/', '/<header(?:\s+class="([A-Za-z0-9_ ]+)")?/', '/<\/header>/', '/<footer(?:\s+class="([A-Za-z0-9_ ]+)")?/', '/<\/footer>/', '/<nav(?:\s+class="([A-Za-z0-9_ ]+)")?/', '/<\/nav>/', '/<!DOCTYPE html>/', '/<html lang="fr">/'),
		array('<div class="html5article $1"', '</div>', '<div $1 class="html5section $2"', '</div>', '<div class="html5header $1"', '</div>', '<div class="html5footer $1"', '</div>', '<div class="html5nav $1"', '</div>', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">', '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">'), 
		$page
	);
}
if (preg_match('/Firefox\/3|MSIE 6|MSIE 7/', $_SERVER['HTTP_USER_AGENT']) > 0)
{
	$page = preg_replace
	(
		array('/<article(?:\s+class="([A-Za-z0-9_ ]+)")?/', '/<\/article>/', '/<section(?:\s+([A-Za-z0-9]+="[A-Za-z0-9_ ]*"\s+)*class="([A-Za-z0-9_ ]+)")?/', '/<\/section>/', '/<header(?:\s+class="([A-Za-z0-9_ ]+)")?/', '/<\/header>/', '/<footer(?:\s+class="([A-Za-z0-9_ ]+)")?/', '/<\/footer>/', '/<nav(?:\s+class="([A-Za-z0-9_ ]+)")?/', '/<\/nav>/', '/<!DOCTYPE html>/', '/<html lang="fr">/'),
		array('<span class="html5article $1"', '</span>', '<span $1 class="html5section $2"', '</span>', '<span class="html5header $1"', '</span>', '<span class="html5footer $1"', '</span>', '<span class="html5nav $1"', '</span>', '', '<html>'), 
		$page
	);
}
if (preg_match('/MSIE 6|MSIE 7|MSIE 8|MSIE 9/', $_SERVER['HTTP_USER_AGENT']) > 0)
{
	$page = preg_replace
	(
		'/<([a-zA-Z0-9]+)\s+id="flashInfo">(.*)<\/\1>/',
		'<marquee id="flashInfo">$2</marquee>',
		$page
	);
}
echo $page;
}

?>