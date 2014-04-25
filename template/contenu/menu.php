<?php

	function ajouterMenu($position,$sections)
	{
		ob_start();
		if($position == "droit")
			echo '<nav id="menuDroite">';
		if($position == "gauche")
			echo '<nav id="menuGauche">';
		if($position == "haut")
			echo '<nav id="menuHaut">';
		if($position == "bas")
			echo '<nav id="menuBas">';
		for($i=0; $i < count($sections); $i++)
		{
			echo '<section id="'.$sections[$i].'">';
			echo '</section>';
		}
		echo '</nav>';
		
		$rslt = ob_get_contents();
		ob_end_clean();
		return $rslt;
	}
	
	function menuPrincipal($mainMenu)
	{
		foreach($mainMenu as $titre => $sousMenu)
		{
			if (is_array($sousMenu))
			{
				echo '<nav class="mainSubMenu">';
				if (isset($sousMenu[0]))
					echo '<a href="'.htmlentities($sousMenu[0], ENT_COMPAT, 'UTF-8').'">'.htmlentities($titre, ENT_COMPAT, 'UTF-8').'</a>';
				else
					echo '<a href="#">'.htmlentities($titre, ENT_COMPAT, 'UTF-8').'</a>';
				echo '<div>';
				foreach ($sousMenu as $libelle => $lien)
				{
					if ($libelle != '0')
						echo '<a href="'.htmlentities($lien, ENT_COMPAT, 'UTF-8').'">'.htmlentities($libelle, ENT_COMPAT, 'UTF-8').'</a>';
				}
				echo '</div></nav>';
			}
			else
				echo '<a href="'.htmlentities($sousMenu, ENT_COMPAT, 'UTF-8').'">'.htmlentities($titre, ENT_COMPAT, 'UTF-8').'</a>';
		}
	}