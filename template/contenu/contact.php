<article id="accueilContenu">
	<section id="contact">
<?php
	include('template/template_general.php');
	formulaireContact();
?> 
	</section>
</article>
<?php
	if (Configuration::$Css['compact'] !== true)
		echo '<script type="text/javascript" src="/Scripts/accueil.js"></script>';
	else
	{
		echo '<script type="text/javascript">';
		ob_start();
		include 'Scripts/accueil.js';
		$js = ob_get_contents();
		ob_end_clean();
		echo preg_replace('/\s+/m', ' ', $js);
		echo '</script>';
	}
?>
