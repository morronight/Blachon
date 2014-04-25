<script type="text/javascript">
function documents_switchSmartListe(listeId)
{
	var liste = document.getElementById(listeId);
	
	if (liste)
	{
		if (liste.className == 'smartListeDocuments')
			liste.className = 'smartListeDocumentsOpened';
		else
			liste.className = 'smartListeDocuments';
	}
}

function documents_trackDocument(url, titre)
{
	var	i;

	try
	{
		if (_gaq && _pageTrackers && url)
		{
			for (i = 0; i < _pageTrackers.length; i++)
			{
				if (_pageTrackers[i] != "")
					_gaq.push([_pageTrackers[i] + "._trackEvent", "Document", "Téléchargement", titre]);
				else
					_gaq.push(["_trackEvent", "Document", "Téléchargement", titre]);
			}
		}
	} catch(err) {}
}</script>
<?php
	require_once('include/camsii/Categorie.php');
	require_once('include/camsii/Document.php');
	$categories = Categorie::GetListe();
	echo '<article id="contenu" class="documents">';
	echo '<h1>Espace documentaire</h1>';
	
	if (($categories !== false) && (count($categories) > 0))
	{ 
		foreach($categories as $categorie)
		{
			$documents = Document::Search($categorie->id, false, false, null, null, null, false);
			$countDoc = 0;
			foreach($documents as $document)
			{
				if(is_file(''.Configuration::$Documents['location'].$document->path) == true.'')
				{
					$countDoc += 1;
				}
			}
			if ($countDoc > 1)
				$nombre = ' ('.$countDoc.' documents)';
			else
				$nombre = ' ('.$countDoc.' document)';
				if($countDoc > 0)
				{
					echo '<h2 class="categoriesH2" onclick="documents_switchSmartListe(\'smartListeDocument'.intval($categorie->id).'\')">'.htmlentities($categorie->nom, ENT_NOQUOTES, 'UTF-8').'<span class="nombreDocs">'.$nombre.'</span></h2>'.PHP_EOL;
					echo '<div class="smartListeDocuments" id="smartListeDocument'.intval($categorie->id).'">'.PHP_EOL;
					echo '<table class="listeDocuments" summary="'.htmlentities($categorie->nom, ENT_QUOTES, 'UTF-8').'">'.PHP_EOL;
				}
			if ($countDoc > 0)
			{
				foreach($documents as $document)
				{	
					if(is_file(''.Configuration::$Documents['location'].$document->path) == true.'')
					{
						echo '<tr class="ligne">';
						echo '<td class="iconePdf"></td>';
						$alt = 'Mis en ligne le '.$document->GetDatePublication().'.';
						if ($document->description !== null)
							$alt .= htmlentities($document->description, ENT_COMPAT, 'UTF-8');
						if (($document->description === null) || ($document->description == ''))
							echo '<td class="tdDocs"><a class="lienDoc" href="/documents/'.$document->GetLien().'" onclick="if (_gaq) _gaq.push([\'_trackEvent\', \'Téléchargement\', \''.str_replace(array('"', '\''), array('\\"', '\\\''), $document->nom).'\', \'/documents/'.$document->GetLien().'\']);" title="'.$alt.'" target="'.$document->nom.'">'.htmlentities($document->nom, ENT_NOQUOTES, 'UTF-8').'</a>';
						else 
							echo '<td class="tdDocs"><a class="lienDoc" href="/documents/'.$document->GetLien().'" onclick="if (_gaq) _gaq.push([\'_trackEvent\', \'Téléchargement\', \''.str_replace(array('"', '\''), array('\\"', '\\\''), $document->nom).'\', \'/documents/'.$document->GetLien().'\']);" title="'.$alt.'" target="'.$document->nom.'">'.htmlentities($document->nom, ENT_NOQUOTES, 'UTF-8').' '.$document->GetDescription().'</a>';
						echo '<span> - '.$document->GetReadeableSize().' - '.$document->GetDatePublication().'</span></td>';
						echo '</tr>'.PHP_EOL;
					}
				}
			echo '</table>'.PHP_EOL;
			echo '</div>'.PHP_EOL;
			}
		}
	}
	echo '</article>';
?>