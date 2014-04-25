<?php

function afficheDocumentsAdmin($documents = null, $idCategories = null, $brouillons = false, $archives = false, $filtre = null)
	{
		require_once 'include/camsii/Document.php';
		if ($documents === null)
			$documents = Document::Search($idCategories, $brouillons, $archives, $filtre);
		if ($documents !== null)
		{
			echo '<div id="liste" class="liste">';
			if (count($documents) > 0)
			{
				foreach ($documents as $document)
				{
					if (is_a($document, 'Document'))
					{
						echo '<div class="listeDocuments">';
						$titre = htmlentities($document->nom, ENT_NOQUOTES, 'UTF-8').strtolower(substr($document->path, -4));
						echo '<h2>'.$titre.' <a target="_blank" href="/documents/'.$document->GetLien().'"><span>(Télécharger '.$document->GetReadeableSize().')</span></a></h2>';
						if ($document->description !== null)
							echo '<p><a href="/administration/document?document='.intval($document->id).'">'.$document->GetDescription().'</a></p>';
						else
							echo '<p><a href="/administration/document?document='.intval($document->id).'"><i>Modifier</i></a></p>';
						echo '</div>';
					}
				}
			}
			else
			{
				if ($idCategories === null)
					echo '<span class="message">Aucun document trouvé. Sélectionnez une catégorie.</span>';
				else
					echo '<span class="message">Aucun document trouvé.</span>';
			}
			echo '</div>';
		}
	}
?>