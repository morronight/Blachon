<?php
	require_once 'include/camsii/Charte.php';
	require_once 'include/camsii/CSS.php';
	require_once 'include/camsii/Credit.php';
	
	function afficheListeCharte($chartes = null)
	{
		if ($chartes === null)
			$chartes = Charte::GetListe();
		if ($chartes !== null)
		{	
			echo '<div id="listeChartes" class="listeChartes">';
			echo '<div class="charte">';
			echo '<span>Id charte - </span>';
			echo '<span>Nom charte</span>';
			echo '</div>';
			if (count($chartes) > 0)
			{
				foreach ($chartes as $charte)
				{
					echo '<div class="charte" id="charte_'.intval($charte->id).'">';
					echo '<span>'.htmlentities($charte->id, ENT_NOQUOTES, 'UTF-8').' - </span>';
					echo '<span>'.htmlentities($charte->nom, ENT_NOQUOTES, 'UTF-8').'</span>';
					echo '<span class="commandes" style="float:none">';
					echo '<span class="icone edit" title="Dupliquer" onclick="document.getElementById(\'dupliquer_'.$charte->id.'\').style.display=\'block\';"></span>';
					echo '<span class="icone delete" title="Supprimer" onclick="return adminCharte_supprimerCharte('.intval($charte->id).');"></span>';
					echo '</span>';
					echo '<div id="dupliquer_'.$charte->id.'" style="display:none;">';
					echo '<input type="text" id="nomNouvelleCharte_'.$charte->id.'" placeholder="entrer le nom de la nouvelle charte">';
					echo '<input type="submit" onclick="return adminCharte_dupliquerCharte('.intval($charte->id).')">';
					echo '</div>';
					$css = CSS::GetListe($charte->id);
					echo '<ul>';
					foreach($css as $cs)
					{
						echo '<li>'.htmlentities($cs->fichier, ENT_NOQUOTES, 'UTF-8');
						if(isset($cs->user_agent)) 
							echo ' ('.$cs->user_agent.')';
						echo '<input type="text" value="'.$cs->ordre.'" onchange="adminCharte_changerOrdreCss('.intval($charte->id).',\''.$cs->fichier.'\', this.value);" />';
						echo '<span class="commandes" style="float:none">';
						echo '<span class="icone delete" title="Supprimer" onclick="return adminCharte_supprimerCss('.intval($charte->id).',\''.htmlentities($cs->fichier, ENT_NOQUOTES, 'UTF-8').'\');"></span>';
						echo '</span>';
						echo '</li>';
						
					}
					echo '<li id="newCss_'.intval($charte->id).'" style="display:none" >';
					$cssLibelles = CSS::GetListeFichiers();
					echo '<select id="newCssNomFichier_'.intval($charte->id).'"/>';
					foreach($cssLibelles as $libelles)
					{
						echo '<option value="'.$libelles->fichier.'">'.$libelles->fichier.'</option>';
					}
					
					echo '</select>';
					$listeUserAgent = array('','/Firefox\/3','MSIE 6','MSIE 7','MSIE 8/');
					echo '<select id="user_agent_'.intval($charte->id).'">';
					for($i = 0; $i < count($listeUserAgent); $i++)
						echo '<option value="'.$listeUserAgent[$i].'">'.$listeUserAgent[$i].'</option>';
					echo '</select>';
					echo '<input type="submit" onclick="adminCharte_validerCss('.intval($charte->id).','.intval(count($css)).');" />';
					echo '</li>';
					echo '<span class="commandes" style="float:none">';
					echo '<span class="icone edit" title="Ajouter un CSS" onclick="return adminCharte_ajouterCss('.intval($charte->id).');"></span>';
					echo '</span>';
					echo '</ul>';
					
					$credits = Credit::GetListe($charte->id);
					/*echo '<ul>';
					foreach($credits as $credit)
						echo '<li>'.htmlentities($credit->fichier, ENT_NOQUOTES, 'UTF-8').'</li>';
					echo '</ul>';*/
					echo '</div>';
				}
			}
			else
				echo 'Aucune charte trouvée';
			echo '</div>';
		}
	}