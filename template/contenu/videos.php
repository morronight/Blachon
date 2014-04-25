<article id="contenu" class="videos">
	<div id="breadcrumb">
		<a href="/">Accueil</a> &gt; Liste des videos
	</div>
	<?php
		include("template/template_video.php");
		if (!isset($theVideos))
			$theVideos = afficheVideos();
		else
			afficheVideos($theVideos);
	?>
</article>