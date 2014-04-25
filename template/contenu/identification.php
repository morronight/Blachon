<link href="/Css/Administration.css" rel="stylesheet" type="text/css">
<article id="pageIdentification">
	<h1>Identification</h2>
	<section>Se connecter</section>
	<br>
		<section class="message"></section>
		<?php
			if (!isset($_SESSION['key']))
				$_SESSION['key'] = md5(rand());
			if (isset($erreur) && ($erreur !== null))
				echo htmlentities($erreur, ENT_COMPAT, 'utf-8');
		?>
		
	<div class="identification">
		<form id="identification" method="post" action="/identification.php" onsubmit="return identification();">
			<input type="hidden" name="key" id="key" value="<?php echo htmlentities($_SESSION['key'], ENT_COMPAT, 'UTF-8'); ?>"/>
			<input type="hidden" name="cipher" id="cipher" value=""/>
			<span class="input"><input type="text" name="identifiant" id="identifiant" value="" placeholder="Adresse email"/></span>
			<span class="input"><input type="password" name="password" id="password" value="" placeholder="Mot de passe"/></span>
			<span class="input"><input type="submit" name="action" value="Envoyer"></span>
		</form>
	</div>
	<div class="ligne"></div>
	<div class="socialbuttons">
	<?php require_once('include/Configuration.php'); ?>
	<a href="http://caveblachon.fr/administration/identificationGoogle.php" class="signInGoogle"><img class="googleplus" src = "/Images/Red-signin_g+.png" /></a>
	<br>
	<a href="http://caveblachon.fr/administration/identificationTwitter.php?authenticate=1" class="twitterlogo"></a>
	<br>
	<a  href="http://caveblachon.fr/administration/identificationFacebook.php"  class="facebooklogo"></a>
	</div>
	<?php
		if (!isset($_SERVER['HTTP_USER_AGENT']) || (preg_match('/Chrome\/[1-9][0-9]+|Version\/(?:5\.[1-9]|6).* Safari\/|Firefox\/[1-9][0-9]+|Opera\/.* Version\/(?:11\.6|1[2-9]\.)/', $_SERVER['HTTP_USER_AGENT']) == 0))
		//|MSIE (?:9|[1-9][0-9])
		{
		?>
			<aside id="avertissementNavigateurs">
				Ce site est optimisé pour fonctionner avec les navigateurs<br>
				<a href="http://www.google.fr/chrome">Chrome 15+</a>,
				<a href="http://www.apple.com/fr/safari/">Safari 5.1+</a> ou
				<a href="http://www.mozilla.org/fr/firefox/">Firefox 10+</a>
				<!--<a href="http://windows.microsoft.com/fr-fr/internet-explorer/products/ie/home">Internet Explorer 9+</a>-->
			</aside>
		<?php
		}
	?>
</article>