<script type="text/javascript" src="/Scripts/sha256.js"></script>
<script type="text/javascript" src="/Scripts/identification.js"></script>
<article id="contenu" class="article">
	<?php
		if (isset($request) && isset($request['erreur']))
			$erreur = $request['erreur'];
		include 'template/contenu/changeMotDePasseContenu.php';

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
