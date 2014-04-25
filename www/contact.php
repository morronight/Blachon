<?php
	function Rec($text)
	{
		$text = htmlspecialchars(trim($text), ENT_QUOTES);
		if (1 === get_magic_quotes_gpc())
		{
			$text = stripslashes($text);
		}
	 
		$text = nl2br($text);
		return $text;
	};
	 

	function IsEmail($email)
	{
		$value = preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);
		return (($value === 0) || ($value === false)) ? false : true;
	}
 
	$destinataire = 'contact@caveblachon.fr';

	$copie = 'oui';
	$message_envoye = "Votre message nous est bien parvenu !";
	$message_non_envoye = "L'envoi du mail a échoué, veuillez réessayer SVP.";
	$message_formulaire_invalide = "Vérifiez que tous les champs soient bien remplis et que l'email soit sans erreur.";
	$nomprenom     = (isset($_POST['nomprenom']))     ? Rec($_POST['nomprenom'])     : '';
	$email   = (isset($_POST['email']))   ? Rec($_POST['email'])   : '';
	$telephone   = (isset($_POST['telephone']))   ? Rec($_POST['telephone'])   : '';
	$objet   = (isset($_POST['objet']))   ? Rec($_POST['objet'])   : '';
	$message = (isset($_POST['message'])) ? Rec($_POST['message']) : '';
	 
	$email = (IsEmail($email)) ? $email : ''; 
	$err_formulaire = false; 
	 
	if (isset($_POST['envoi']))
	{
		if (($nomprenom != '') && ($objet != ''))
		{
			if ($email != '') 
			{
				$headers  = 'From:'.utf8_decode($nomprenom).' <'.$email.'>' . "\r\n";
			}
			else
				$headers  = 'From:Site Internet Cave Blachon <contact@caveblachon.fr>' . "\r\n";
			//$headers .= 'Reply-To: '.$email. "\r\n" ;
			//$headers .= 'X-Mailer:PHP/'.phpversion();
	 
			if ($copie == 'oui')
			{
				$cible = $destinataire.','.$email;
			}
			else
			{
				$cible = $destinataire;
			};
			$message = str_replace("&#039;","'",$message);
			$message = str_replace("&#8217;","'",$message);
			$message = str_replace("&quot;",'"',$message);
			$message = str_replace('&lt;br&gt;','',$message);
			$message = str_replace('&lt;br /&gt;','',$message);
			$message = str_replace("&lt;","&lt;",$message);
			$message = str_replace("&gt;","&gt;",$message);
			$message = str_replace("&amp;","&",$message);
	 
			if (mail($cible, $objet, $message, $headers))
			{
				?><script type="text/javascript">
				alert("Votre message nous est bien parvenu");
				location = "index.php";
				</script>
				<?php
			}
			else
			{
				?><script type="text/javascript">
				alert("L\'envoi du mail a échoué, veuillez réessayer SVP");
				location = "index.php";
				</script>
				<?php
			};
		}
		else
		{
			?><script type="text/javascript">
				alert("Vérifiez que tous les champs soient bien remplis et que l\'email soit sans erreur");
				location = "index.php";
				</script>
				<?php	
			$err_formulaire = true;
		};
	}; 
?>