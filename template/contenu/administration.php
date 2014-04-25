<link href="/Css/Administration.css" rel="stylesheet" type="text/css">
<article id="contenu" class="administration">
	<h1><span class="icone adminHome" title="Accueil administration"></span> Administration</h1>
	<section>Vous êtes sur la page d'administration du site de la cave Blachon</section>
	<section id="menuAdmin">
		<fieldset>
			<legend>Articles</legend>
			<a href="/administration/article">Créer un article</a><br/>
			<a href="/administration/articles">Modifier un article</a><br/>
		</fieldset>
		<fieldset>
			<legend>Images / Photos</legend>
			<a href="/administration/image">Envoyer une image/photo</a><br/>
			<a href="/administration/images">Modifier une image/photo</a><br/>
		</fieldset>
		<fieldset>
			<legend>Vidéos</legend>
			<a href="/administration/video">Ajouter une vidéo</a><br/>
			<a href="/administration/videos">Modifier une vidéo</a><br/>
		</fieldset>
		<fieldset>
			<legend>Galeries</legend>
			<a href="/administration/galerie">Créer une galerie</a><br/>
			<a href="/administration/galeries">Modifier une galerie</a><br/>
		</fieldset>
		<fieldset>
			<legend>Documents</legend>
			<a href="/administration/document">Envoyer un document</a><br/>
			<a href="/administration/documents">Modifier un document</a><br/>
		</fieldset>
		<!--fieldset>
			<legend>Page d'accueil</legend>
			<!--a href="/administration/bandeau">Modifier le message défilant</a><br/>
			<a href="/administration/photosAccueil">Modifier les photos de la page d'accueil</a><br/>
		</fieldset!-->
		<fieldset>
			<legend>Utilisateurs</legend>
			<a href="/administration/utilisateurs">Modifier les administrateurs du site Internet</a><br/>
		</fieldset>
		<?php if(isset($_SESSION['utilisateur']) && $_SESSION['habilitation'] == 2){?>
		<fieldset>
			<legend>Chartes</legend>
			<a href="/administration/chartes">Modifier les chartes du site Internet</a><br/>
		</fieldset>
		<?php } if(isset($_SESSION['fb_141680632674080_access_token'])){ ?>
		<fieldset>
			<legend>Deconnexion de Facebook</legend>
			<a href="/administration/identificationFacebook?action=deconnexion">Se déconnecter</a><br/>
		</fieldset>
		<?php } ?>
		<?php if(isset($_SESSION['google_access_token'])){ ?>
		<fieldset>
			<legend>Deconnexion de Google</legend>
			<a href="/administration/identificationGoogle?action=deconnexion">Se déconnecter</a><br/>
		</fieldset>
		<?php } ?>
		<?php if(isset($_SESSION['twitter_access_token'])){ ?>
		<fieldset>
			<legend>Deconnexion de Twitter</legend>
			<a href="/administration/identificationTwitter?action=deconnexion">Se déconnecter</a><br/>
		</fieldset>
		<?php } ?>
	</section>
</article>